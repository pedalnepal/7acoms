<?php

namespace App\Services\Cybersource;

use Illuminate\Support\Facades\Log;

/**
 * The Unified Checkout integration.
 *
 * Every payment starts the same way:
 *
 *  1. createCaptureContext() calls the Sessions API and returns the signed JWT
 *     that configures the browser SDK, along with the SDK script URL and its
 *     subresource-integrity hash pulled out of that JWT.
 *  2. The browser collects the payment details and hands back a transient
 *     token — a short-lived (15 minute) reference, never the card data.
 *
 * How the transaction is then run depends on the complete mandate:
 *
 *  - With it (the default), the capture context asks the gateway to run 3-D
 *    Secure and the sale itself. The SDK's complete() resolves with a signed
 *    result, which readTransactionResult() verifies and reads.
 *  - Without it, authorize() charges the transient token through the Payments
 *    API — no payer authentication, but the amount is stated server-side.
 *
 * Either way the amount comes from this application: under the mandate it is
 * the one baked into the capture context, which the browser cannot alter.
 */
class UnifiedCheckoutService
{
    /**
     * Gateway statuses that mean the payment was taken. AUTHORIZED settles
     * outright; PENDING and AUTHORIZED_PENDING_REVIEW are accepted but not yet
     * final, and must not be charged a second time.
     */
    public const ACCEPTED_STATUSES = ['AUTHORIZED', 'PENDING', 'AUTHORIZED_PENDING_REVIEW'];

    public function __construct(
        private CybersourceClient $client,
        private JwtVerifier $verifier
    ) {
    }

    public function isConfigured(): bool
    {
        return $this->client->isConfigured();
    }

    /**
     * Whether the gateway, rather than this application, runs the transaction.
     *
     * The mandate only runs when a type is configured, so that single setting
     * decides which of the two flows the checkout page and the process()
     * endpoint follow.
     */
    public function usesCompleteMandate(): bool
    {
        return (bool) config('cybersource.complete_mandate_type');
    }

    /**
     * Create a capture context for one payment attempt.
     *
     * @param  array{reference: string, amount: string|float, currency: string, bill_to?: array<string, string|null>}  $order
     * @return array{jwt: string, client_library: string, client_library_integrity: ?string, complete_mandate: bool}
     *
     * @throws CybersourceException
     */
    public function createCaptureContext(array $order): array
    {
        $response = $this->client->post('/uc/v1/sessions', $this->captureContextPayload($order));

        // The Sessions API answers with the capture context JWT as the raw body.
        $jwt = trim($response->body());

        if (! $response->successful() || ! $this->looksLikeJwt($jwt)) {
            throw CybersourceException::withContext(
                'The payment gateway did not return a valid checkout session.',
                [
                    'status'  => $response->status(),
                    'reason'  => $this->reasonFrom($response->body()),
                    'details' => $this->detailsFrom($response->body()),
                ]
            );
        }

        $claims        = $this->decodeJwtPayload($jwt);
        $clientLibrary = $this->findClaim($claims, 'clientLibrary');

        if (! $clientLibrary) {
            throw CybersourceException::withContext(
                'The checkout session did not include a client library URL.',
                ['claims' => array_keys($claims)]
            );
        }

        return [
            'jwt'                      => $jwt,
            'client_library'           => $clientLibrary,
            'client_library_integrity' => $this->findClaim($claims, 'clientLibraryIntegrity'),
            // The page has to know which flow it is driving before it mounts.
            'complete_mandate'         => $this->usesCompleteMandate(),
        ];
    }

