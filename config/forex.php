<?php

/*
|--------------------------------------------------------------------------
| Foreign Exchange
|--------------------------------------------------------------------------
|
| The registration fee table prices some categories in USD, but the acquiring
| bank settles in NPR only. Those orders are therefore converted at the Nepal
| Rastra Bank published rate before they reach the gateway: the delegate is
| quoted in USD and charged the NPR equivalent.
|
| When the bank enables USD settlement, set PAYMENT_SETTLEMENT_CURRENCY to an
| empty value. Conversion then stops and each order is charged in the currency
| it was priced in — no code change needed.
|
*/

return [

    // The only currency the gateway will settle. Empty means "charge whatever
    // the order was priced in", i.e. no conversion at all.
    'settlement_currency' => env('PAYMENT_SETTLEMENT_CURRENCY', 'NPR'),

    /*
    |----------------------------------------------------------------------
    | Nepal Rastra Bank API
    |----------------------------------------------------------------------
    |
    | https://www.nrb.org.np/api/forex/v1/rates
    |
    | Public and unauthenticated. Note that it answers HTTP 200 even for a bad
    | request — the real status is in the body's status.code.
    |
    */

    'nrb' => [
        'base_url' => env('NRB_FOREX_URL', 'https://www.nrb.org.np/api/forex/v1'),
        'timeout'  => (int) env('NRB_FOREX_TIMEOUT', 10),

        // The endpoint caps per_page at 100.
        'per_page' => 100,

        // NRB drops TLS connections intermittently, so a dropped connection is
        // retried before the fetch is called a failure.
        'retries'     => (int) env('NRB_FOREX_RETRIES', 3),
        'retry_delay' => (int) env('NRB_FOREX_RETRY_DELAY', 300),
    ],

    /*
    |----------------------------------------------------------------------
    | Which rate to apply
    |----------------------------------------------------------------------
    |
    | 'sell' is the rate at which the bank sells foreign currency. A delegate
    | owing USD is settling a USD debt with rupees, so the selling rate is the
    | one that leaves the committee whole; 'buy' would under-collect by the
    | spread.
    |
    */

    'rate_type' => env('FOREX_RATE_TYPE', 'sell'),

    // Optional margin over the NRB rate, in percent, to absorb the difference
    // between the published rate and what the acquirer actually settles at.
    // 0 charges the exact published rate.
    'markup_percent' => (float) env('FOREX_MARKUP', 0),

    /*
    |----------------------------------------------------------------------
    | Freshness
    |----------------------------------------------------------------------
    |
    | NRB does not publish on every calendar day, so the newest rate on or
    | before today is used. Beyond max_stale_days the rate is treated as
    | unusable and checkout is stopped rather than guessing.
    |
    */

    // How far back to look when asking NRB for rates.
    'lookback_days' => (int) env('FOREX_LOOKBACK_DAYS', 7),

    'max_stale_days' => (int) env('FOREX_MAX_STALE_DAYS', 7),

    // Least time between two live fetches when the stored rate is not today's,
    // so an NRB outage cannot turn into one outbound request per page view.
    'refresh_throttle' => (int) env('FOREX_REFRESH_THROTTLE', 900),

    /*
    |----------------------------------------------------------------------
    | Last-resort rates
    |----------------------------------------------------------------------
    |
    | Used only when nothing usable is stored and NRB cannot be reached. Left
    | at 0 (unset) the checkout stops instead, which is the safer default —
    | set one only if the committee would rather collect at a known rate than
    | not collect at all.
    |
    */

    'fallback_rates' => [
        'USD' => (float) env('FOREX_USD_NPR_FALLBACK', 0),
    ],

    /*
    |----------------------------------------------------------------------
    | Rounding
    |----------------------------------------------------------------------
    |
    | Decimal places for the converted charge, per settlement currency. The
    | converted amount is always rounded up, so conversion never collects less
    | than the published fee. NPR is charged in whole rupees.
    |
    */

    'rounding' => [
        'NPR'     => 0,
        'default' => 2,
    ],
];
