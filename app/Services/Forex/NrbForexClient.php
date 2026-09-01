<?php

namespace App\Services\Forex;

use App\Models\ForexRate;
use Carbon\CarbonImmutable;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;

/**
 * The Nepal Rastra Bank foreign exchange API.
 *
 * GET /rates requires all four of page, per_page, from and to. It is also
 * unusual in answering HTTP 200 for a rejected request, putting the real
 * outcome in the body's status.code — so the body has to be inspected even
 * when the transport succeeded.
 */
class NrbForexClient
{
    /**
     * Every rate NRB published in the given range, oldest first.
     *
     * @return array<int, array{
     *     date: string,
     *     published_on: ?string,
     *     modified_on: ?string,
     *     rates: array<int, array{iso3: string, name: ?string, unit: int, buy: float, sell: float}>
     * }>
     *
     * @throws ForexException
     */
    public function ratesBetween(CarbonImmutable $from, CarbonImmutable $to): array
    {
        $days     = [];
        $page     = 1;
        $lastPage = 1;

        // A range is normally one page, but a wide backfill is not — so follow
        // the pagination the response reports rather than assuming.
        do {
            $body = $this->request($from, $to, $page);

            foreach ($body['data']['payload'] ?? [] as $day) {
                if ($parsed = $this->parseDay($day)) {
                    $days[] = $parsed;
                }
            }

            $lastPage = (int) ($body['pagination']['pages'] ?? 1);
            $page++;
        } while ($page <= $lastPage && $page <= 50);

        usort($days, static fn ($a, $b) => strcmp($a['date'], $b['date']));

        return $days;
    }

    /**
     * Fetch a range and store it, returning how many rate rows were written.
     *
     * @throws ForexException
     */
    public function sync(CarbonImmutable $from, CarbonImmutable $to): int
    {
        $written = 0;

        foreach ($this->ratesBetween($from, $to) as $day) {
            foreach ($day['rates'] as $rate) {
                // Matched with whereDate rather than updateOrCreate: a date
                // column is written as a full datetime by the model's date
                // cast, which an equality match on 'Y-m-d' then misses — and
                // the re-insert trips the unique index.
                $stored = ForexRate::forCurrency($rate['iso3'])
                    ->whereDate('rate_date', $day['date'])
                    ->first()
                    ?: new ForexRate([
                        'currency_iso3' => $rate['iso3'],
                        'rate_date'     => $day['date'],
                    ]);

                $stored->fill([
                    'currency_name' => $rate['name'],
                    'unit'          => $rate['unit'],
                    'buy'           => $rate['buy'],
                    'sell'          => $rate['sell'],
                    'published_on'  => $day['published_on'],
                    'modified_on'   => $day['modified_on'],
                ])->save();

                $written++;
            }
        }

        return $written;
    }

    /**
     * @return array<string, mixed>
     *
     * @throws ForexException
     */
    private function request(CarbonImmutable $from, CarbonImmutable $to, int $page): array
    {
        $query = [
            'page'     => $page,
            'per_page' => (int) config('forex.nrb.per_page', 100),
            'from'     => $from->toDateString(),
            'to'       => $to->toDateString(),
        ];

        try {
            $response = Http::timeout((int) config('forex.nrb.timeout', 10))
                ->acceptJson()
                // NRB's TLS layer drops roughly one connection in three
                // ("server closed abruptly / missing close_notify"), which
                // plain curl reproduces just as readily. Only the dropped
                // connection is retried — a real error status is left to the
                // caller rather than hammered.
                ->retry(
                    (int) config('forex.nrb.retries', 3),
                    (int) config('forex.nrb.retry_delay', 300),
                    fn ($e) => $e instanceof ConnectionException,
                    throw: false
                )
                ->get($this->endpoint(), $query);
        } catch (\Throwable $e) {
            throw ForexException::withContext(
                'Could not reach the Nepal Rastra Bank forex API: ' . $e->getMessage(),
                $query
            );
        }

        if (! $response->successful()) {
            throw ForexException::withContext(
                'The Nepal Rastra Bank forex API returned HTTP ' . $response->status() . '.',
                $query
            );
        }

        $body = $response->json();

        if (! is_array($body)) {
            throw ForexException::withContext('The Nepal Rastra Bank forex API returned an unreadable body.', $query);
        }

        // A 200 with status.code 400 is how this API reports bad parameters.
        $code = (int) ($body['status']['code'] ?? 0);

        if ($code !== 200) {
            throw ForexException::withContext(
                'The Nepal Rastra Bank forex API rejected the request (status ' . $code . ').',
                $query + ['validation' => $body['errors']['validation'] ?? null]
            );
        }

        return $body;
    }

    private function endpoint(): string
    {
        return rtrim((string) config('forex.nrb.base_url'), '/') . '/rates';
    }

    /**
     * @param  mixed  $day
     * @return array<string, mixed>|null
     */
    private function parseDay($day): ?array
    {
        if (! is_array($day) || empty($day['date'])) {
            return null;
        }

        $rates = [];

        foreach ($day['rates'] ?? [] as $rate) {
            // The published docs name this field ISO3 while the live API
            // returns iso3; accept either rather than depend on the casing.
            $currency = $rate['currency'] ?? [];
            $iso3     = strtoupper((string) ($currency['iso3'] ?? $currency['ISO3'] ?? ''));

            if (strlen($iso3) !== 3 || ! isset($rate['buy'], $rate['sell'])) {
                continue;
            }

            $rates[] = [
                'iso3' => $iso3,
                'name' => $currency['name'] ?? null,
                'unit' => max(1, (int) ($currency['unit'] ?? 1)),
                'buy'  => (float) $rate['buy'],
                'sell' => (float) $rate['sell'],
            ];
        }

        if (! $rates) {
            return null;
        }

        return [
            'date'         => CarbonImmutable::parse($day['date'])->toDateString(),
            'published_on' => ! empty($day['published_on'])
                ? CarbonImmutable::parse($day['published_on'])->toDateString()
                : null,
            'modified_on' => ! empty($day['modified_on'])
                ? CarbonImmutable::parse($day['modified_on'])->toDateString()
                : null,
            'rates' => $rates,
        ];
    }
}