    /**
     * Charge a transient token.
     *
     * Fields sent here take precedence over the same fields inside the token,
     * so the amount charged is the one calculated server-side.
     *
     * @param  array{reference: string, amount: string|float, currency: string, bill_to?: array<string, string|null>}  $order
     * @return array{status: string, transaction_id: ?string, reason: ?string, message: ?string, approved: bool, raw: array<string, mixed>}
     *
     * @throws CybersourceException
     */
    public function authorize(string $transientTokenJwt, array $order): array
    {
        $payload = [
            'clientReferenceInformation' => [
                'code' => $order['reference'],
            ],
            'processingInformation' => [
                'commerceIndicator' => 'internet',
                'capture'           => (bool) config('cybersource.capture', true),
            ],
            'orderInformation' => [
                'amountDetails' => [
                    'totalAmount' => $this->formatAmount($order['amount']),
                    'currency'    => $order['currency'],
                ],
            ],
            'tokenInformation' => [
                'transientTokenJwt' => $transientTokenJwt,
            ],
        ];

        if ($billTo = $this->cleanBillTo($order)) {
            $payload['orderInformation']['billTo'] = $billTo;
        }

        $response = $this->client->post('/pts/v2/payments', $payload);

        $body     = $response->json() ?? [];
        $status   = (string) ($body['status'] ?? ($response->successful() ? 'UNKNOWN' : 'ERROR'));
        $approved = $response->successful() && in_array($status, self::ACCEPTED_STATUSES, true);

        // The HTTP status and raw body are what actually explain a failure that
        // isn't a clean gateway decline (an auth/permission/routing problem at
        // Cybersource looks identical to a decline otherwise — same ERROR status,
        // no reason, no message). Keep both so that's diagnosable from the stored
        // transaction alone, without having to replay the request by hand.
        if (! $approved) {
            Log::warning('Unified Checkout authorization not approved', [
                'reference'   => $order['reference'],
                'http_status' => $response->status(),
                'status'      => $status,
                'body'        => $response->body() !== '' ? $response->body() : '(empty body)',
            ]);
        }

        return [
            'status'         => $status,
            'transaction_id' => $body['id'] ?? null,
            'reason'         => $body['reason'] ?? null,
            'message'        => $body['message'] ?? ($body['errorInformation']['message'] ?? null),
            'approved'       => $approved,
            'raw'            => $body + ['http_status' => $response->status()],
        ];
    }

    /**
     * Read the outcome of a transaction the complete mandate ran.
     *
     * The result decides whether a registration counts as paid, so its
     * signature is checked before a single claim is read. Where the payment
     * response sits inside the token depends on which services the mandate
     * ran, so the claims are searched rather than walked down a fixed path —
     * the same reason findClaim() exists for the capture context.
     *
     * @return array{status: string, transaction_id: ?string, reason: ?string, message: ?string, approved: bool, authenticated: bool, reference: ?string, raw: array<string, mixed>}
     *
     * @throws CybersourceException
     */
    public function readTransactionResult(string $jwt): array
    {
        $claims  = $this->verifier->verify($jwt);
        $payment = $this->findNodeWith($claims, 'status') ?? [];

        $status   = (string) ($payment['status'] ?? 'UNKNOWN');
        $approved = in_array($status, self::ACCEPTED_STATUSES, true);

        if (! $approved) {
            Log::warning('Unified Checkout complete mandate not approved', [
                'status' => $status,
                'reason' => $payment['reason'] ?? null,
                'claims' => array_keys($claims),
            ]);
        }

        return [
            'status'         => $status,
            'transaction_id' => $this->stringOrNull($payment['id'] ?? null)
                ?? $this->findClaim($claims, 'transactionId'),
            'reason'         => $this->stringOrNull($payment['reason'] ?? null),
            'message'        => $this->stringOrNull($payment['message'] ?? null)
                ?? $this->stringOrNull($payment['errorInformation']['message'] ?? null),
            'approved'       => $approved,
            'authenticated'  => $this->wasAuthenticated($claims),
            // Named explicitly rather than searched for: 'code' is a common
            // key, and matching the wrong one would reject a real payment.
            'reference'      => $this->stringOrNull(
                $this->findArrayClaim($claims, 'clientReferenceInformation')['code'] ?? null
            ),
            'raw'            => $claims,
        ];
    }

    /**
     * Whether payer authentication actually happened.
     *
     * A CAVV is the issuer's proof that the cardholder was authenticated; a
     * transaction status of Y (authenticated) or A (attempted, liability still
     * shifts) says the same thing where no CAVV is surfaced. Anything else —
     * including a mandate configured for NONE — counts as unauthenticated.
     *
     * @param  array<string, mixed>  $claims
     */
    private function wasAuthenticated(array $claims): bool
    {
        $authentication = $this->findArrayClaim($claims, 'consumerAuthenticationInformation')
            ?? $this->findNodeWith($claims, 'transactionStatus')
            ?? [];

        return ! empty($authentication['cavv'])
            || in_array(
                strtoupper((string) ($authentication['transactionStatus'] ?? '')),
                ['Y', 'A'],
                true
            );
    }

    /**
     * Non-sensitive details behind a transient token — cardholder name, billing
     * and shipping address. Returns null rather than failing the payment, since
     * this only enriches the stored record.
     */
    public function paymentDetails(string $jti): ?array
    {
        try {
            $response = $this->client->get('/flex/v2/payment-details/' . urlencode($jti));

            return $response->successful() ? $response->json() : null;
        } catch (\Throwable $e) {
            Log::warning('Cybersource payment details lookup failed: ' . $e->getMessage());

            return null;
        }
    }

