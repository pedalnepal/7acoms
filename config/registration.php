<?php

/*
|--------------------------------------------------------------------------
| Registration Fee Schedule
|--------------------------------------------------------------------------
|
| The published fee table for the 7th ACOMS PG Trainee Congress 2027, kept in
| config so the organising committee's rates can be adjusted without touching
| code. Amounts mirror the table on the "Registration Details" page.
|
| The tier that applies is decided by the date payment is made, matching the
| registration guideline: "The applicable fee is determined by the date
| payment is received, not the date the form is submitted."
|
*/

return [

    // Inclusive cut-off for each tier. A payment made after the last date
    // listed here falls into the final tier.
    'tiers' => [
        'early'   => ['label' => 'Early Bird Registration', 'until' => '2026-11-15'],
        'regular' => ['label' => 'Registration',            'until' => '2027-01-15'],
        'late'    => ['label' => 'Late Registration',       'until' => null],
    ],

    // Category => currency and the fee for each tier.
    'categories' => [
        'NAOMS Member' => [
            'currency' => 'NPR',
            'fees'     => ['early' => 1, 'regular' => 20000, 'late' => 22000],
        ],
        'Non-NAOMS Member (Nepalese)' => [
            'currency' => 'NPR',
            'fees'     => ['early' => 20000, 'regular' => 22000, 'late' => 24000],
        ],
        'International Delegate' => [
            'currency' => 'USD',
            'fees'     => ['early' => 200, 'regular' => 240, 'late' => 260],
        ],
        'Residents and Dental Surgeons (Nepalese)' => [
            'currency' => 'NPR',
            'fees'     => ['early' => 15000, 'regular' => 17000, 'late' => 19000],
        ],
        'Residents and Dental Surgeons (International)' => [
            'currency' => 'USD',
            'fees'     => ['early' => 150, 'regular' => 170, 'late' => 190],
        ],
        'Accompanying Person' => [
            'currency' => 'NPR',
            'fees'     => ['early' => 15000, 'regular' => 15000, 'late' => 15000],
        ],
        'Accompanying Person (International)' => [
            'currency' => 'USD',
            'fees'     => ['early' => 100, 'regular' => 100, 'late' => 100],
        ],
    ],

    // Which accompanying-person category to bill per accompanying head, chosen
    // by the currency of the delegate's own category.
    'accompanying_category' => [
        'NPR' => 'Accompanying Person',
        'USD' => 'Accompanying Person (International)',
    ],

    /*
    |----------------------------------------------------------------------
    | Add-ons
    |----------------------------------------------------------------------
    |
    | The published fee table covers delegate categories only. The hands-on
    | course and accommodation have no published rate, so they are billed at
    | zero until the committee sets one here (per currency).
    |
    */

    'hands_on_course' => [
        'NPR' => 0,
        'USD' => 0,
    ],

    // Charged per room booked.
    'accommodation' => [
        'Single' => ['NPR' => 0, 'USD' => 0],
        'Double / Twin' => ['NPR' => 0, 'USD' => 0],
        'Deluxe' => ['NPR' => 0, 'USD' => 0],
    ],

    // Registration categories that are themselves an accompanying person, and
    // so are never charged an additional accompanying head.
    'accompanying_only_categories' => [
        'Accompanying Person',
        'Accompanying Person (International)',
    ],
];
