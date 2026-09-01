<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Mail\RegistrationPaymentReceipt;
use App\Models\PaymentTransaction;
use App\Models\Registration;
use App\Services\Cybersource\CybersourceException;
use App\Services\Cybersource\UnifiedCheckoutService;
use App\Services\Forex\CurrencyConverter;
use App\Services\Forex\ForexException;
use App\Services\RegistrationFeeCalculator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

/**
 * Card payment for a submitted registration, through Cybersource Unified
 * Checkout.
 *
 * The browser never states what it owes: this controller prices the
 * registration itself and bakes that price into the capture context, so the
 * amount taken is the one it calculated whichever way the transaction runs.
 *
 * By default the capture context carries a complete mandate, which puts 3-D
 * Secure in front of a sale the gateway performs itself; the browser returns a
 * signed result that is verified here. With the mandate switched off, the
 * transient token is charged from the server instead.
 *
 * International categories are priced in USD but the bank settles in NPR, so
 * the fee is converted at the NRB rate before it reaches the gateway. The rate
 * is fixed when the checkout page is built and reused when the token is
 * charged — re-converting between the two would charge an amount the capture
 * context was never created for.
 */
class PaymentController extends Controller
{
    public function __construct(
        private UnifiedCheckoutService $checkout,
        private RegistrationFeeCalculator $fees,
        private CurrencyConverter $converter
    ) {}

    /**
     * The checkout page. Creates a fresh capture context on every load, since
     * a capture context is good for one payment attempt only.
     */
    public function show(string $reference)
    {
        $registration = $this->findRegistration($reference);

        if ($registration->isPaymentSettledOrPending()) {
            return redirect()->route('registration.payment.complete', $registration->payment_reference);
        }

        // Re-price on every visit: the tier is set by the date payment is made,
        // so a delegate who returns after a deadline pays the current rate.
        try {
            $this->repriceRegistration($registration);
        } catch (ForexException $e) {
            Log::error('Registration payment could not be priced: ' . $e->getMessage(), $e->context);

            return view('front.page.payment', [
                'registration' => $registration,
                'session'      => null,
                'error'        => 'We cannot confirm the exchange rate for your registration fee at the moment. Please try again shortly, or contact the organising committee.',
            ] + $this->pageMeta());
        }

        if (! $this->checkout->isConfigured()) {
            Log::error('Registration payment attempted while Cybersource is not configured.');

            return view('front.page.payment', [
                'registration' => $registration,
                'session'      => null,
                'error'        => 'Online payment is temporarily unavailable. Please contact the organising committee to complete your registration.',
            ] + $this->pageMeta());
        }

        try {
            $session = $this->checkout->createCaptureContext($this->orderFor($registration));
        } catch (CybersourceException $e) {
            Log::error('Unified Checkout session failed: ' . $e->getMessage(), $e->context);

            return view('front.page.payment', [
                'registration' => $registration,
                'session'      => null,
                'error'        => 'We could not start the secure payment session. Please try again in a few moments.',
            ] + $this->pageMeta());
        }

        return view('front.page.payment', [
            'registration' => $registration,
            'session'      => $session,
            'error'        => null,
        ] + $this->pageMeta());
    }

    /**
     * Settle the payment attempt the SDK just finished.
     *
     * Called by the checkout page over AJAX; answers with JSON so the page can
     * show the gateway's message without losing the mounted checkout. Which of
     * the two flows applies is decided by the configuration, not by the
     * browser: a page that posts a result when no mandate is configured has it
     * ignored, and one that posts none when a mandate is configured is refused.
     */
    public function process(Request $request, string $reference): JsonResponse
    {
        $registration = $this->findRegistration($reference);

        $request->validate([
            'transient_token' => 'required|string',
            'payment_result'  => 'nullable|string',
        ]);

        if ($registration->isPaymentSettledOrPending()) {
            return response()->json([
                'success'  => true,
                'redirect' => route('registration.payment.complete', $registration->payment_reference),
            ]);
        }

        $order = $this->orderFor($registration);
        $token = $this->checkout->readTransientToken($request->input('transient_token'));

        return $this->checkout->usesCompleteMandate()
            ? $this->settleCompletedTransaction($request, $registration, $order, $token)
            : $this->authorizeTransientToken($request, $registration, $order, $token);
    }

