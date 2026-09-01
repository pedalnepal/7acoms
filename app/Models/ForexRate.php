<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * One currency's buying and selling rate against the Nepali rupee for one day,
 * as published by Nepal Rastra Bank.
 *
 * Stored rather than fetched on demand so that a payment can be priced while
 * NRB is unreachable, and so the rate applied to a past payment stays on
 * record for reconciliation.
 */
class ForexRate extends Model
{
    protected $table = 'forex_rates';

    protected $fillable = [
        'currency_iso3', 'currency_name', 'unit', 'buy', 'sell',
        'rate_date', 'published_on', 'modified_on',
    ];

    protected $casts = [
        'unit'         => 'integer',
        'buy'          => 'decimal:6',
        'sell'         => 'decimal:6',
        'rate_date'    => 'date',
        'published_on' => 'date',
        'modified_on'  => 'date',
    ];

    public function scopeForCurrency(Builder $query, string $iso3): Builder
    {
        return $query->where('currency_iso3', strtoupper($iso3));
    }

    /**
     * Rates per single unit of the foreign currency. NRB quotes some
     * currencies in blocks — JPY per 10, INR per 100 — so the stored figures
     * mean nothing without this division.
     */
    public function perUnit(string $type = 'sell'): float
    {
        $unit = max(1, (int) $this->unit);

        return (float) $this->{$type} / $unit;
    }
}
