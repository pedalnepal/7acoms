<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

// NRB publishes the day's rates in the morning. Fetching them ahead of the
// day's payments keeps checkout off the live API; the provider still tops up
// on demand if this run is ever missed.
Schedule::command('forex:sync')
    ->dailyAt('09:15')
    ->timezone('Asia/Kathmandu')
    ->withoutOverlapping();
