<?php

namespace App\Services\Cybersource;

use Illuminate\Support\Facades\Log;

/**
 * The Unified Checkout integration.
 *
 * Three server-side steps make up the flow:
 *
 *  1. createCaptureContext() calls the Sessions API and returns the signed JWT
 *     that configures the browser SDK, along with the SDK script URL and its
 *     subresource-integrity hash pulled out of that JWT.
 *  2. The browser collects the payment details and hands back a transient
 *     token — a short-lived (15 minute) reference, never the card data.
 *  3. authorize() charges that transient token through the Payments API, using
 *     the amount this application calculated rather than anything the browser
 *     supplied.
 */
class UnifiedCheckoutService
{
    /**
     * Gateway statuses that mean the payment was taken. AUTHORIZED settles
     * outright; PENDING and AUTHORIZED_PENDING_REVIEW are accepted but not yet
     * final, and must not be charged a second time.
     */
    public const ACCEPTED_STATUSES = ['AUTHORIZED', 'PENDING', 'AUTHORIZED_PENDING_REVIEW'];

    public function __construct(private CybersourceClient $client)
    {
    }

    public function isConfigured(): bool
    {
        return $this->client->isConfigured();
    }

    /**
     * Create a capture context for one payment attempt.
     *
     * @param  array{reference: string, amount: string|float, currency: string, bill_to?: array<string, string|null>}  $order
     * @return array{jwt: string, client_library: string, client_library_integrity: ?string}
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

        $body   = $response->json() ?? [];
        $status = (string) ($body['status'] ?? ($response->successful() ? 'UNKNOWN' : 'ERROR'));

        return [
            'status'         => $status,
            'transaction_id' => $body['id'] ?? null,
            'reason'         => $body['reason'] ?? null,
            'message'        => $body['message'] ?? ($body['errorInformation']['message'] ?? null),
            'approved'       => $response->successful() && in_array($status, self::ACCEPTED_STATUSES, true),
            'raw'            => $body,
        ];
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
