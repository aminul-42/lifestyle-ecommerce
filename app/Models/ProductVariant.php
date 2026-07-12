<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductVariant extends Model
{
    protected $fillable = [
        'product_id', 'size', 'color', 'sku', 'price', 'discount_price', 'stock', 'is_active',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'discount_price' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /** Falls back to parent product's price if this variant has no override */
    public function getFinalPriceAttribute(): float
    {
        if ($this->discount_price) {
            return (float) $this->discount_price;
        }

        return (float) ($this->price ?? $this->product->price);
    }

    public function getLabelAttribute(): string
    {
        return trim(collect([$this->size, $this->color])->filter()->implode(' / '));
    }
}