<?php

namespace Tests\Feature;

use App\Models\ForexRate;
use App\Models\PaymentTransaction;
use App\Models\Registration;
use App\Services\Forex\ExchangeRateProvider;
use App\Services\Forex\ForexException;
use App\Services\Forex\NrbForexClient;
use Carbon\CarbonImmutable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Tests\TestCase;

/**
 * The bank settles in NPR only, so a fee priced in USD has to be converted at
 * the Nepal Rastra Bank rate before the gateway sees it.
 */
class ForexConversionTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Mail::fake();

        config([
            'cybersource.environment'    => 'test',
            'cybersource.merchant_id'    => 'test-mid',
            'cybersource.api_key_id'     => 'test-key',
            'cybersource.api_secret_key' => base64_encode('test-secret'),
            'cybersource.target_origins' => ['https://example.com'],
            'cybersource.client_version' => '1.0',

            'forex.settlement_currency' => 'NPR',
            'forex.rate_type'           => 'sell',
            'forex.markup_percent'      => 0,
            'forex.max_stale_days'      => 7,
            'forex.fallback_rates.USD'  => 0,
        ]);
    }

    /** A rates response shaped like the one NRB returns. */
    private function nrbResponse(array $days, int $pages = 1): array
    {
        return [
            'status'     => ['code' => 200],
            'errors'     => ['validation' => null],
            'params'     => [],
            'data'       => ['payload' => $days],
            'pagination' => ['page' => 1, 'pages' => $pages, 'per_page' => 100, 'total' => count($days)],
        ];
    }

    private function nrbDay(string $date, array $rates): array
    {
        return [
            'date'         => $date,
            'published_on' => $date . ' 09:00:00',
            'modified_on'  => $date . ' 09:00:00',
            'rates'        => $rates,
        ];
    }

    private function nrbRate(string $iso3, int $unit, string $buy, string $sell, ?string $name = null): array
    {
        return [
            'currency' => ['iso3' => $iso3, 'name' => $name ?? $iso3, 'unit' => $unit],
            'buy'      => $buy,
            'sell'     => $sell,
        ];
    }

    private function fakeCaptureContext(): string
    {
        $payload = $this->b64u(['ctx' => [[
            'data' => [
                'clientLibrary'          => 'https://apitest.cybersource.com/uc/v1/assets/1.0.0/UnifiedCheckout.js',
                'clientLibraryIntegrity' => 'sha256-testintegrity',
            ],
            'type' => 'gda-0.10.0',
        ]]]);

        return 'eyJraWQiOiIzZyJ9.' . $payload . '.signature';
    }

    private function fakeTransientToken(): string
    {
        $payload = $this->b64u([
            'jti'      => 'TESTJTI123',
            'metadata' => ['paymentType' => 'PANENTRY', 'cardholderAuthenticationStatus' => true],
            'content'  => ['paymentInformation' => ['card' => [
                'number' => ['maskedValue' => 'XXXXXXXXXXXX1111', 'bin' => '411111'],
                'type'   => ['value' => '001'],
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
            'full_name'         => 'Sam Tan',
            'email'             => 'sam@example.com',
            'phone'             => '9800000000',
            'nationality'       => 'Singaporean',
            'category'          => 'International Delegate',
            'reg_for'           => 'Conference',
            'accommodation'     => 'No',
            'accompanying'      => 'No',
            'status'            => 'pending',
            'payment_reference' => (string) Str::uuid(),
            'payment_status'    => Registration::PAYMENT_UNPAID,
        ], $overrides));
    }

    private function storeUsdRate(float $sell, ?CarbonImmutable $on = null): ForexRate
    {
        return ForexRate::create([
            'currency_iso3' => 'USD',
            'currency_name' => 'U.S. Dollar',
            'unit'          => 1,
            'buy'           => $sell - 0.6,
            'sell'          => $sell,
            'rate_date'     => ($on ?: CarbonImmutable::now())->toDateString(),
        ]);
    }

    // ---------------------------------------------------------------- checkout

    public function test_a_usd_fee_reaches_the_gateway_as_npr(): void
    {
        $this->storeUsdRate(135.75);

        Http::fake(['*/uc/v1/sessions' => Http::response($this->fakeCaptureContext(), 201)]);

        $registration = $this->registration();

        $this->get(route('registration.payment', $registration->payment_reference))->assertOk();

        $registration->refresh();

        // The published fee stays in the currency the fee table prices it in...
        $this->assertSame('USD', $registration->currency);
        $this->assertSame('200.00', $registration->amount);

        // ...and the charge is its NPR equivalent, rounded up to whole rupees.
        $this->assertSame('NPR', $registration->charge_currency);
        $this->assertSame('27150.00', $registration->charge_amount);
        $this->assertSame('135.750000', $registration->fx_rate);

        Http::assertSent(function ($request) {
            if (! str_contains($request->url(), '/uc/v1/sessions')) {
                return true;
            }

            $body = json_decode($request->body(), true);

            return $body['data']['orderInformation']['amountDetails']['totalAmount'] === '27150.00'
                && $body['data']['orderInformation']['amountDetails']['currency'] === 'NPR';
        });
    }

    public function test_the_delegate_is_told_what_their_card_will_be_charged(): void
    {
        $this->storeUsdRate(135.75);

        Http::fake(['*/uc/v1/sessions' => Http::response($this->fakeCaptureContext(), 201)]);

        $this->get(route('registration.payment', $this->registration()->payment_reference))
            ->assertOk()
            ->assertSee('USD 200.00')
            ->assertSee('NPR 27,150')
            ->assertSee('1 USD = 135.75 NPR');
    }

    public function test_an_npr_fee_is_charged_untouched_and_needs_no_rate(): void
    {
        Http::fake(['*/uc/v1/sessions' => Http::response($this->fakeCaptureContext(), 201)]);

        $registration = $this->registration([
            'category'    => 'NAOMS Member',
            'nationality' => 'Nepali',
        ]);

        $this->get(route('registration.payment', $registration->payment_reference))->assertOk();

        $registration->refresh();

        $this->assertSame('NPR', $registration->currency);
        $this->assertSame('NPR', $registration->charge_currency);
        $this->assertSame('18000.00', $registration->charge_amount);
        $this->assertNull($registration->fx_rate);
        $this->assertFalse($registration->isConverted());

        // No conversion means no reason to ask NRB anything.
        Http::assertNotSent(fn ($request) => str_contains($request->url(), 'nrb.org.np'));
    }

    public function test_conversion_stops_the_checkout_when_no_rate_can_be_found(): void
    {
        // NRB unreachable, nothing stored, no fallback configured.
        Http::fake(['*nrb.org.np*' => Http::response('', 503)]);

        $this->get(route('registration.payment', $this->registration()->payment_reference))
            ->assertOk()
            ->assertSee('cannot confirm the exchange rate', false);

        // The gateway must never be asked for a session we cannot price.
        Http::assertNotSent(fn ($request) => str_contains($request->url(), '/uc/v1/sessions'));
    }

    public function test_the_amount_charged_is_the_one_fixed_when_the_page_was_built(): void
    {
        Http::fake(['*/pts/v2/payments' => Http::response(['id' => '123', 'status' => 'AUTHORIZED'], 201)]);

        $registration = $this->registration([
            'amount'          => 200,
            'currency'        => 'USD',
            'charge_amount'   => 27150,
            'charge_currency' => 'NPR',
            'fx_rate'         => 135.75,
            'fx_rate_date'    => CarbonImmutable::now()->toDateString(),
        ]);

        // A rate move between page load and submission must not change what
        // the capture context was created for.
        $this->storeUsdRate(999.00);

        $this->postJson(
            route('registration.payment.process', $registration->payment_reference),
            ['transient_token' => $this->fakeTransientToken()]
        )->assertOk();

        Http::assertSent(function ($request) {
            if (! str_contains($request->url(), '/pts/v2/payments')) {
                return true;
            }

            $body = json_decode($request->body(), true);

            return $body['orderInformation']['amountDetails']['totalAmount'] === '27150.00'
                && $body['orderInformation']['amountDetails']['currency'] === 'NPR';
        });
    }

    public function test_the_transaction_records_both_the_fee_and_the_charge(): void
    {
        Http::fake(['*/pts/v2/payments' => Http::response(['id' => '123', 'status' => 'AUTHORIZED'], 201)]);

        $registration = $this->registration([
            'amount'          => 200,
            'currency'        => 'USD',
            'charge_amount'   => 27150,
            'charge_currency' => 'NPR',
            'fx_rate'         => 135.75,
            'fx_rate_date'    => '2026-09-01',
        ]);

        $this->postJson(
            route('registration.payment.process', $registration->payment_reference),
            ['transient_token' => $this->fakeTransientToken()]
        )->assertOk();

        $transaction = PaymentTransaction::first();

        $this->assertSame('27150.00', $transaction->amount);
        $this->assertSame('NPR', $transaction->currency);
        $this->assertSame('200.00', $transaction->presentment_amount);
        $this->assertSame('USD', $transaction->presentment_currency);
        $this->assertSame('135.750000', $transaction->fx_rate);
        $this->assertTrue($transaction->wasConverted());
    }

    // ---------------------------------------------------------------- rounding

    public function test_a_converted_charge_is_rounded_up_to_whole_rupees(): void
    {
        $this->storeUsdRate(135.7712);

        Http::fake(['*/uc/v1/sessions' => Http::response($this->fakeCaptureContext(), 201)]);

        $registration = $this->registration();

        $this->get(route('registration.payment', $registration->payment_reference))->assertOk();

        // 200 * 135.7712 = 27154.24, rounded up so conversion never collects
        // less than the published fee.
        $this->assertSame('27155.00', $registration->refresh()->charge_amount);
    }

    public function test_a_markup_is_applied_over_the_published_rate(): void
    {
        config(['forex.markup_percent' => 2]);

        $this->storeUsdRate(100.00);

        Http::fake(['*/uc/v1/sessions' => Http::response($this->fakeCaptureContext(), 201)]);

        $registration = $this->registration();

        $this->get(route('registration.payment', $registration->payment_reference))->assertOk();

        $this->assertSame('20400.00', $registration->refresh()->charge_amount);
    }

    // ------------------------------------------------------- rate resolution

    public function test_a_stale_rate_is_used_when_nrb_cannot_be_reached(): void
    {
        Http::fake(['*nrb.org.np*' => Http::response('', 503)]);

        $this->storeUsdRate(134.00, CarbonImmutable::now()->subDays(3));

        $rate = app(ExchangeRateProvider::class)->rateFor('USD');

        $this->assertSame(134.0, $rate['rate']);
        $this->assertTrue($rate['stale']);
        $this->assertSame('nrb-stale', $rate['source']);
    }

    public function test_a_rate_older_than_the_stale_window_is_refused(): void
    {
        Http::fake(['*nrb.org.np*' => Http::response('', 503)]);

        $this->storeUsdRate(134.00, CarbonImmutable::now()->subDays(30));

        $this->expectException(ForexException::class);

        app(ExchangeRateProvider::class)->rateFor('USD');
    }

    public function test_the_configured_fallback_is_the_last_resort(): void
    {
        config(['forex.fallback_rates.USD' => 138.0]);

        Http::fake(['*nrb.org.np*' => Http::response('', 503)]);

        $rate = app(ExchangeRateProvider::class)->rateFor('USD');

        $this->assertSame(138.0, $rate['rate']);
        $this->assertSame('fallback', $rate['source']);
    }

    public function test_a_missing_rate_is_fetched_from_nrb_on_demand(): void
    {
        $today = CarbonImmutable::now()->toDateString();

        Http::fake([
            '*nrb.org.np*' => Http::response($this->nrbResponse([
                $this->nrbDay($today, [$this->nrbRate('USD', 1, '135.10', '135.70')]),
            ])),
        ]);

        $rate = app(ExchangeRateProvider::class)->rateFor('USD');

        $this->assertSame(135.7, $rate['rate']);
        $this->assertSame('nrb', $rate['source']);
        $this->assertFalse($rate['stale']);

        // And it is stored, so the next payment does not go out to NRB again.
        $this->assertNotNull(ForexRate::forCurrency('USD')->whereDate('rate_date', $today)->first());
    }

    public function test_a_rate_published_ahead_of_today_is_not_applied(): void
    {
        Http::fake(['*nrb.org.np*' => Http::response('', 503)]);

        $this->storeUsdRate(200.00, CarbonImmutable::now()->addDay());
        $this->storeUsdRate(135.00);

        $this->assertSame(135.0, app(ExchangeRateProvider::class)->rateFor('USD')['rate']);
    }

    // ------------------------------------------------------------- NRB client

    public function test_block_quoted_currencies_are_divided_by_their_unit(): void
    {
        $today = CarbonImmutable::now()->toDateString();

        Http::fake([
            '*nrb.org.np*' => Http::response($this->nrbResponse([
                // NRB quotes JPY per 10 units and INR per 100.
                $this->nrbDay($today, [
                    $this->nrbRate('JPY', 10, '9.10', '9.20'),
                    $this->nrbRate('INR', 100, '160.00', '160.15'),
                ]),
            ])),
        ]);

        app(NrbForexClient::class)->sync(CarbonImmutable::now()->subDays(2), CarbonImmutable::now());

        $this->assertSame(10, ForexRate::forCurrency('JPY')->first()->unit);

        $provider = app(ExchangeRateProvider::class);

        $this->assertSame(0.92, $provider->rateFor('JPY')['rate']);
        $this->assertSame(1.6015, $provider->rateFor('INR')['rate']);
    }

    public function test_the_api_rejecting_a_request_inside_a_200_is_treated_as_a_failure(): void
    {
        // NRB answers HTTP 200 with status.code 400 for bad parameters.
        Http::fake(['*nrb.org.np*' => Http::response([
            'status' => ['code' => 400],
            'errors' => ['validation' => ['per_page' => ['Per Page is required']]],
            'data'   => ['payload' => null],
        ])]);

        $this->expectException(ForexException::class);

        app(NrbForexClient::class)->ratesBetween(CarbonImmutable::now()->subDay(), CarbonImmutable::now());
    }

    public function test_the_request_carries_every_parameter_the_api_requires(): void
    {
        Http::fake(['*nrb.org.np*' => Http::response($this->nrbResponse([]))]);

        app(NrbForexClient::class)->ratesBetween(
            CarbonImmutable::parse('2026-01-10'),
            CarbonImmutable::parse('2026-02-10')
        );

        Http::assertSent(function ($request) {
            parse_str((string) parse_url($request->url(), PHP_URL_QUERY), $query);

            return str_contains($request->url(), '/api/forex/v1/rates')
                && $query['from'] === '2026-01-10'
                && $query['to'] === '2026-02-10'
                && $query['page'] === '1'
                && $query['per_page'] === '100';
        });
    }

    public function test_every_page_of_a_multi_page_range_is_read(): void
    {
        $first  = $this->nrbResponse([$this->nrbDay('2026-08-30', [$this->nrbRate('USD', 1, '135.00', '135.60')])], 2);
        $second = $this->nrbResponse([$this->nrbDay('2026-08-31', [$this->nrbRate('USD', 1, '135.10', '135.70')])], 2);

        Http::fake(['*nrb.org.np*' => Http::sequence()->push($first)->push($second)]);

        $written = app(NrbForexClient::class)->sync(
            CarbonImmutable::parse('2026-08-01'),
            CarbonImmutable::parse('2026-08-31')
        );

        $this->assertSame(2, $written);
        $this->assertSame(2, ForexRate::count());
    }

    public function test_syncing_the_same_day_twice_updates_rather_than_duplicates(): void
    {
        $day = '2026-08-31';

        Http::fake(['*nrb.org.np*' => Http::sequence()
            ->push($this->nrbResponse([$this->nrbDay($day, [$this->nrbRate('USD', 1, '135.00', '135.60')])]))
            ->push($this->nrbResponse([$this->nrbDay($day, [$this->nrbRate('USD', 1, '135.05', '135.65')])])),
        ]);

        $client = app(NrbForexClient::class);
        $from   = CarbonImmutable::parse('2026-08-25');
        $to     = CarbonImmutable::parse($day);

        $client->sync($from, $to);
        $client->sync($from, $to);

        $this->assertSame(1, ForexRate::count());
        $this->assertSame('135.650000', ForexRate::first()->sell);
    }

    // ------------------------------------------------------------- settlement

    public function test_clearing_the_settlement_currency_stops_conversion_entirely(): void
    {
        // What the committee will set once the bank accepts USD directly.
        config(['forex.settlement_currency' => '']);

        Http::fake(['*/uc/v1/sessions' => Http::response($this->fakeCaptureContext(), 201)]);

        $registration = $this->registration();

        $this->get(route('registration.payment', $registration->payment_reference))->assertOk();

        $registration->refresh();

        $this->assertSame('USD', $registration->charge_currency);
        $this->assertSame('200.00', $registration->charge_amount);
        $this->assertNull($registration->fx_rate);

        Http::assertNotSent(fn ($request) => str_contains($request->url(), 'nrb.org.np'));

        Http::assertSent(function ($request) {
            if (! str_contains($request->url(), '/uc/v1/sessions')) {
                return true;
            }

            $body = json_decode($request->body(), true);

            return $body['data']['orderInformation']['amountDetails']['currency'] === 'USD';
        });
    }
}