    /**
     * Record a transaction the gateway ran under the complete mandate.
     *
     * Nothing is charged here — 3-D Secure and the sale already happened in the
     * browser's conversation with the gateway. What is left is to prove the
     * result is genuine and belongs to this registration, then act on it.
     *
     * @param  array<string, mixed>  $order
     * @param  array<string, mixed>  $token
     */
    private function settleCompletedTransaction(
        Request $request,
        Registration $registration,
        array $order,
        array $token
    ): JsonResponse {
        try {
            $jwt = trim((string) $request->input('payment_result'));

            if ($jwt === '') {
                throw new CybersourceException('The browser returned no payment result to verify.');
            }

            $result = $this->checkout->readTransactionResult($jwt);

            // The signature proves the gateway issued this result; the
            // reference proves it was issued for this registration, and not
            // replayed from another delegate's successful payment.
            if ($result['reference'] !== null && $result['reference'] !== $order['reference']) {
                throw CybersourceException::withContext(
                    'The payment result belongs to a different registration.',
                    ['expected' => $order['reference'], 'received' => $result['reference']]
                );
            }
        } catch (CybersourceException $e) {
            Log::error('Unified Checkout payment result could not be verified: ' . $e->getMessage(), $e->context);

            $this->recordTransaction($registration, $order, $token, [
                'status'         => 'ERROR',
                'transaction_id' => null,
                'reason'         => 'RESULT_UNVERIFIED',
                'message'        => $e->getMessage(),
                'approved'       => false,
                'raw'            => [],
            ]);

            $registration->payment_status = Registration::PAYMENT_FAILED;
            $registration->save();

            // Unlike a failed authorization, the card may well have been
            // charged here — the gateway ran the transaction. Sending the
            // delegate back to try again could take the money twice, so ask
            // them to check with the committee instead.
            return response()->json([
                'success' => false,
                'message' => 'Your payment could not be confirmed. Please do not pay again — contact the organising committee quoting reference ' . $order['reference'] . '.',
            ], 502);
        }

        // The verified result is the better witness to payer authentication
        // than the transient token, which the browser supplied unverified.
        $token['authenticated'] = $result['authenticated'] || ($token['authenticated'] ?? false);

        $transaction = $this->recordTransaction($registration, $order, $token, $result);

        if (! $result['approved']) {
            $registration->payment_status = Registration::PAYMENT_FAILED;
            $registration->save();

            return response()->json([
                'success' => false,
                'message' => $this->declineMessage($result),
            ], 422);
        }

        $this->markPaid($registration, $result, $transaction);

        return response()->json([
            'success'  => true,
            'redirect' => route('registration.payment.complete', $registration->payment_reference),
        ]);
    }

    /**
     * Charge the transient token from the server, for the flow without a
     * complete mandate.
     *
     * @param  array<string, mixed>  $order
     * @param  array<string, mixed>  $token
     */
    private function authorizeTransientToken(
        Request $request,
        Registration $registration,
        array $order,
        array $token
    ): JsonResponse {
        try {
            $result = $this->checkout->authorize($request->input('transient_token'), $order);
        } catch (CybersourceException $e) {
            Log::error('Unified Checkout authorization failed: ' . $e->getMessage(), $e->context);

            $this->recordTransaction($registration, $order, $token, [
                'status'         => 'ERROR',
                'transaction_id' => null,
                'reason'         => 'GATEWAY_UNREACHABLE',
                'message'        => $e->getMessage(),
                'approved'       => false,
                'raw'            => [],
            ]);

            $registration->payment_status = Registration::PAYMENT_FAILED;
            $registration->save();

            return response()->json([
                'success' => false,
                'message' => 'We could not reach the payment gateway. No charge was made — please try again.',
            ], 502);
        }

        $transaction = $this->recordTransaction($registration, $order, $token, $result);

        if (! $result['approved']) {
            $registration->payment_status = Registration::PAYMENT_FAILED;
            $registration->save();

            return response()->json([
                'success' => false,
                'message' => $this->declineMessage($result),
            ], 422);
        }

        $this->markPaid($registration, $result, $transaction);

        return response()->json([
            'success'  => true,
            'redirect' => route('registration.payment.complete', $registration->payment_reference),
        ]);
    }

    /**
     * The outcome page, reachable after payment and on any later visit.
     */
    public function complete(string $reference)
    {
        $registration = $this->findRegistration($reference);

        return view('front.page.payment-complete', [
            'registration' => $registration,
            'transaction'  => $registration->transactions()->latest('id')->first(),
        ] + $this->pageMeta());
    }

    /**
     * Recalculate and store what this registration owes, together with the
     * amount and rate the card will be charged at.
     *
     * @throws ForexException when the fee cannot be converted for settlement.
     */
    private function repriceRegistration(Registration $registration): void
    {
        $quote  = $this->fees->calculate($registration);
        $charge = $this->converter->settle($quote['total'], $quote['currency']);

        $registration->amount        = $quote['total'];
        $registration->currency      = $quote['currency'];
        $registration->fee_tier      = $quote['tier'];
        $registration->fee_breakdown = $quote['lines'];

        $registration->charge_amount   = $charge['amount'];
        $registration->charge_currency = $charge['currency'];
        $registration->fx_rate         = $charge['rate'];
        $registration->fx_rate_date    = $charge['rate_date'];

        $registration->save();
    }

