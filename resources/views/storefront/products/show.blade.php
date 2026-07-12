@extends('layouts.storefront')

@section('title', $product->name . ' — ' . setting('site_name', config('app.name')))

@push('styles')
<style>
    .pd-wrap { display: grid; grid-template-columns: 1fr 1fr; gap: 50px; margin-bottom: 60px; }

    .pd-gallery-main { width: 100%; aspect-ratio: 4/5; border-radius: 18px; overflow: hidden; background: var(--primary-soft2); display: flex; align-items: center; justify-content: center; border: 1px solid var(--stone); }
    .pd-gallery-main img { width: 100%; height: 100%; object-fit: cover; }
    .pd-gallery-main svg { width: 60px; height: 60px; color: var(--mute); opacity: 0.4; }
    .pd-thumbs { display: flex; gap: 10px; margin-top: 12px; flex-wrap: wrap; }
    .pd-thumb { width: 66px; height: 66px; border-radius: 10px; overflow: hidden; border: 2px solid var(--stone); cursor: pointer; opacity: 0.7; transition: all 0.2s; }
    .pd-thumb.active { border-color: var(--accent); opacity: 1; }
    .pd-thumb img { width: 100%; height: 100%; object-fit: cover; }

    .pd-info .tag-badge { font-size: 11px; }
    .pd-info h1 { font-family: 'Fraunces', Georgia, serif; font-size: clamp(26px, 3.4vw, 36px); font-weight: 500; color: var(--ink); margin: 8px 0 14px; line-height: 1.15; }
    .pd-price-row { display: flex; align-items: baseline; gap: 10px; margin-bottom: 18px; }
    .pd-price { font-family: 'JetBrains Mono', monospace; font-weight: 700; font-size: 24px; color: var(--ink); }
    .pd-price-old { font-family: 'JetBrains Mono', monospace; font-size: 16px; color: var(--mute); text-decoration: line-through; }
    .pd-desc { font-size: 14.5px; color: var(--mute); line-height: 1.7; margin-bottom: 26px; }

    .pd-variant-group { margin-bottom: 20px; }
    .pd-variant-label { font-family: 'JetBrains Mono', monospace; font-size: 11px; text-transform: uppercase; letter-spacing: 0.08em; color: var(--mute); display: block; margin-bottom: 10px; }
    .pd-variant-options { display: flex; gap: 8px; flex-wrap: wrap; }
    .pd-opt {
        border: 1.5px solid var(--stone); border-radius: 8px; padding: 8px 16px; font-size: 13px; font-weight: 600;
        background: #fff; cursor: pointer; color: var(--ink); transition: all 0.15s;
    }
    .pd-opt:hover { border-color: var(--ink); }
    .pd-opt.selected { border-color: var(--ink); background: var(--ink); color: #fff; }
    .pd-opt.unavailable { opacity: 0.35; cursor: not-allowed; text-decoration: line-through; }

    .pd-stock { font-size: 13px; font-weight: 600; margin-bottom: 22px; display: inline-flex; align-items: center; gap: 6px; }
    .pd-stock.in { color: #166534; }
    .pd-stock.low { color: #92400e; }
    .pd-stock.out { color: #991b1b; }
    .pd-stock::before { content: ''; width: 7px; height: 7px; border-radius: 50%; background: currentColor; }

    .pd-qty-row { display: flex; align-items: center; gap: 14px; margin-bottom: 22px; }
    .pd-qty-stepper { display: flex; align-items: center; border: 1.5px solid var(--stone); border-radius: 999px; overflow: hidden; }
    .pd-qty-stepper button { width: 36px; height: 36px; border: none; background: #fff; font-size: 16px; cursor: pointer; color: var(--ink); }
    .pd-qty-stepper span { width: 36px; text-align: center; font-family: 'JetBrains Mono', monospace; font-weight: 600; }

    .pd-actions { display: flex; gap: 12px; flex-wrap: wrap; }
    .pd-actions .btn-primary { padding: 12px 28px; font-size: 15px; }

    .pd-meta-row { display: flex; gap: 20px; margin-top: 26px; padding-top: 20px; border-top: 1px solid var(--stone); font-size: 12.5px; color: var(--mute); flex-wrap: wrap; }
    .pd-meta-row span { display: flex; align-items: center; gap: 6px; }
    .pd-meta-row svg { width: 15px; height: 15px; color: var(--accent); }

    /* Reviews */
    .review-summary { display: flex; align-items: center; gap: 24px; margin-bottom: 26px; flex-wrap: wrap; }
    .review-score { font-family: 'Fraunces', Georgia, serif; font-size: 46px; font-weight: 600; color: var(--ink); line-height: 1; }
    .review-stars { color: #f59e0b; font-size: 15px; letter-spacing: 1px; }
    .review-item { padding: 18px 0; border-bottom: 1px solid var(--stone); }
    .review-item:last-child { border-bottom: none; }
    .review-item-head { display: flex; align-items: center; justify-content: space-between; margin-bottom: 6px; flex-wrap: wrap; gap: 6px; }
    .review-avatar { width: 32px; height: 32px; border-radius: 50%; background: var(--primary); color: #fff; display: inline-flex; align-items: center; justify-content: center; font-size: 12px; font-weight: 700; margin-right: 8px; }
    .review-name { font-weight: 600; font-size: 13.5px; }
    .verified-chip { font-family: 'JetBrains Mono', monospace; font-size: 10px; background: var(--accent-soft); color: var(--ink); padding: 2px 8px; border-radius: 999px; margin-left: 8px; }
    .review-date { font-size: 12px; color: var(--mute); }
    .review-text { font-size: 14px; color: #374151; line-height: 1.6; margin-top: 4px; }
    .no-reviews { color: var(--mute); font-size: 14px; padding: 20px 0; }

    @media (max-width: 860px) {
        .pd-wrap { grid-template-columns: 1fr; gap: 30px; }
    }
</style>
@endpush

@section('content')

    @php
        $activeVariants = $product->has_variants ? $product->variants : collect();
        $sizes = $activeVariants->pluck('size')->filter()->unique()->values();
        $colors = $activeVariants->pluck('color')->filter()->unique()->values();
        $basePrice = $product->discount_price ?? $product->price;
        $baseStock = $product->stock;
    @endphp

    <div class="crumb reveal">
        <a href="{{ route('home') }}">Home</a><span>/</span>
        <a href="{{ route('shop.index') }}">Shop</a>
        @if($product->category)
            <span>/</span><a href="{{ route('shop.index', ['category' => $product->category_id]) }}">{{ $product->category->name }}</a>
        @endif
        <span>/</span>{{ $product->name }}
    </div>

    <div class="pd-wrap reveal">
        {{-- ── Gallery ─────────────────────────────────── --}}
        <div>
            <div class="pd-gallery-main" id="pdMainImage">
                @if($product->images->isNotEmpty())
                    <img src="{{ Storage::disk('public')->url($product->images->first()->path) }}" alt="{{ $product->name }}" id="pdMainImg">
                @else
                    <svg fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M20.38 3.46L16 2a4 4 0 01-8 0L3.62 3.46a2 2 0 00-1.34 2.23l.58 3.47a1 1 0 00.99.84H6v10a2 2 0 002 2h8a2 2 0 002-2V10h2.15a1 1 0 00.99-.84l.58-3.47a2 2 0 00-1.34-2.23z"/></svg>
                @endif
            </div>
            @if($product->images->count() > 1)
                <div class="pd-thumbs">
                    @foreach($product->images as $i => $img)
                        <div class="pd-thumb {{ $i === 0 ? 'active' : '' }}" onclick="pdSwapImage(this, '{{ Storage::disk('public')->url($img->path) }}')">
                            <img src="{{ Storage::disk('public')->url($img->path) }}" alt="">
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- ── Info ────────────────────────────────────── --}}
        <div class="pd-info">
            <span class="tag-badge">{{ ucfirst($product->gender) }}{{ $product->category ? ' · ' . $product->category->name : '' }}</span>
            <h1>{{ $product->name }}</h1>

            <div class="pd-price-row" id="pdPriceRow">
                <span class="pd-price" id="pdPrice">{{ money($basePrice) }}</span>
                @if(!$product->has_variants && $product->discount_price)
                    <span class="pd-price-old">{{ money($product->price) }}</span>
                @endif
            </div>

            @if($product->short_description)
                <p class="pd-desc">{{ $product->short_description }}</p>
            @endif

            @if($product->has_variants)
                @php
                    $variantsForJs = $activeVariants->map(function ($v) {
                        return [
                            'id' => $v->id,
                            'size' => $v->size,
                            'color' => $v->color,
                            'price' => $v->price ?? null,
                            'discount_price' => $v->discount_price ?? null,
                            'stock' => $v->stock ?? 0,
                        ];
                    })->values();
                @endphp
                <div id="pdVariantData" style="display:none;"
                     data-variants="{{ $variantsForJs->toJson() }}"
                     data-base-price="{{ $basePrice }}"></div>

                @if($sizes->isNotEmpty())
                    <div class="pd-variant-group">
                        <span class="pd-variant-label">Size</span>
                        <div class="pd-variant-options" id="pdSizeOptions">
                            @foreach($sizes as $size)
                                <button type="button" class="pd-opt" data-size="{{ $size }}" onclick="pdSelectSize(this)">{{ $size }}</button>
                            @endforeach
                        </div>
                    </div>
                @endif

                @if($colors->isNotEmpty())
                    <div class="pd-variant-group">
                        <span class="pd-variant-label">Color</span>
                        <div class="pd-variant-options" id="pdColorOptions">
                            @foreach($colors as $color)
                                <button type="button" class="pd-opt" data-color="{{ $color }}" onclick="pdSelectColor(this)">{{ $color }}</button>
                            @endforeach
                        </div>
                    </div>
                @endif
            @endif

            <div>
                <span class="pd-stock in" id="pdStockLabel">
                    {{ $baseStock > 10 ? 'In stock' : ($baseStock > 0 ? 'Only ' . $baseStock . ' left' : 'Out of stock') }}
                </span>
            </div>

            <div class="pd-qty-row">
                <div class="pd-qty-stepper">
                    <button type="button" onclick="pdChangeQty(-1)">−</button>
                    <span id="pdQty">1</span>
                    <button type="button" onclick="pdChangeQty(1)">+</button>
                </div>
            </div>

            <div class="pd-actions">
                <button type="button" class="btn btn-primary" id="pdAddToCartBtn" onclick="pdAddToCart({{ $product->id }}, {{ $product->has_variants ? 'true' : 'false' }})">Add to Cart</button>
                <button type="button" class="btn-ghost" onclick="showToast('Wishlist is coming soon!')">Save for Later</button>
            </div>

            <div class="pd-meta-row">
                @if(setting('cod_enabled') == '1')
                    <span><svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg> Cash on delivery available</span>
                @endif
                <span><svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg> Manual payment verified fast</span>
            </div>
        </div>
    </div>

    {{-- ── Description ──────────────────────────────────── --}}
    @if($product->description)
        <section class="reveal" style="margin-bottom:60px; max-width:760px;">
            <div class="sec-head"><h2>The Details</h2></div>
            <p style="font-size:14.5px; color:#374151; line-height:1.8;">{{ $product->description }}</p>
        </section>
    @endif

    {{-- ── Reviews ───────────────────────────────────────── --}}
    <section class="reveal" style="margin-bottom:60px;">
        <div class="sec-head">
            <h2>Customer Reviews</h2>
            <p>Honest fit notes from people who bought it.</p>
        </div>

        @if($reviewsCount > 0)
            <div class="review-summary">
                <span class="review-score">{{ $avgRating }}</span>
                <div>
                    <div class="review-stars">{{ str_repeat('★', round($avgRating)) }}{{ str_repeat('☆', 5 - round($avgRating)) }}</div>
                    <span style="font-size:13px; color:var(--mute);">Based on {{ $reviewsCount }} {{ Str::plural('review', $reviewsCount) }}</span>
                </div>
            </div>

            @foreach($reviews as $review)
                <div class="review-item">
                    <div class="review-item-head">
                        <div style="display:flex; align-items:center;">
                            <span class="review-avatar">{{ strtoupper(substr($review->user->name ?? 'U', 0, 1)) }}</span>
                            <span class="review-name">{{ $review->user->name ?? 'Customer' }}</span>
                            @if($review->order_id)
                                <span class="verified-chip">Verified Purchase</span>
                            @endif
                        </div>
                        <span class="review-date">{{ $review->created_at->format('d M, Y') }}</span>
                    </div>
                    <div class="review-stars" style="font-size:13px;">{{ str_repeat('★', $review->rating) }}{{ str_repeat('☆', 5 - $review->rating) }}</div>
                    @if($review->comment)
                        <p class="review-text">{{ $review->comment }}</p>
                    @endif
                </div>
            @endforeach

            @if($reviews->hasPages())
                <div class="shop-pagination" style="justify-content:flex-start; margin-top:20px;">
                    @if ($reviews->onFirstPage())
                        <span class="disabled">‹</span>
                    @else
                        <a href="{{ $reviews->previousPageUrl() }}">‹</a>
                    @endif
                    @if ($reviews->hasMorePages())
                        <a href="{{ $reviews->nextPageUrl() }}">›</a>
                    @else
                        <span class="disabled">›</span>
                    @endif
                </div>
            @endif
        @else
            <p class="no-reviews">No reviews yet — be the first to share how this fits.</p>
        @endif
    </section>

    {{-- ── Related products ─────────────────────────────── --}}
    @if($related->isNotEmpty())
        <section class="reveal">
            <div class="sec-head">
                <h2>You Might Also Like</h2>
                <p>More from the same category.</p>
            </div>
            <div class="tag-row">
                @foreach($related as $rp)
                    @include('storefront.partials.product-tag-card', ['product' => $rp])
                @endforeach
            </div>
        </section>
    @endif

@endsection

@push('scripts')
<script>
    let pdSelectedSize = null;
    let pdSelectedColor = null;
    let pdSelectedVariantId = null;
    let pdQty = 1;

    function pdSwapImage(el, url) {
        document.getElementById('pdMainImg').src = url;
        document.querySelectorAll('.pd-thumb').forEach(t => t.classList.remove('active'));
        el.classList.add('active');
    }

    function pdSelectSize(el) {
        document.querySelectorAll('#pdSizeOptions .pd-opt').forEach(b => b.classList.remove('selected'));
        el.classList.add('selected');
        pdSelectedSize = el.dataset.size;
        pdUpdateVariant();
    }

    function pdSelectColor(el) {
        document.querySelectorAll('#pdColorOptions .pd-opt').forEach(b => b.classList.remove('selected'));
        el.classList.add('selected');
        pdSelectedColor = el.dataset.color;
        pdUpdateVariant();
    }

    function pdUpdateVariant() {
        const dataEl = document.getElementById('pdVariantData');
        if (!dataEl) return;

        const variants = JSON.parse(dataEl.dataset.variants || '[]');
        const basePrice = parseFloat(dataEl.dataset.basePrice);

        const match = variants.find(v =>
            (pdSelectedSize === null || v.size === pdSelectedSize) &&
            (pdSelectedColor === null || v.color === pdSelectedColor)
        );

        const priceEl = document.getElementById('pdPrice');
        const stockEl = document.getElementById('pdStockLabel');

        if (match) {
            pdSelectedVariantId = match.id;
            const price = match.discount_price ?? match.price ?? basePrice;
            priceEl.textContent = formatMoney(price);

            const stock = match.stock ?? 0;
            stockEl.className = 'pd-stock ' + (stock > 10 ? 'in' : stock > 0 ? 'low' : 'out');
            stockEl.textContent = stock > 10 ? 'In stock' : stock > 0 ? `Only ${stock} left` : 'Out of stock';
        } else {
            pdSelectedVariantId = null;
            priceEl.textContent = formatMoney(basePrice);
        }
    }

    async function parseJson(res) { try { return (await res.json()) ?? {}; } catch { return {}; } }

    async function pdAddToCart(productId, hasVariants) {
        if (hasVariants && !pdSelectedVariantId) {
            showToast('Please select a size/color first.', 'error');
            return;
        }

        const btn = document.getElementById('pdAddToCartBtn');
        btn.disabled = true;
        const originalText = btn.textContent;
        btn.textContent = 'Adding...';

        try {
            const res = await fetch("{{ route('cart.store') }}", {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json', 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    product_id: productId,
                    product_variant_id: pdSelectedVariantId,
                    quantity: pdQty,
                }),
            });
            const json = await parseJson(res);

            if (!res.ok) {
                showToast(json.message || 'Could not add to cart.', 'error');
                return;
            }

            showToast(json.message || 'Added to cart!');
        } catch (err) {
    console.error('Add to cart error:', err);
    showToast('Network error — please try again.', 'error');
} finally {
            btn.disabled = false;
            btn.textContent = originalText;
        }
    }

    function formatMoney(amount) {
        const symbol = "{{ setting('currency_symbol', '৳') }}";
        return symbol + ' ' + Number(amount).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }

    function pdChangeQty(delta) {
        pdQty = Math.max(1, pdQty + delta);
        document.getElementById('pdQty').textContent = pdQty;
    }
</script>
@endpush