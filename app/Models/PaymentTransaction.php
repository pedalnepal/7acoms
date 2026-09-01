<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PaymentTransaction extends Model
{
    protected $table = 'payment_transactions';

    protected $fillable = [
        'registration_id', 'reference', 'transaction_id', 'status', 'amount', 'currency',
        'presentment_amount', 'presentment_currency', 'fx_rate', 'fx_rate_date',
        'payment_type', 'card_type', 'card_masked', 'authenticated',
        'reason_code', 'message', 'response',
    ];

    protected $casts = [
        'response'           => 'array',
        'authenticated'      => 'boolean',
        'amount'             => 'decimal:2',
        'presentment_amount' => 'decimal:2',
        'fx_rate'            => 'decimal:6',
        'fx_rate_date'       => 'date',
    ];

    /**
     * Whether this charge was taken in a different currency from the fee it
     * settles — the amount/currency here are always what the gateway charged.
     */
    public function wasConverted(): bool
    {
        return $this->presentment_currency !== null
            && $this->presentment_currency !== $this->currency;
    }

    public function registration(): BelongsTo
    {
        return $this->belongsTo(Registration::class);
    }

    public function isSuccessful(): bool
    {
        return in_array($this->status, ['AUTHORIZED', 'PENDING'], true);
    }
}
