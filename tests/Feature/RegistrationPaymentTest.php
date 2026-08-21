<?php

namespace Tests\Feature;

use App\Models\PaymentTransaction;
use App\Models\Registration;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class RegistrationPaymentTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Mail::fake();

        // Pinned so the suite does not depend on the ambient .env.
        config([
            'cybersource.environment'    => 'test',
            'cybersource.merchant_id'    => 'test-mid',
            'cybersource.api_key_id'     => 'test-key',
            'cybersource.api_secret_key' => base64_encode('test-secret'),
            'cybersource.target_origins' => ['https://example.com'],
            'cybersource.client_version' => '1.0',
        ]);
    }

    /** A capture context shaped like the one the Sessions API returns. */
    private function fakeCaptureContext(): string
    {
        $payload = $this->b64u([
            'ctx' => [[
                'data' => [
                    'clientLibrary'          => 'https://apitest.cybersource.com/uc/v1/assets/1.0.0/UnifiedCheckout.js',
                    'clientLibraryIntegrity' => 'sha256-testintegrity',
                ],
                'type' => 'gda-0.10.0',
            ]],
        ]);

        return 'eyJraWQiOiIzZyJ9.' . $payload . '.signature';
    }

    private function fakeTransientToken(): string
    {
        $payload = $this->b64u([
            'jti'      => 'TESTJTI123',
            'metadata' => ['paymentType' => 'PANENTRY', 'cardholderAuthenticationStatus' => true],
            'content'  => ['paymentInformation' => ['card' => [
                'number'          => ['maskedValue' => 'XXXXXXXXXXXX1111', 'bin' => '411111'],
                'type'            => ['value' => '001'],
                'expirationMonth' => ['value' => '12'],
                'expirationYear'  => ['value' => '2026'],
            ]]],
        ]);

        return 'eyJraWQiOiIzZyJ9.' . $payload . '.signature';
    }

    private function b64u(array $data): string
    {
        return rtrim(strtr(base64_encode(json_encode($data)), '+/', '-_'), '=');
    }

    private function registration(array $overrides = []): Registration
    {
        return Registration::create(array_merge([
            'full_name'         => 'Asha Rai',
            'email'             => 'asha@example.com',
            'phone'             => '9800000000',
            'nationality'       => 'Nepali',
            'category'          => 'NAOMS Member',
            'reg_for'           => 'Conference',
            'accommodation'     => 'No',
            'accompanying'      => 'No',
            'status'            => 'pending',
            'payment_reference' => (string) \Illuminate\Support\Str::uuid(),
            'payment_status'    => Registration::PAYMENT_UNPAID,
        ], $overrides));
    }

    public function test_submitting_the_registration_form_redirects_to_payment_with_a_server_calculated_fee(): void
    {
        $response = $this->post(route('registration.store'), [
            'fullName'      => 'Asha Rai',
            'email'         => 'asha@example.com',
            'phone'         => '9800000000',
            'designation'   => 'Resident',
            'workplace'     => 'Kathmandu',
            'idCard'        => UploadedFile::fake()->image('id.jpg'),
            'nationality'   => 'Nepali',
            'naomsMember'   => 'Yes',
            'regFor'        => 'Conference',
            'accommodation' => 'No',
            'accompanying'  => 'No',
            'category'      => 'NAOMS Member',
        ]);

        $registration = Registration::first();

        $this->assertNotNull($registration);
        $this->assertNotNull($registration->payment_reference);
        $this->assertSame(Registration::PAYMENT_UNPAID, $registration->payment_status);
        $this->assertSame('NPR', $registration->currency);
        $this->assertGreaterThan(0, (float) $registration->amount);

        $response->assertRedirect(route('registration.payment', $registration->payment_reference));
    }

    public function test_the_payment_receipt_upload_is_no_longer_required(): void
    {
        $this->post(route('registration.store'), [
            'fullName' => 'Asha Rai', 'email' => 'asha@example.com', 'phone' => '98',
            'designation' => 'Resident', 'workplace' => 'KTM',
            'idCard' => UploadedFile::fake()->image('id.jpg'),
            'nationality' => 'Nepali', 'naomsMember' => 'Yes', 'regFor' => 'Conference',
            'accommodation' => 'No', 'accompanying' => 'No', 'category' => 'NAOMS Member',
        ])->assertSessionHasNoErrors();
    }

    public function test_the_checkout_page_renders_the_sdk_from_the_capture_context(): void
    {
        Http::fake([
            '*/uc/v1/sessions' => Http::response($this->fakeCaptureContext(), 201),
        ]);

        $registration = $this->registration();

        $this->get(route('registration.payment', $registration->payment_reference))
            ->assertOk()
            // The script URL and integrity hash come from the capture context,
            // never hard-coded.
            ->assertSee('https://apitest.cybersource.com/uc/v1/assets/1.0.0/UnifiedCheckout.js')
            ->assertSee('sha256-testintegrity')
            ->assertSee('payment-buttons');

        // The Sessions request must carry the server-calculated amount.
        Http::assertSent(function ($request) {
            $body = json_decode($request->body(), true);

            return $request->url() === 'https://apitest.cybersource.com/uc/v1/sessions'
                && $body['data']['orderInformation']['amountDetails']['totalAmount'] === '18000.00'
                && $body['data']['orderInformation']['amountDetails']['currency'] === 'NPR'
                && $body['targetOrigins'] === ['https://example.com']
                && $request->hasHeader('Signature')
                && $request->hasHeader('Digest')
                && $request->hasHeader('v-c-merchant-id');
        });
    }

    public function test_a_successful_authorization_marks_the_registration_paid(): void
    {
        Http::fake([
            '*/pts/v2/payments' => Http::response([
                'id'     => '7654321098',
                'status' => 'AUTHORIZED',
            ], 201),
        ]);

        $registration = $this->registration();
        $registration->update(['amount' => 18000, 'currency' => 'NPR', 'fee_tier' => 'early']);

        $this->postJson(
            route('registration.payment.process', $registration->payment_reference),
            ['transient_token' => $this->fakeTransientToken()]
        )->assertOk()->assertJson([
            'success'  => true,
            'redirect' => route('registration.payment.complete', $registration->payment_reference),
        ]);

        $registration->refresh();

        $this->assertTrue($registration->isPaid());
        $this->assertNotNull($registration->paid_at);
        $this->assertSame('paid', $registration->status);

        $transaction = PaymentTransaction::first();
        $this->assertSame('7654321098', $transaction->transaction_id);
        $this->assertSame('AUTHORIZED', $transaction->status);
        $this->assertSame('XXXXXXXXXXXX1111', $transaction->card_masked);
        $this->assertTrue($transaction->authenticated);

        // The charge must use the amount this application calculated, not
        // anything the browser could influence.
        Http::assertSent(function ($request) {
            $body = json_decode($request->body(), true);

            return str_contains($request->url(), '/pts/v2/payments')
                && $body['orderInformation']['amountDetails']['totalAmount'] === '18000.00'
                && $body['tokenInformation']['transientTokenJwt'] !== ''
                && $body['clientReferenceInformation']['code'] !== '';
        });
    }

    public function test_a_declined_payment_leaves_the_registration_unpaid(): void
    {
        Http::fake([
            '*/pts/v2/payments' => Http::response([
                'id'     => '111',
                'status' => 'DECLINED',
                'errorInformation' => ['message' => 'Decline - General decline of the card.'],
            ], 201),
        ]);

        $registration = $this->registration();
        $registration->update(['amount' => 18000, 'currency' => 'NPR']);

        $this->postJson(
            route('registration.payment.process', $registration->payment_reference),
            ['transient_token' => $this->fakeTransientToken()]
        )->assertStatus(422)->assertJson(['success' => false]);

        $registration->refresh();

        $this->assertFalse($registration->isPaid());
        $this->assertSame(Registration::PAYMENT_FAILED, $registration->payment_status);
        $this->assertSame('DECLINED', PaymentTransaction::first()->status);
    }

    public function test_an_already_paid_registration_is_not_charged_twice(): void
    {
        Http::fake();

        $registration = $this->registration([
            'payment_status' => Registration::PAYMENT_PAID,
            'amount'         => 18000,
            'currency'       => 'NPR',
        ]);

        $this->postJson(
            route('registration.payment.process', $registration->payment_reference),
            ['transient_token' => $this->fakeTransientToken()]
        )->assertOk()->assertJson(['success' => true]);

        Http::assertNothingSent();
        $this->assertSame(0, PaymentTransaction::count());

        // And the checkout page sends them straight to their receipt.
        $this->get(route('registration.payment', $registration->payment_reference))
            ->assertRedirect(route('registration.payment.complete', $registration->payment_reference));
    }

    public function test_the_checkout_page_degrades_gracefully_when_the_gateway_fails(): void
    {
        Http::fake([
            '*/uc/v1/sessions' => Http::response(['reason' => 'INVALID_APIKEY'], 400),
        ]);

        $this->get(route('registration.payment', $this->registration()->payment_reference))
            ->assertOk()
            ->assertSee('could not start the secure payment session', false);
    }

    /**
     * The gateway refuses any origin that is not https on a real domain, and
     * rejects the whole session. Catching it here keeps the failure legible.
     */
    #[DataProvider('invalidOrigins')]
    public function test_an_unusable_target_origin_is_rejected_before_any_request(string $origin): void
    {
        Http::fake();
        config(['cybersource.target_origins' => [$origin]]);

        $this->get(route('registration.payment', $this->registration()->payment_reference))
            ->assertOk()
            ->assertSee('could not start the secure payment session', false);

        Http::assertNothingSent();
    }

    public static function invalidOrigins(): array
    {
        return [
            'plain http'   => ['http://acoms2027.org.np'],
            'localhost'    => ['https://localhost'],
            'http localhost with port' => ['http://localhost:8000'],
            'ip address'   => ['https://127.0.0.1'],
        ];
    }

    public function test_a_valid_https_origin_is_accepted(): void
    {
        Http::fake(['*/uc/v1/sessions' => Http::response($this->fakeCaptureContext(), 201)]);
        config(['cybersource.target_origins' => ['https://acoms2027.org.np']]);

        $this->get(route('registration.payment', $this->registration()->payment_reference))
            ->assertOk()
            ->assertSee('UnifiedCheckout.js');

        // clientVersion must be MAJOR.MINOR — a bare "1" is refused by the gateway.
        Http::assertSent(function ($request) {
            $body = json_decode($request->body(), true);

            return $body['targetOrigins'] === ['https://acoms2027.org.np']
                && preg_match('/^\d+\.\d+$/', $body['clientVersion']) === 1;
        });
    }

    public function test_an_unknown_payment_reference_is_not_found(): void
    {
        $this->get(route('registration.payment', 'aaaaaaaa-bbbb-cccc-dddd-eeeeeeeeeeee'))
            ->assertNotFound();
    }
}
