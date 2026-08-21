<?php

/*
|--------------------------------------------------------------------------
| Cybersource Unified Checkout
|--------------------------------------------------------------------------
|
| Credentials and behaviour for the Unified Checkout REST integration used by
| the registration payment flow. Secrets belong in .env — never in this file.
|
| The API key id / shared secret pair is generated in the Business Center
| under Payment Configuration > Key Management > REST Shared Secret.
|
*/

return [

    // 'test' talks to the sandbox, 'production' to live processing.
    'environment' => env('CYBS_ENVIRONMENT', 'test'),

    'hosts' => [
        'test'       => env('CYBS_HOST_TEST', 'apitest.cybersource.com'),
        'production' => env('CYBS_HOST_PRODUCTION', 'api.cybersource.com'),
    ],

    // Merchant ID, and the REST shared-secret key pair used to sign requests.
    'merchant_id'    => env('CYBS_MERCHANT_ID'),
    'api_key_id'     => env('CYBS_API_KEY_ID'),
    'api_secret_key' => env('CYBS_API_SECRET_KEY'),

    'timeout' => (int) env('CYBS_TIMEOUT', 30),

    /*
    |----------------------------------------------------------------------
    | Capture context defaults
    |----------------------------------------------------------------------
    |
    | These shape the Sessions API request that builds the capture context.
    |
    */

    // Every origin that will host the SDK. Production origins must be https.
    // Comma-separated in .env; falls back to APP_URL's origin.
    'target_origins' => array_values(array_filter(array_map(
        'trim',
        explode(',', (string) env('CYBS_TARGET_ORIGINS', ''))
    ))),

    'country' => env('CYBS_COUNTRY', 'NP'),
    'locale'  => env('CYBS_LOCALE', 'en_US'),

    // Pinned so the JavaScript surface the checkout page is written against
    // (VAS.UnifiedCheckout) stays valid; patch releases still arrive
    // automatically. The gateway requires MAJOR.MINOR here and rejects a bare
    // major such as "1". Set CYBS_CLIENT_VERSION empty to always take the
    // newest version instead.
    'client_version' => env('CYBS_CLIENT_VERSION', '1.0'),

    'allowed_payment_types' => array_values(array_filter(array_map(
        'trim',
        explode(',', (string) env('CYBS_ALLOWED_PAYMENT_TYPES', 'PANENTRY'))
    ))),

    'allowed_card_networks' => array_values(array_filter(array_map(
        'trim',
        explode(',', (string) env('CYBS_ALLOWED_CARD_NETWORKS', 'VISA,MASTERCARD,AMEX,JCB,DISCOVER,DINERSCLUB'))
    ))),

    'button_type' => env('CYBS_BUTTON_TYPE', 'PAY'),

    /*
    |----------------------------------------------------------------------
    | Processing
    |----------------------------------------------------------------------
    |
    | The transient token returned by the browser is authorised server-side, so
    | the amount charged is always the amount this application calculated.
    |
    */

    // true performs a sale (authorisation + capture in one request);
    // false authorises only, leaving the capture to be requested later.
    'capture' => (bool) env('CYBS_CAPTURE', true),

    'merchant_descriptor' => env('CYBS_MERCHANT_DESCRIPTOR', env('APP_NAME', '7th ACOMS 2027')),
];