    /**
     * Read the claims of a transient token without a network call. The token
     * carries the masked card number, BIN, card type and payment method.
     *
     * @return array<string, mixed>
     */
    public function readTransientToken(string $jwt): array
    {
        $claims = $this->decodeJwtPayload($jwt);
        $card   = $claims['content']['paymentInformation']['card'] ?? [];

        return [
            'jti'          => $claims['jti'] ?? null,
            'payment_type' => $claims['metadata']['paymentType'] ?? null,
            'card_type'    => $card['type']['value'] ?? null,
            'card_masked'  => $card['number']['maskedValue'] ?? null,
            'card_bin'     => $card['number']['bin'] ?? null,
            'expiry'       => isset($card['expirationMonth']['value'], $card['expirationYear']['value'])
                ? $card['expirationMonth']['value'] . '/' . $card['expirationYear']['value']
                : null,
            'authenticated' => filter_var(
                $claims['metadata']['cardholderAuthenticationStatus'] ?? false,
                FILTER_VALIDATE_BOOLEAN
            ),
        ];
    }

    /**
     * @param  array<string, mixed>  $order
     * @return array<string, mixed>
     */
    private function captureContextPayload(array $order): array
    {
        $payload = [
            'targetOrigins'       => $this->targetOrigins(),
            'country'             => config('cybersource.country'),
            'locale'              => config('cybersource.locale'),
            'allowedPaymentTypes' => config('cybersource.allowed_payment_types'),
            'allowedCardNetworks' => config('cybersource.allowed_card_networks'),
            'buttonType'          => config('cybersource.button_type'),
            'captureMandate'      => [
                'billingType'              => 'FULL',
                'requestEmail'             => true,
                'requestPhone'             => true,
                'requestShipping'          => false,
                'showAcceptedNetworkIcons' => true,
                'showConfirmationStep'     => true,
            ],
            'data' => [
                'orderInformation' => [
                    'amountDetails' => [
                        'totalAmount' => $this->formatAmount($order['amount']),
                        'currency'    => $order['currency'],
                    ],
                ],
                'clientReferenceInformation' => [
                    'code' => $order['reference'],
                ],
            ],
        ];

        if ($version = config('cybersource.client_version')) {
            $payload['clientVersion'] = $version;
        }

        // Present only when a mandate type is configured; without it the
        // gateway returns a transient token and nothing else, and this
        // application authorises the payment itself.
        if ($this->usesCompleteMandate()) {
            $payload['completeMandate'] = array_filter([
                'type'                   => config('cybersource.complete_mandate_type'),
                'consumerAuthentication' => config('cybersource.consumer_authentication'),
            ]);
        }

        // Prefilling what we already know saves the delegate re-typing it.
        if ($billTo = $this->cleanBillTo($order)) {
            $payload['data']['orderInformation']['billTo'] = $billTo;
        }

        return $payload;
    }

    /**
     * @param  array<string, mixed>  $order
     * @return array<string, string>
     */
    private function cleanBillTo(array $order): array
    {
        $billTo = $order['bill_to'] ?? [];

        return array_filter($billTo, static fn ($value) => $value !== null && $value !== '');
    }

    /**
     * Every origin that may host the SDK.
     *
     * The gateway rejects the whole session unless each origin uses https and
     * names a fully qualified domain — "localhost" and bare http are refused.
     * Checking here turns an opaque gateway validation error into a message
     * that says what to fix.
     *
     * @return array<int, string>
     *
     * @throws CybersourceException
     */
    private function targetOrigins(): array
    {
        $origins = config('cybersource.target_origins') ?: [];

        if (! $origins) {
            $url = parse_url((string) config('app.url'));

            if (! empty($url['host'])) {
                $origin = ($url['scheme'] ?? 'https') . '://' . $url['host'];

                if (! empty($url['port'])) {
                    $origin .= ':' . $url['port'];
                }

                $origins = [$origin];
            }
        }

        $origins = array_values(array_unique($origins));

        if (! $origins) {
            throw new CybersourceException(
                'No checkout origin is configured. Set CYBS_TARGET_ORIGINS to the https origin that serves the payment page.'
            );
        }

        foreach ($origins as $origin) {
            if (($invalid = $this->originProblem($origin)) !== null) {
                throw CybersourceException::withContext(
                    "Invalid checkout origin [{$origin}]: {$invalid} Set CYBS_TARGET_ORIGINS to an https origin with a full domain name, for example https://acoms2027.org.np.",
                    ['origin' => $origin]
                );
            }
        }

        return $origins;
    }

