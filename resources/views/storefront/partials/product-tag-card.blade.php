@php
    $activeVariants = $product->has_variants ? $product->variants->where('is_active', true) : collect();
    $variantPrices = $activeVariants->map(fn($v) => $v->discount_price ?? $v->price ?? null)->filter()->values();
    $effectivePrice = $product->has_variants
        ? ($variantPrices->isNotEmpty() ? $variantPrices->min() : ($product->discount_price ?? $product->price))
        : ($product->discount_price ?? $product->price);
    $effectiveStock = $product->has_variants ? $activeVariants->sum('stock') : $product->stock;
    $primaryImage = $product->images->first();
@endphp
<a href="{{ route('shop.show', $product->slug) }}" class="tag-card" style="display:block;">
    @if($effectiveStock <= 0)
        <span class="sold-out-strip">Sold Out</span>
    @endif
    <div class="tag-card-img">
        @if($primaryImage)
            <img src="{{ Storage::disk('public')->url($primaryImage->path) }}" alt="{{ $product->name }}">
        @else
            <svg fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M20.38 3.46L16 2a4 4 0 01-8 0L3.62 3.46a2 2 0 00-1.34 2.23l.58 3.47a1 1 0 00.99.84H6v10a2 2 0 002 2h8a2 2 0 002-2V10h2.15a1 1 0 00.99-.84l.58-3.47a2 2 0 00-1.34-2.23z"/></svg>
        @endif
    </div>
    <div class="tag-card-body">
        <span class="tag-badge">{{ ucfirst($product->gender) }}{{ $product->has_variants ? ' · Multiple Sizes' : '' }}</span>
        <h4>{{ $product->name }}</h4>
        <div class="tag-price-row">
            @if($product->has_variants)
                <span class="tag-price">From {{ money($effectivePrice) }}</span>
            @else
                <span class="tag-price">{{ money($effectivePrice) }}</span>
                @if($product->discount_price)
                    <span class="tag-price-old">{{ money($product->price) }}</span>
                @endif
            @endif
        </div>
    </div>
</a>