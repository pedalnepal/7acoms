<?php

namespace App\Services\Forex;

use App\Models\ForexRate;
use Carbon\CarbonImmutable;
use Carbon\CarbonInterface;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

/**
 * Resolves the rate to apply to a payment, in rupees per one unit of a foreign
 * currency.
 *
 * A checkout must not depend on NRB being up at that moment, so stored rates
 * are the primary source and the live API only tops them up. Resolution runs
 * down the sources in order of trustworthiness:
 *
 *   1. today's stored rate
 *   2. a live fetch (throttled), then today's stored rate again
 *   3. the most recent stored rate still inside max_stale_days
 *   4. the configured fallback, if the committee set one
 *
 * and refuses to price the order at all if none of them yield a rate, rather
 * than inventing one.
 */
class ExchangeRateProvider
{
    private const THROTTLE_KEY = 'forex:nrb:last-attempt';

    public function __construct(private NrbForexClient $client) {}

    /**
     * @return array{rate: float, date: string, source: string, stale: bool}
     *
     * @throws ForexException
     */
    public function rateFor(string $currency, ?CarbonImmutable $on = null): array
    {
        $currency = strtoupper($currency);
        $on       = $on ?: CarbonImmutable::now();
        $type     = (string) config('forex.rate_type', 'sell');

        if ($stored = $this->stored($currency, $on)) {
            if ($this->isCurrent($stored, $on)) {
                return $this->result($stored->perUnit($type), $stored->rate_date, 'nrb', false);
            }
        }

        // Nothing for today. Try to top up, but no more often than the
        // throttle allows — NRB does not publish every day, and an outage must
        // not become one outbound request per page view.
        if ($this->shouldAttemptFetch()) {
            $this->refresh($on);

            $stored = $this->stored($currency, $on);

            if ($stored && $this->isCurrent($stored, $on)) {
                return $this->result($stored->perUnit($type), $stored->rate_date, 'nrb', false);
            }
        }

        if ($stored && $this->withinStaleWindow($stored, $on)) {
            Log::warning('Pricing a payment at a stale NRB rate.', [
                'currency'  => $currency,
                'rate_date' => $stored->rate_date->toDateString(),
                'today'     => $on->toDateString(),
            ]);

            return $this->result($stored->perUnit($type), $stored->rate_date, 'nrb-stale', true);
        }

        $fallback = (float) config("forex.fallback_rates.$currency", 0);

        if ($fallback > 0) {
            Log::warning('No usable NRB rate; falling back to the configured rate.', [
                'currency' => $currency,
                'rate'     => $fallback,
            ]);

            return $this->result($fallback, $on, 'fallback', true);
        }

        throw ForexException::withContext(
            "No usable exchange rate is available for {$currency}.",
            [
                'currency'       => $currency,
                'newest_stored'  => $stored?->rate_date?->toDateString(),
                'max_stale_days' => (int) config('forex.max_stale_days', 7),
            ]
        );
    }

    /**
     * Pull the recent window from NRB into storage. Failures are logged and
     * swallowed: the caller still has the stored rates to fall back on, and a
     * checkout should not die because NRB is down.
     */
    public function refresh(?CarbonImmutable $on = null): void
    {
        $on = $on ?: CarbonImmutable::now();

        Cache::put(self::THROTTLE_KEY, $on->timestamp, (int) config('forex.refresh_throttle', 900));

        try {
            $this->client->sync($on->subDays((int) config('forex.lookback_days', 7)), $on);
        } catch (ForexException $e) {
            Log::warning('NRB forex refresh failed: ' . $e->getMessage(), $e->context);
        }
    }

    /**
     * The newest stored rate on or before the given day. Rates published ahead
     * of time are ignored, so a payment is never priced at a future rate.
     */
    private function stored(string $currency, CarbonImmutable $on): ?ForexRate
    {
        return ForexRate::forCurrency($currency)
            ->whereDate('rate_date', '<=', $on->toDateString())
            ->orderByDesc('rate_date')
            ->first();
    }

    private function isCurrent(ForexRate $rate, CarbonImmutable $on): bool
    {
        return $rate->rate_date->isSameDay($on);
    }

    private function withinStaleWindow(ForexRate $rate, CarbonImmutable $on): bool
    {
        return $rate->rate_date->diffInDays($on) <= (int) config('forex.max_stale_days', 7);
    }

    private function shouldAttemptFetch(): bool
    {
        return ! Cache::has(self::THROTTLE_KEY);
    }

    /**
     * @param  CarbonInterface|string  $date
     * @return array{rate: float, date: string, source: string, stale: bool}
     */
    private function result(float $rate, $date, string $source, bool $stale): array
    {
        $rate *= 1 + ((float) config('forex.markup_percent', 0) / 100);

        return [
            'rate'   => round($rate, 6),
            'date'   => $date instanceof CarbonInterface ? $date->toDateString() : (string) $date,
            'source' => $source,
            'stale'  => $stale,
        ];
    }
}