    /**
     * Why the gateway would refuse this origin, or null when it is acceptable.
     */
    private function originProblem(string $origin): ?string
    {
        $parts = parse_url($origin);

        if (! $parts || empty($parts['host'])) {
            return 'it is not a valid URL.';
        }

        if (($parts['scheme'] ?? '') !== 'https') {
            return 'origins must use the https protocol.';
        }

        if (! str_contains($parts['host'], '.') || filter_var($parts['host'], FILTER_VALIDATE_IP)) {
            return 'the host must be a fully qualified domain name with at least one dot (localhost and IP addresses are refused).';
        }

        return null;
    }

    private function formatAmount(string|float $amount): string
    {
        return number_format((float) $amount, 2, '.', '');
    }

    private function looksLikeJwt(string $value): bool
    {
        return substr_count($value, '.') === 2 && str_starts_with($value, 'ey');
    }

    /**
     * @return array<string, mixed>
     */
    private function decodeJwtPayload(string $jwt): array
    {
        $parts = explode('.', $jwt);

        if (count($parts) < 2) {
            return [];
        }

        $json = base64_decode(strtr($parts[1], '-_', '+/'), false);

        return is_string($json) ? (json_decode($json, true) ?: []) : [];
    }

    /**
     * Find a claim anywhere in the capture context.
     *
     * Cybersource asks integrators to parse responses dynamically because the
     * structure may gain fields, so this searches the decoded claims rather
     * than assuming a fixed path such as ctx[0].data.clientLibrary.
     *
     * @param  array<string, mixed>  $claims
     */
    private function findClaim(array $claims, string $key): ?string
    {
        if (isset($claims[$key]) && is_string($claims[$key])) {
            return $claims[$key];
        }

        foreach ($claims as $value) {
            if (is_array($value) && ($found = $this->findClaim($value, $key)) !== null) {
                return $found;
            }
        }

        return null;
    }

    /**
     * The innermost object that carries $key as a string value.
     *
     * Returning the whole node rather than the value keeps its siblings
     * available, so a status can be read together with the id and reason that
     * belong to the same response and not to some unrelated nested object.
     *
     * @param  array<string, mixed>  $claims
     * @return array<string, mixed>|null
     */
    private function findNodeWith(array $claims, string $key): ?array
    {
        if (isset($claims[$key]) && is_string($claims[$key])) {
            return $claims;
        }

        foreach ($claims as $value) {
            if (is_array($value) && ($found = $this->findNodeWith($value, $key)) !== null) {
                return $found;
            }
        }

        return null;
    }

    /**
     * Find a nested object by name anywhere in the claims.
     *
     * @param  array<string, mixed>  $claims
     * @return array<string, mixed>|null
     */
    private function findArrayClaim(array $claims, string $key): ?array
    {
        if (isset($claims[$key]) && is_array($claims[$key])) {
            return $claims[$key];
        }

        foreach ($claims as $value) {
            if (is_array($value) && ($found = $this->findArrayClaim($value, $key)) !== null) {
                return $found;
            }
        }

        return null;
    }

    private function stringOrNull(mixed $value): ?string
    {
        return is_string($value) || is_int($value) ? (string) $value : null;
    }

    /**
     * Pull the reason value out of an error response body, for logging.
     *
     * The gateway is not consistent about where it puts this. Validation and
     * profile errors use a top-level `reason` or `message`, but an
     * authentication rejection answers {"response":{"rmsg":"..."}} instead —
     * so that nested field has to be read too, or a 401 logs no reason at all.
     */
    private function reasonFrom(string $body): ?string
    {
        $decoded = json_decode($body, true);

        if (! is_array($decoded)) {
            return null;
        }

        return $decoded['reason']
            ?? $decoded['message']
            ?? $decoded['response']['rmsg']
            ?? null;
    }

    /**
     * The per-field validation errors from a 400 response.
     *
     * A validation failure names the offending fields, which is the only thing
     * that makes the error actionable — so it must reach the log. Entries coded
     * UNKNOWN are dropped: they are "further unsupported condition due to
     * previous error" noise cascading from the real failure.
     *
     * @return array<int, string>
     */
    private function detailsFrom(string $body): array
    {
        $decoded = json_decode($body, true);

        if (! is_array($decoded)) {
            return [];
        }

        $errors = [];

        foreach ($decoded['errors'] ?? [] as $error) {
            if (! is_array($error) || ($error['code'] ?? null) === 'UNKNOWN') {
                continue;
            }

            $errors[] = trim(($error['location'] ?? '?') . ': ' . ($error['message'] ?? '?'));
        }

        // Other Cybersource endpoints report field errors as `details` instead.
        foreach ($decoded['details'] ?? [] as $detail) {
            if (is_array($detail)) {
                $errors[] = trim(($detail['field'] ?? '?') . ': ' . ($detail['reason'] ?? '?'));
            }
        }

        return $errors;
    }
}
