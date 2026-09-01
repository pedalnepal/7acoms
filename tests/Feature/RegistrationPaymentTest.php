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

    /**
     * A fixed throwaway key pair standing in for the one Cybersource signs
     * with. It is written out rather than generated, because openssl_pkey_new()
     * needs an openssl.cnf that a bare PHP CLI often cannot find — and a test
     * of signature verification should not turn on that.
     */
    private const TEST_PRIVATE_KEY = <<<'PEM'
        -----BEGIN PRIVATE KEY-----
        MIIEvAIBADANBgkqhkiG9w0BAQEFAASCBKYwggSiAgEAAoIBAQDLyT9a6gaTBogW
        fFhbJwlneZcH0Aj++xppv66mccOpuyaZDZtK2vSJml3FQl/mfTbWgj5RRQV1mX6i
        PTtTmm1n0jjCYgXsysHeH1qh8TI04kicgp2pe+csWLIfUVk9Kv+RTiACEfWB9lWw
        /IldwwdsouF9KbWlrXbeCQFneq1PKFAB511V/wV4c7AgwBdPj4J1x3nFDSeH+v42
        uCobU9ETVw8AWgaTdm8KBLT2ecoY0E4iPxFZvO2LRtiYigexqjpnV/qheTUpiYsF
        3VFok+NxS+c35g+v3HgUYG1P8CWUSFSAqGsXeubnH2iXC29JVWnyzOPz/GjtfSR3
        /fi0emqhAgMBAAECggEABmYGfvqHo9Cp77jORmhqn2zeLODfk2/7c5QQ7gl26Qqn
        Ctm2RUqsMHDgHlw36iMG2IPk5ITcB8X+x6XQCc84vbCIVzQgNv/jx+9ol1qdJrNv
        L7jbLfHlv4r2ZszVVjoWJbAUCbSaJo8/QAwh3QeNYWAY3+byHOZeK9kRCfh8XhaZ
        zRxqK1FDcponbV1LZ6LxG10k98cyuJTP95t+heKwuVF3e+ZhbOWedhOXcXDXIRkv
        BbrPp+9C+VZwXnan77fYXOcOiiHYsoR2+oiHDBVoWjm0qrVe666xSZtYBJ4ozgO9
        CNo+InoRIm3S4DsUA1dIx1+MwwQJPyOIYaJF5F10lQKBgQD42gUPiGCBRlnMcM6V
        r+hymJ8IjsiuwwxLHTpSEX6iLm9+ZLJusk3q402gYuM+Dt5vTvasvsfWLJIJsvub
        7IcjrvMAUBg3gb5qfHFKgqMfO3KNgCWQ2potkM+vaT6F+X8d34sJH98Ude0jZ9uJ
        k9SwM5CGS8JN9SlX/S/AIitfHQKBgQDRo9RV3F6xfzCu95sSWXl/mzDMaZiaQyvH
        G6xrGnIcI30wlPQYO9iSH25Er+ry7oFMKM1+DBXaXeCa6AqXo7LU8KMAk7POLgJd
        V2DR+uCx1/nlgCMSj4Ory2z1hCQOnewCpY8XyvvB9Wp28M1l88tLCR/MJ3WCLPK6
        I9NJkS1OVQKBgHrObOzramShqTMhAHISa24SL3lzrUnBa9GtzNgvVgUHUXJYk03Z
        pGYkYmyiIG7Bu7fGiilZxRjGyhMLRuv93/sWHUmsPSc3WhcG7onMyl9hYPi/ospH
        dwrBihyQnZBUPg0qAl0wY1CfmbV9TXQphi6mY85CmNMMB6kqg2QX+LtpAoGAZ9oz
        DD3ZIzERGvxKc/KjP9XPfNQq5vCztTU+WuiE8u+ZywFfUsoC5ZBwfdJwH9yLbQEh
        m3m9Maoj5FljNe/AXcC/3z9Maa1dOoRY0Gzp9vA5Oboapc02u1pRJedKPXt6OW1u
        z6icw18iWL294u29HfTtLaO2kMBXlmK8/srOmOkCgYAOJUA1rY4Ncu7a96UosjRg
        2bWq7Sz10hcTr0ma0pgn4pio1lXyMoK+sEyGly+0qn++hkrvclelwRD136+lV4PP
        q+5YoJCFRWoKP4d14fIInsvFnnQZtkjcanR4frCaRoqoSMF0aK7OaGuChVPPvoGd
        UHZBVQEIHqu6okdKlIEmAw==
        -----END PRIVATE KEY-----
        PEM;

    /** A second key pair, standing in for one the gateway never published. */
    private const IMPOSTOR_PRIVATE_KEY = <<<'PEM'
        -----BEGIN PRIVATE KEY-----
        MIIEvQIBADANBgkqhkiG9w0BAQEFAASCBKcwggSjAgEAAoIBAQCw61nzkhNVjmvo
        pwte9IlbpnvwLpLCZOaUHyFdU+D3KFsHKX263HgRj1ibEhDyo721J0bQKa3kW7Xi
        b593fejKv/rN5mYNNlqDqhUM88BXvUtCbh+z92vVQOTt63xbHZBwF1ts8DAl9BiI
        Ox/Ir+F2NV5zGokNqhsOv3mZE8g8Scv/bxvt36tdUtaK6HHoUX0wzS2nZzYyTpTQ
        RLMUNiaBWJOBDxnjv6wu9zT4PpnqwNLikisQl7WWgtMUJVtnVqPs2gudgWTNqbfp
        aQ/jb30hUBs5FbNNuJpbd+iVIhAhm3OHSAmiMfgFhYBI3tI52W4VhuR2MrGC5T6T
        nNZac8hJAgMBAAECggEAFgazG/09fXseCajUZFXHDRtI1mICAQQ9ZccLaRpiiXrj
        fFyIy/0bULpbQnEpHYBPw9A4Q33Q6buWRTL29FI/a5CjucM7xNzVv4srFEH7miDV
        0Bc0SLxe1z29Uras9I4Vd7McvY8Yu3zsmfdccWieRhCGagetdP9O35LsoWeCWCj1
        wzWBSOUaksSNyaxdrqZOuNSoUV9N20bfIqt9LTGi9Hc/WTSt//X7FH4F/fJhaCEF
        ByJNfWnDuw0OcC9M5EHEC0vYmS7+xLtlrIE+hKZc3HIUOUy9SZQKdF1PvS7G0CCX
        sn+U7syyVL20O7GO+1FXTzbIlozBcU0ViBMbP2eFtQKBgQDlIoR9hwPS7Kore6Bm
        plC1UyHBcLDycmJNxrw5hu0ReZa8GkzvVkxAaI970X0KgEINK6dVs7UGIGDSBhtI
        g+8qTwt4cItmD6C7Ak+eVeaAoPAc7OHAC941bsmuBCHLECJCCJorHEesYDEYf2jE
        Lr/JKsVM54CT+MzwtlCBsrPC/QKBgQDFqZXxywE69IKXpZim6S31IOMuFuS5d8CX
        BxD0dZxZDo8vCtPbAuvMpcN9MaG1/eY4VP339I/hkOMWwFCeec4kR/mcD9FrsbkP
        20gBBTZr6e6Ayrm7ejl19N57Hx2XHDTvCfAo6wC3jEJ3on/YqXUhh9mfvGYj2Bfq
        zqY8FoU6PQKBgDhAXhhXAghRE4GzSvCIWf3516qemMIcdKe1Z1YUGrUUjX3GUyba
        n8OcJ8V062adkBmdoun2QTWs0zgcSaxmv1s6po09y+sVYRcn/RXY7sqbN/dR7CXy
        g+3IMfjniMhKQK7HX9m/ipT11He4J6cp9ulS97bminlJNj7N8zuz/E+dAoGAa95p
        cuO7TyYiool/bg8wwmZpNDzQI5sDoif8C7ynTz6PyzdYeFv2vN71eTv9qSjfW3ye
        gJOZdZqxIzhehq2oXspcoNtNCT2a5dlgSRZEJc4rF2QKyhIgi4vPYlePuWhuRskg
        o+Pjp0dG+qzWojGQN3VAwVh6UTNbkgumUoJjwG0CgYEAoalhPMtd0RUrUh0GKApL
        hsk0lK1FCF6RybIU2bzy/T5aRCbNMELR3UImaMd96xZpO7CCdUI3bE4MDiHJPlMP
        /hDi3vQ/vM99Zlio5QBYpsYkYwyeb09tYd+bdxgf8OsvSIuSmbwrbnbyULKoiXnm
        zpO/qL9KMM5cbG7mBARaw5k=
        -----END PRIVATE KEY-----
        PEM;

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
            // The shipped default: the gateway runs 3-D Secure and the sale.
            'cybersource.complete_mandate_type'   => 'CAPTURE',
            'cybersource.consumer_authentication' => '3DS',
        ]);
    }

    /**
     * Switch off the complete mandate, leaving the flow where this application
     * authorises the transient token itself.
     */
    private function withoutCompleteMandate(): void
    {
        config(['cybersource.complete_mandate_type' => null]);
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
        return $this->b64uRaw(json_encode($data));
    }

    private function b64uRaw(string $value): string
    {
        return rtrim(strtr(base64_encode($value), '+/', '-_'), '=');
    }

    private function signingKey(): \OpenSSLAsymmetricKey
    {
        return openssl_pkey_get_private(self::TEST_PRIVATE_KEY);
    }

    /**
     * Publish the test key at the public-keys endpoint, in the JWK form the
     * gateway serves, so the verifier can rebuild it.
     */
    private function fakePublicKeyEndpoint(): void
    {
        $details = openssl_pkey_get_details($this->signingKey());

        Http::fake([
            '*/flex/v2/public-keys/*' => Http::response([
                'kty' => 'RSA',
                'use' => 'sig',
                'kid' => 'test-kid',
                'n'   => $this->b64uRaw($details['rsa']['n']),
                'e'   => $this->b64uRaw($details['rsa']['e']),
            ], 200),
        ]);
    }

    /**
     * A payment result shaped like the one the complete mandate returns,
     * signed with the test key.
     */
    private function fakePaymentResult(array $overrides = []): string
    {
        $claims = array_replace_recursive([
            'iss'  => 'Flex/08',
            'data' => [
                'id'                         => '7654321098',
                'status'                     => 'AUTHORIZED',
                'clientReferenceInformation' => ['code' => 'ACOMS-000001'],
                'orderInformation'           => [
                    'amountDetails' => ['totalAmount' => '18000.00', 'currency' => 'NPR'],
                ],
                'consumerAuthenticationInformation' => [
                    'transactionStatus' => 'Y',
                    'cavv'              => 'AAABCZIhcQAAAABZlyFxAAAAAAA=',
                ],
            ],
        ], $overrides);

        return $this->sign($claims);
    }

    private function sign(array $claims, ?\OpenSSLAsymmetricKey $key = null): string
    {
        $body = $this->b64u(['alg' => 'RS256', 'kid' => 'test-kid'])
            . '.' . $this->b64u($claims);

        openssl_sign($body, $signature, $key ?? $this->signingKey(), OPENSSL_ALGO_SHA256);

        return $body . '.' . $this->b64uRaw($signature);
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

        // Read back rather than written down: the point is that the gateway is
        // told what this application priced, and the fee table moves.
        $registration->refresh();
        $expectedAmount = number_format((float) $registration->chargeAmount(), 2, '.', '');

        $this->assertGreaterThan(0, (float) $expectedAmount);

        // The Sessions request must carry the server-calculated amount, and
        // the mandate that puts 3-D Secure in front of the sale.
        Http::assertSent(function ($request) use ($registration, $expectedAmount) {
            $body = json_decode($request->body(), true);

            return $request->url() === 'https://apitest.cybersource.com/uc/v1/sessions'
                && $body['data']['orderInformation']['amountDetails']['totalAmount'] === $expectedAmount
                && $body['data']['orderInformation']['amountDetails']['currency'] === $registration->chargeCurrency()
                && $body['targetOrigins'] === ['https://example.com']
                && $body['completeMandate']['type'] === 'CAPTURE'
                && $body['completeMandate']['consumerAuthentication'] === '3DS'
                && $request->hasHeader('Signature')
                && $request->hasHeader('Digest')
                && $request->hasHeader('v-c-merchant-id');
        });
    }

    public function test_the_capture_context_omits_the_mandate_when_it_is_switched_off(): void
    {
        $this->withoutCompleteMandate();

        Http::fake([
            '*/uc/v1/sessions' => Http::response($this->fakeCaptureContext(), 201),
        ]);

        $this->get(route('registration.payment', $this->registration()->payment_reference))
            ->assertOk();

        Http::assertSent(function ($request) {
            return ! array_key_exists('completeMandate', json_decode($request->body(), true));
        });
    }

    public function test_a_verified_complete_mandate_result_marks_the_registration_paid(): void
    {
        $this->fakePublicKeyEndpoint();

        $registration = $this->registration();
        $registration->update(['amount' => 18000, 'currency' => 'NPR', 'fee_tier' => 'early']);

        $this->postJson(
            route('registration.payment.process', $registration->payment_reference),
            [
                'transient_token' => $this->fakeTransientToken(),
                'payment_result'  => $this->fakePaymentResult(),
            ]
        )->assertOk()->assertJson(['success' => true]);

        $registration->refresh();

        $this->assertTrue($registration->isPaid());
        $this->assertSame('paid', $registration->status);

        $transaction = PaymentTransaction::first();
        $this->assertSame('7654321098', $transaction->transaction_id);
        $this->assertSame('AUTHORIZED', $transaction->status);
        $this->assertTrue($transaction->authenticated);

        // The gateway already took the money; nothing may be charged again.
        Http::assertNotSent(fn ($request) => str_contains($request->url(), '/pts/v2/payments'));
    }

    public function test_a_declined_complete_mandate_result_leaves_the_registration_unpaid(): void
    {
        $this->fakePublicKeyEndpoint();

        $registration = $this->registration();
        $registration->update(['amount' => 18000, 'currency' => 'NPR']);

        $this->postJson(
            route('registration.payment.process', $registration->payment_reference),
            [
                'transient_token' => $this->fakeTransientToken(),
                'payment_result'  => $this->fakePaymentResult(['data' => ['status' => 'DECLINED']]),
            ]
        )->assertStatus(422)->assertJson(['success' => false]);

        $registration->refresh();

        $this->assertFalse($registration->isPaid());
        $this->assertSame(Registration::PAYMENT_FAILED, $registration->payment_status);
        $this->assertSame('DECLINED', PaymentTransaction::first()->status);
    }

    public function test_a_result_signed_by_the_wrong_key_is_refused(): void
    {
        $this->fakePublicKeyEndpoint();

        $registration = $this->registration();
        $registration->update(['amount' => 18000, 'currency' => 'NPR']);

        // Signed with a key the gateway never published.
        $impostor = openssl_pkey_get_private(self::IMPOSTOR_PRIVATE_KEY);

        $forged = $this->sign(['data' => [
            'id'                         => '999',
            'status'                     => 'AUTHORIZED',
            'clientReferenceInformation' => ['code' => $registration->paymentCode()],
        ]], $impostor);

        $this->postJson(
            route('registration.payment.process', $registration->payment_reference),
            ['transient_token' => $this->fakeTransientToken(), 'payment_result' => $forged]
        )->assertStatus(502)->assertJson(['success' => false]);

        $this->assertFalse($registration->refresh()->isPaid());
        $this->assertSame('ERROR', PaymentTransaction::first()->status);
    }

    public function test_a_result_issued_for_another_registration_is_refused(): void
    {
        $this->fakePublicKeyEndpoint();

        $registration = $this->registration();
        $registration->update(['amount' => 18000, 'currency' => 'NPR']);

        $this->postJson(
            route('registration.payment.process', $registration->payment_reference),
            [
                'transient_token' => $this->fakeTransientToken(),
                'payment_result'  => $this->fakePaymentResult([
                    'data' => ['clientReferenceInformation' => ['code' => 'ACOMS-000999']],
                ]),
            ]
        )->assertStatus(502);

        $this->assertFalse($registration->refresh()->isPaid());
    }

    public function test_the_mandate_flow_refuses_a_request_that_carries_no_result(): void
    {
        Http::fake();

        $registration = $this->registration();
        $registration->update(['amount' => 18000, 'currency' => 'NPR']);

        $this->postJson(
            route('registration.payment.process', $registration->payment_reference),
            ['transient_token' => $this->fakeTransientToken()]
        )->assertStatus(502)->assertJson(['success' => false]);

        $this->assertFalse($registration->refresh()->isPaid());
    }

    public function test_a_successful_authorization_marks_the_registration_paid(): void
    {
        $this->withoutCompleteMandate();

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
        $this->withoutCompleteMandate();

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
