<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Str;

class Order extends Model
{
    protected $fillable = [
        'order_number', 'user_id', 'coupon_id',
        'full_name', 'phone', 'address_line', 'city', 'area', 'postal_code', 'order_note',
        'subtotal', 'discount', 'shipping_fee', 'total',
        'payment_method', 'payment_status', 'status', 'tracking_number',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'discount' => 'decimal:2',
        'shipping_fee' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    protected static function booted(): void
    {
        static::creating(function (Order $order) {
            if (empty($order->order_number)) {
                $order->order_number = 'ORD-'.date('Y').'-'.strtoupper(Str::random(6));
            }
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function coupon(): BelongsTo
    {
        return $this->belongsTo(Coupon::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function paymentProofs(): HasMany
    {
        return $this->hasMany(PaymentProof::class);
    }

    /** The most recent payment proof submitted for this order */
    public function latestPaymentProof(): HasOne
    {
        return $this->hasOne(PaymentProof::class)->latestOfMany();
    }

    // ---- Status helpers (used in Blade & controllers) ----

    public function isPaymentPending(): bool
    {
        return $this->payment_status === 'pending';
    }

    public function isAwaitingVerification(): bool
    {
        return $this->payment_status === 'submitted';
    }

    public function isVerified(): bool
    {
        return $this->payment_status === 'verified';
    }

   public function statusBadgeColor(): string
{
    return match ($this->status) {
        'processing' => 'yellow',
        'confirmed' => 'blue',
        'shipped' => 'purple',
        'delivered' => 'green',
        'cancelled' => 'red',
        default => 'gray',
    };
}

public function paymentBadgeColor(): string
{
    return match ($this->payment_status) {
        'pending' => 'gray',
        'submitted' => 'yellow',
        'verified' => 'green',
        'rejected' => 'red',
        default => 'gray',
    };
}

public function getRouteKeyName()
{
    return 'order_number';
}
}