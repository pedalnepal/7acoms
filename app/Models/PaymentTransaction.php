<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PaymentTransaction extends Model
{
    protected $table = 'payment_transactions';

    protected $fillable = [
        'registration_id', 'reference', 'transaction_id', 'status', 'amount', 'currency',
        'payment_type', 'card_type', 'card_masked', 'authenticated',
        'reason_code', 'message', 'response',
    ];

    protected $casts = [
        'response'      => 'array',
        'authenticated' => 'boolean',
        'amount'        => 'decimal:2',
    ];

    public function registration(): BelongsTo
    {
        return $this->belongsTo(Registration::class);
    }

    public function isSuccessful(): bool
    {
        return in_array($this->status, ['AUTHORIZED', 'PENDING'], true);
    }
}