    /**
     * @return array{reference: string, amount: string|float, currency: string, bill_to: array<string, string|null>}
     */
    private function orderFor(Registration $registration): array
    {
        // The amount is the settled one, in the currency the bank accepts, and
        // was fixed when the page was built — never recalculated here.
        return [
            'reference' => $registration->paymentCode(),
            'amount'    => $registration->chargeAmount(),
            'currency'  => $registration->chargeCurrency(),
            'bill_to'   => [
                'firstName'   => $this->firstName($registration->full_name),
                'lastName'    => $this->lastName($registration->full_name),
                'email'       => $registration->email,
                'phoneNumber' => $registration->phone,
                'country'     => $this->billingCountry($registration),
            ],
        ];
    }

    /**
     * @param  array<string, mixed>  $order
     * @param  array<string, mixed>  $token
     * @param  array<string, mixed>  $result
     */
    private function recordTransaction(
        Registration $registration,
        array $order,
        array $token,
        array $result
    ): PaymentTransaction {
        // amount / currency are what the gateway was asked to take; the
        // presentment pair is the published fee those settle.
        return $registration->transactions()->create([
            'reference'            => $order['reference'],
            'transaction_id'       => $result['transaction_id'],
            'status'               => $result['status'],
            'amount'               => $order['amount'],
            'currency'             => $order['currency'],
            'presentment_amount'   => $registration->amount,
            'presentment_currency' => $registration->currency,
            'fx_rate'              => $registration->fx_rate,
            'fx_rate_date'         => $registration->fx_rate_date,
            'payment_type'         => $token['payment_type'] ?? null,
            'card_type'            => $token['card_type'] ?? null,
            'card_masked'          => $token['card_masked'] ?? null,
            'authenticated'        => (bool) ($token['authenticated'] ?? false),
            'reason_code'          => $result['reason'],
            'message'              => $result['message'],
            'response'             => $result['raw'],
        ]);
    }

    private function markPaid(Registration $registration, array $result, PaymentTransaction $transaction): void
    {
        // Only a plain AUTHORIZED is final. PENDING and AUTHORIZED_PENDING_REVIEW
        // are accepted by the gateway but still to be confirmed.
        $registration->payment_status = $result['status'] === 'AUTHORIZED'
            ? Registration::PAYMENT_PAID
            : Registration::PAYMENT_PENDING;

        if ($registration->payment_status === Registration::PAYMENT_PAID) {
            $registration->paid_at = now();
            $registration->status  = 'paid';
        }

        $registration->save();

        // A mail failure must never undo a successful payment.
        try {
            if ($registration->email) {
                Mail::to($registration->email)->send(new RegistrationPaymentReceipt($registration, $transaction));
            }

            if ($admin = config('mail.admin_address')) {
                Mail::to($admin)->send(new RegistrationPaymentReceipt($registration, $transaction, true));
            }
        } catch (\Throwable $e) {
            Log::warning('Registration payment receipt email failed: ' . $e->getMessage());
        }
    }

    /**
     * A message the delegate can act on, without leaking gateway internals.
     */
    private function declineMessage(array $result): string
    {
        return match ($result['status']) {
            'DECLINED' => 'Your bank declined the payment. Please try a different card or contact your bank.',
            'INVALID_REQUEST' => 'The payment details could not be processed. Please check them and try again.',
            default => 'The payment could not be completed. Please try again, or contact the organising committee.',
        };
    }

    private function findRegistration(string $reference): Registration
    {
        return Registration::where('payment_reference', $reference)->firstOrFail();
    }

    private function firstName(?string $fullName): ?string
    {
        $parts = preg_split('/\s+/', trim((string) $fullName)) ?: [];

        return $parts[0] ?? null;
    }

    private function lastName(?string $fullName): ?string
    {
        $parts = preg_split('/\s+/', trim((string) $fullName)) ?: [];

        return count($parts) > 1 ? end($parts) : ($parts[0] ?? null);
    }

    /**
     * The billing country prefilled in the checkout. Nepali delegates default
     * to Nepal; everyone else is left to choose, since the form only records a
     * SAARC / non-SAARC grouping.
     */
    private function billingCountry(Registration $registration): ?string
    {
        return strcasecmp((string) $registration->nationality, 'Nepali') === 0 ? 'NP' : null;
    }

    /**
     * @return array<string, string>
     */
    private function pageMeta(): array
    {
        return [
            'title'            => 'Registration Payment',
            'meta_description' => '',
            'meta_keyword'     => '',
            'meta_robot'       => 'noindex, nofollow',
            'image'            => '',
        ];
    }
}
