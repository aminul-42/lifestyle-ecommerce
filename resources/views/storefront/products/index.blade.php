@extends('layouts.storefront')

@section('title', 'Shop — ' . setting('site_name', config('app.name')))

@push('styles')
<style>
    .shop-head { margin-bottom: 26px; }
    .shop-head h1 { font-family: 'Fraunces', Georgia, serif; font-size: clamp(28px, 4vw, 40px); font-weight: 500; color: var(--ink); margin-bottom: 8px; }
    .shop-head p { color: var(--mute); font-size: 14px; }

    .filter-bar {
        display: flex; flex-wrap: wrap; gap: 10px; align-items: center;
        padding: 14px; border: 1px solid var(--stone); border-radius: 16px; margin-bottom: 30px;
        background: #fff;
    }
    .filter-bar select, .filter-bar input[type="text"] {
        border: 1.5px solid var(--stone); border-radius: 999px; padding: 8px 16px; font-size: 13px;
        font-family: 'Inter', sans-serif; background: var(--paper); color: var(--ink); outline: none;
    }
    .filter-bar input[type="text"] { flex: 1; min-width: 160px; }
    .filter-bar select:focus, .filter-bar input:focus { border-color: var(--accent); }
    .filter-bar button {
        border: none; background: var(--ink); color: #fff; padding: 8px 20px; border-radius: 999px;
        font-size: 13px; font-weight: 600; cursor: pointer;
    }
    .filter-clear { font-size: 12.5px; color: var(--mute); text-decoration: underline; }

    .result-meta { font-family: 'JetBrains Mono', monospace; font-size: 12px; color: var(--mute); margin-bottom: 20px; display: block; }

    .empty-shop { border: 1.5px dashed var(--stone); border-radius: 18px; padding: 60px 20px; text-align: center; color: var(--mute); }
    .empty-shop p.zf-serif { font-size: 20px; color: var(--ink); margin-bottom: 6px; }

    .shop-pagination { display: flex; justify-content: center; gap: 6px; margin-top: 40px; flex-wrap: wrap; }
    .shop-pagination a, .shop-pagination span {
        min-width: 36px; height: 36px; display: inline-flex; align-items: center; justify-content: center;
        border-radius: 8px; font-size: 13px; font-weight: 600; border: 1px solid var(--stone); color: var(--ink);
        font-family: 'JetBrains Mono', monospace;
    }
    .shop-pagination .active { background: var(--ink); color: #fff; border-color: var(--ink); }
    .shop-pagination .disabled { opacity: 0.35; }

    @media (max-width: 600px) {
        .filter-bar { flex-direction: column; align-items: stretch; }
        .filter-bar input[type="text"] { width: 100%; }
    }
</style>
@endpush

@section('content')

    <div class="shop-head reveal">
        <h1>The Full Collection</h1>
        <p>Every piece, sized right — filter by fit, category, or budget.</p>
    </div>

    <form method="GET" action="{{ route('shop.index') }}" class="filter-bar reveal">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search products...">

        <select name="category" onchange="this.form.submit()">
            <option value="">All Categories</option>
            @foreach ($categories as $cat)
                <option value="{{ $cat->id }}" {{ request('category') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
            @endforeach
        </select>

        <select name="gender" onchange="this.form.submit()">
            <option value="">All Fits</option>
            @foreach (['men' => "Men's", 'women' => "Women's", 'kids' => "Kids'", 'unisex' => 'Unisex'] as $val => $label)
                <option value="{{ $val }}" {{ request('gender') == $val ? 'selected' : '' }}>{{ $label }}</option>
            @endforeach
        </select>

        <select name="sort" onchange="this.form.submit()">
            <option value="">Newest First</option>
            <option value="price_low" {{ request('sort') == 'price_low' ? 'selected' : '' }}>Price: Low to High</option>
            <option value="price_high" {{ request('sort') == 'price_high' ? 'selected' : '' }}>Price: High to Low</option>
            <option value="popular" {{ request('sort') == 'popular' ? 'selected' : '' }}>Most Popular</option>
        </select>

        <button type="submit">Filter</button>

        @if (request()->anyFilled(['search', 'category', 'gender', 'sort']))
            <a href="{{ route('shop.index') }}" class="filter-clear">Clear all</a>
        @endif
    </form>

    <span class="result-meta">{{ $products->total() }} {{ Str::plural('piece', $products->total()) }} found</span>

    @if ($products->isNotEmpty())
        <div class="tag-grid reveal">
            @foreach ($products as $product)
                @include('storefront.partials.product-tag-card', ['product' => $product])
            @endforeach
        </div>

        @if ($products->hasPages())
            <div class="shop-pagination">
                @if ($products->onFirstPage())
                    <span class="disabled">‹</span>
                @else
                    <a href="{{ $products->previousPageUrl() }}">‹</a>
                @endif

                @foreach ($products->getUrlRange(1, $products->lastPage()) as $page => $url)
                    @if ($page == $products->currentPage())
                        <span class="active">{{ $page }}</span>
                    @else
                        <a href="{{ $url }}">{{ $page }}</a>
                    @endif
                @endforeach

                @if ($products->hasMorePages())
                    <a href="{{ $products->nextPageUrl() }}">›</a>
                @else
                    <span class="disabled">›</span>
                @endif
            </div>
        @endif
    @else
        <div class="empty-shop reveal">
            <p class="zf-serif">No pieces match that search</p>
            <p style="font-size:13px;">Try clearing a filter or searching a different term.</p>
        </div>
    @endif

@endsection