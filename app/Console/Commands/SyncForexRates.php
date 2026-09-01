<?php

namespace App\Console\Commands;

use App\Services\Forex\ForexException;
use App\Services\Forex\NrbForexClient;
use Carbon\CarbonImmutable;
use Illuminate\Console\Command;

class SyncForexRates extends Command
{
    protected $signature = 'forex:sync
                            {--days= : How many days back to fetch (defaults to forex.lookback_days)}
                            {--from= : Start date, Y-m-d, overriding --days}
                            {--to= : End date, Y-m-d (defaults to today)}';

    protected $description = 'Store the Nepal Rastra Bank exchange rates for a date range';

    public function handle(NrbForexClient $client): int
    {
        $to = $this->option('to')
            ? CarbonImmutable::parse($this->option('to'))
            : CarbonImmutable::now();

        $from = $this->option('from')
            ? CarbonImmutable::parse($this->option('from'))
            : $to->subDays((int) ($this->option('days') ?: config('forex.lookback_days', 7)));

        $this->info("Fetching NRB rates from {$from->toDateString()} to {$to->toDateString()}…");

        try {
            $written = $client->sync($from, $to);
        } catch (ForexException $e) {
            $this->error($e->getMessage());

            if ($e->context) {
                $this->line(json_encode($e->context));
            }

            return self::FAILURE;
        }

        $this->info("Stored {$written} rate(s).");

        return self::SUCCESS;
    }
}
