<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Registration extends Model
{
    use SoftDeletes;

    /** Payment has not been attempted, or the last attempt did not go through. */
    public const PAYMENT_UNPAID = 'unpaid';

    /** Accepted by the gateway but held for review or settlement. */
    public const PAYMENT_PENDING = 'pending';

    /** Funds authorised (and captured, unless the integration is auth-only). */
    public const PAYMENT_PAID = 'paid';

    /** The gateway declined the last attempt. The delegate may retry. */
    public const PAYMENT_FAILED = 'failed';

    protected $table = 'registrations';

    protected $fillable = [
        'reg_date', 'full_name', 'email', 'phone', 'designation', 'workplace',
        'id_card_name', 'id_card_path', 'nationality', 'naoms_member', 'member_id',
        'reg_for', 'accommodation', 'acc_rooms', 'acc_type', 'accompanying', 'acp_count',
        'category', 'receipt_name', 'receipt_path', 'others', 'status',
        'payment_reference', 'payment_status', 'amount', 'currency', 'fee_tier',
        'fee_breakdown', 'paid_at',
    ];

    protected $dates = ['deleted_at'];

    protected $casts = [
        'fee_breakdown' => 'array',
        'paid_at'       => 'datetime',
        'amount'        => 'decimal:2',
    ];

    public function transactions(): HasMany
    {
        return $this->hasMany(PaymentTransaction::class);
    }

    public function isPaid(): bool
    {
        return $this->payment_status === self::PAYMENT_PAID;
    }

    /**
     * A payment already accepted by the gateway must not be charged again,
     * whether it settled outright or is still under review.
     */
    public function isPaymentSettledOrPending(): bool
    {
        return in_array($this->payment_status, [self::PAYMENT_PAID, self::PAYMENT_PENDING], true);
    }

    /**
     * The reference sent to the gateway, and shown to the delegate. Short
     * enough for the gateway's reference field and unique per registration.
     */
    public function paymentCode(): string
    {
        return 'ACOMS-' . str_pad((string) $this->id, 6, '0', STR_PAD_LEFT);
    }

    public function formattedAmount(): string
    {
        return $this->currency . ' ' . number_format((float) $this->amount, 2);
    }
}
