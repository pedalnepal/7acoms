<?php

namespace App\Services\Forex;

use Carbon\CarbonImmutable;

/**
 * Turns the fee a delegate is quoted into the amount the gateway is asked to
 * charge.
 *
 * The acquiring bank settles in one currency only (config forex.settlement_currency).
 * A fee already priced in that currency passes through untouched; anything else
 * is converted at the NRB rate for the day. Once the bank accepts the other
 * currency directly, clearing the setting makes every order pass through.
 */
class CurrencyConverter
{
    public function __construct(private ExchangeRateProvider $rates) {}

    /**
     * @return array{
     *     amount: float,
     *     currency: string,
     *     converted: bool,
     *     rate: ?float,
     *     rate_date: ?string,
     *     source: ?string,
     *     stale: bool
     * }
     *
     * @throws ForexException when the fee cannot be converted at any known rate.
     */
    public function settle(float $amount, string $currency, ?CarbonImmutable $on = null): array
    {
        $currency   = strtoupper($currency);
        $settlement = strtoupper((string) config('forex.settlement_currency', ''));

        if ($settlement === '' || $settlement === $currency) {
            return [
                'amount'    => round($amount, 2),
                'currency'  => $currency,
                'converted' => false,
                'rate'      => null,
                'rate_date' => null,
                'source'    => null,
                'stale'     => false,
            ];
        }

        // NRB quotes foreign currencies against the rupee only, so that is the
        // one settlement currency this can convert into.
        if ($settlement !== 'NPR') {
            throw ForexException::withContext(
                "Cannot convert {$currency} into {$settlement}: the NRB rates are quoted against NPR only.",
                ['from' => $currency, 'to' => $settlement]
            );
        }

        $rate = $this->rates->rateFor($currency, $on);

        return [
            'amount'    => $this->roundUp($amount * $rate['rate'], $settlement),
            'currency'  => $settlement,
            'converted' => true,
            'rate'      => $rate['rate'],
            'rate_date' => $rate['date'],
            'source'    => $rate['source'],
            'stale'     => $rate['stale'],
        ];
    }

    /**
     * Rounded up, never down: a converted charge that fell short of the
     * published fee would leave the committee collecting less than its own
     * fee table. NPR is charged in whole rupees.
     */
    private function roundUp(float $amount, string $currency): float
    {
        $decimals = (int) config(
            "forex.rounding.$currency",
            config('forex.rounding.default', 2)
        );

        $factor = 10 ** $decimals;

        // A float product such as 27149.999999997 must not round up to 27150.01,
        // so shave the representation error before taking the ceiling.
        return ceil(round($amount * $factor, 4)) / $factor;
    }
}
