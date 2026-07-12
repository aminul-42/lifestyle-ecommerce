<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Product extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'category_id', 'name', 'slug', 'sku', 'gender', 'short_description', 'description',
        'price', 'discount_price', 'stock', 'has_variants', 'weight',
        'is_featured', 'is_active', 'meta_title', 'meta_description', 'views',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'discount_price' => 'decimal:2',
        'has_variants' => 'boolean',
        'is_featured' => 'boolean',
        'is_active' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::creating(function (Product $product) {
            if (empty($product->slug)) {
                $product->slug = Str::slug($product->name).'-'.Str::random(5);
            }
        });
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class)->orderBy('sort_order');
    }

    public function variants(): HasMany
    {
        return $this->hasMany(ProductVariant::class);
    }

    public function activeVariants(): HasMany
    {
        return $this->hasMany(ProductVariant::class)->where('is_active', true)->where('stock', '>', 0);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class)->where('is_approved', true);
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    // ---- Accessors ----

    public function getPrimaryImageAttribute(): string
    {
        $img = $this->images->firstWhere('is_primary', true) ?? $this->images->first();

        return $img ? \Storage::disk('public')->url($img->path) : asset('images/placeholder.png');
    }

    /** Final selling price (discount price if set, else regular price) */
    public function getFinalPriceAttribute(): float
    {
        return (float) ($this->discount_price ?? $this->price);
    }

    public function getDiscountPercentAttribute(): ?int
    {
        if (! $this->discount_price || $this->discount_price >= $this->price) {
            return null;
        }

        return (int) round((($this->price - $this->discount_price) / $this->price) * 100);
    }

    public function getAverageRatingAttribute(): float
    {
        return round($this->reviews()->avg('rating') ?? 0, 1);
    }

    public function getIsInStockAttribute(): bool
    {
        return $this->has_variants
            ? $this->variants()->where('stock', '>', 0)->exists()
            : $this->stock > 0;
    }

    // ---- Scopes ----

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeGender($query, string $gender)
    {
        return $query->where(function ($q) use ($gender) {
            $q->where('gender', $gender)->orWhere('gender', 'unisex');
        });
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }
}