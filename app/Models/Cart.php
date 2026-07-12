<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Cart extends Model
{
    protected $fillable = ['user_id', 'cart_token', 'product_id', 'product_variant_id', 'quantity'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function variant(): BelongsTo
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_id');
    }

    /** Unit price for this cart line (variant override or product price) */public function getUnitPriceAttribute(): ?float
{
    if ($this->product_variant_id) {
        return $this->variant?->final_price;
    }

    return $this->product?->final_price;
}

public function getLineTotalAttribute(): float
{
    return $this->unit_price !== null ? $this->unit_price * $this->quantity : 0;
}
}