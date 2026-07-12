<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Coupon extends Model
{
    protected $fillable = [
        'code', 'type', 'value', 'min_order_amount', 'usage_limit', 'used_count', 'expires_at', 'is_active',
    ];

    protected $casts = [
        'expires_at' => 'date',
        'is_active' => 'boolean',
    ];

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function isValid(float $orderAmount): bool
    {
        if (! $this->is_active) {
            return false;
        }

        if ($this->expires_at && $this->expires_at->isPast()) {
            return false;
        }

        if ($this->usage_limit !== null && $this->used_count >= $this->usage_limit) {
            return false;
        }

        if ($orderAmount < $this->min_order_amount) {
            return false;
        }

        return true;
    }

    public function calculateDiscount(float $orderAmount): float
    {
        if ($this->type === 'percent') {
            return round($orderAmount * ($this->value / 100), 2);
        }

        return min($this->value, $orderAmount); // fixed, but never exceed order total
    }
}