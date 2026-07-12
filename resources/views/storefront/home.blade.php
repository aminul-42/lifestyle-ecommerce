@extends('layouts.storefront')

@section('title', setting('site_name', config('app.name')))

@push('styles')
<style>
    .admin-banner { background: var(--ink); color: #fff; font-size: 13px; font-weight: 600; text-align: center; padding: 9px 16px; display: flex; align-items: center; justify-content: center; gap: 10px; margin: -32px -20px 32px; }
    .admin-banner a { color: var(--accent); text-decoration: underline; }

    /* ===== HERO ===== */
    .hero { position: relative; display: grid; grid-template-columns: 1.05fr 0.95fr; gap: 48px; align-items: center; padding: 20px 0 56px; overflow: hidden; }
    .hero-cut {
        position: absolute; top: -40px; right: 26%; width: 3px; height: 130%;
        background: var(--accent); transform: rotate(14deg); transform-origin: top;
        opacity: 0; animation: cutIn 0.9s cubic-bezier(.2,.8,.2,1) 0.2s forwards;
    }
    @keyframes cutIn { from { opacity: 0; transform: rotate(14deg) scaleY(0); } to { opacity: 1; transform: rotate(14deg) scaleY(1); } }

    .hero-eyebrow {
        display: inline-flex; align-items: center; gap: 8px; font-size: 11px; font-weight: 700;
        letter-spacing: 0.16em; text-transform: uppercase; color: var(--ink);
        background: var(--accent-soft); padding: 6px 14px 6px 10px; border-radius: 999px; margin-bottom: 22px;
    }
    .hero-eyebrow::before { content: ''; width: 7px; height: 7px; border-radius: 50%; background: var(--accent); display: inline-block; }

    .quote-slider { position: relative; min-height: 148px; margin-bottom: 22px; }
    .quote-slide { position: absolute; inset: 0; opacity: 0; transform: translateY(18px) scale(0.98); transition: opacity 0.55s ease, transform 0.55s ease; pointer-events: none; }
    .quote-slide.active { opacity: 1; transform: translateY(0) scale(1); pointer-events: auto; }
    .quote-slide .q-mark { font-size: 46px; font-weight: 900; color: var(--accent); line-height: 1; display: block; margin-bottom: 2px; }
    .quote-slide p { font-weight: 800; font-size: clamp(28px, 3.8vw, 44px); line-height: 1.08; letter-spacing: -0.015em; color: var(--ink); }

    .quote-dots { display: flex; gap: 8px; margin-bottom: 26px; }
    .quote-dot { width: 26px; height: 4px; border-radius: 2px; background: var(--stone); border: none; cursor: pointer; transition: background 0.2s ease, width 0.2s ease; padding: 0; }
    .quote-dot.active { background: var(--accent); width: 40px; }

    .hero p.lede { font-size: 16px; color: var(--mute); max-width: 440px; line-height: 1.65; margin-bottom: 28px; opacity: 0; animation: fadeUp 0.7s ease forwards; animation-delay: 0.5s; }
    .hero-ctas { display: flex; gap: 12px; flex-wrap: wrap; opacity: 0; animation: fadeUp 0.7s ease forwards; animation-delay: 0.7s; }
    @keyframes fadeUp { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }

    .hero-ctas .btn-primary { background: var(--ink); }
    .hero-ctas .btn-primary:hover { background: var(--accent); }

    .hero-stats { display: flex; gap: 0; margin-top: 38px; opacity: 0; animation: fadeUp 0.7s ease forwards; animation-delay: 0.9s; }
    .hero-stat { padding-right: 28px; margin-right: 28px; border-right: 1.5px solid var(--stone); }
    .hero-stat:last-child { border-right: none; margin-right: 0; padding-right: 0; }
    .hero-stat strong { font-size: 26px; font-weight: 900; color: var(--ink); display: block; font-variant-numeric: tabular-nums; }
    .hero-stat span { font-size: 11px; color: var(--mute); text-transform: uppercase; letter-spacing: 0.07em; font-weight: 600; }

    /* ===== HERO VISUAL — New Arrivals slider ===== */
    .hero-visual { position: relative; height: 440px; }
    .hero-ring { position: absolute; top: 2%; right: 8%; width: 90px; height: 90px; border-radius: 50%; border: 2px dashed var(--ink); opacity: 0.3; animation: spinSlow 22s linear infinite; }
    @keyframes spinSlow { from { transform: rotate(0deg); } to { transform: rotate(360deg); } }

    .arrivals-card {
        position: absolute; inset: 0; margin: auto; width: 280px; height: 380px;
        background: var(--card); border-radius: 8px 28px 28px 8px; overflow: hidden;
        box-shadow: 0 30px 60px rgba(18,18,18,0.20); border: 2px solid var(--ink);
        opacity: 0; transform: translateY(24px) scale(0.97); transition: opacity 0.5s ease, transform 0.5s ease;
        pointer-events: none;
    }
    .arrivals-card.active { opacity: 1; transform: translateY(0) scale(1); pointer-events: auto; }
    .arrivals-img { width: 100%; height: 250px; background: var(--accent-soft); display: flex; align-items: center; justify-content: center; position: relative; }
    .arrivals-img img { width: 100%; height: 100%; object-fit: cover; }
    .arrivals-img svg { width: 44px; height: 44px; color: var(--mute); opacity: 0.5; }
    .arrivals-new-tag {
        position: absolute; top: 12px; left: 12px; background: var(--accent); color: #fff;
        font-size: 10px; font-weight: 800; letter-spacing: 0.08em; text-transform: uppercase;
        padding: 5px 10px; border-radius: 999px;
    }
    .arrivals-body { padding: 16px 18px; }
    .arrivals-body .a-eyebrow { font-size: 10px; letter-spacing: 0.1em; text-transform: uppercase; color: var(--mute); font-weight: 700; }
    .arrivals-body h3 { font-weight: 800; font-size: 17px; margin: 6px 0 8px; color: var(--ink); line-height: 1.25; }
    .arrivals-price-row { display: flex; align-items: baseline; gap: 8px; }
    .arrivals-price { font-weight: 800; font-size: 16px; color: var(--ink); }
    .arrivals-price-old { font-size: 12px; color: var(--mute); text-decoration: line-through; }

    .arrivals-nav { position: absolute; bottom: -6px; left: 50%; transform: translateX(-50%); display: flex; gap: 7px; z-index: 5; }
    .arrivals-dot { width: 7px; height: 7px; border-radius: 50%; background: var(--stone); border: none; cursor: pointer; padding: 0; transition: background 0.2s ease, width 0.2s ease; }
    .arrivals-dot.active { background: var(--accent); width: 20px; border-radius: 4px; }

    .arrivals-empty {
        position: absolute; inset: 0; margin: auto; width: 260px; height: 300px;
        background: var(--ink); color: #fff; border-radius: 8px 28px 28px 8px;
        display: flex; flex-direction: column; align-items: center; justify-content: center; text-align: center; padding: 30px;
    }
    .arrivals-empty strong { font-size: 18px; font-weight: 800; margin-bottom: 6px; }
    .arrivals-empty span { font-size: 12.5px; color: #cfcfcf; }

    .float-badge {
        position: absolute; background: var(--card); border: 1.5px solid var(--ink); border-radius: 12px;
        padding: 10px 14px; font-size: 12px; font-weight: 700; color: var(--ink);
        box-shadow: 0 10px 26px rgba(18,18,18,0.10); animation: bob 5s ease-in-out infinite; z-index: 4;
    }
    .float-badge svg { width: 14px; height: 14px; vertical-align: -2px; margin-right: 4px; color: var(--accent); }
    @keyframes bob { 0%,100% { transform: translateY(0); } 50% { transform: translateY(-8px); } }

    .reveal { opacity: 0; transform: translateY(24px); transition: opacity 0.7s ease, transform 0.7s ease; }
    .reveal.in-view { opacity: 1; transform: translateY(0); }

    /* ===== MARQUEE ===== */
    .marquee-wrap { background: var(--ink); border-radius: 16px; overflow: hidden; padding: 16px 0; margin: 40px 0 64px; }
    .marquee-track { display: flex; gap: 44px; width: max-content; animation: scroll-left 24s linear infinite; }
    .marquee-track span { font-size: 13px; font-weight: 700; letter-spacing: 0.05em; color: #fff; white-space: nowrap; display: flex; align-items: center; gap: 44px; }
    .marquee-track span::after { content: '✂'; color: var(--accent); font-size: 12px; }
    @keyframes scroll-left { from { transform: translateX(0); } to { transform: translateX(-50%); } }

    /* ===== CATEGORY GRID — ticket-notch cards for guaranteed contrast ===== */
    .cat-grid { display: grid; grid-template-columns: repeat(6, 1fr); grid-auto-rows: 140px; gap: 16px; }
    .cat-grid .cat-card:nth-child(1) { grid-column: span 3; grid-row: span 2; }
    .cat-grid .cat-card:nth-child(2) { grid-column: span 3; grid-row: span 1; }
    .cat-grid .cat-card:nth-child(3) { grid-column: span 3; grid-row: span 1; }
    .cat-grid .cat-card:not(:nth-child(-n+3)) { grid-column: span 2; }

    .cat-card {
        position: relative; border-radius: 18px; overflow: hidden; cursor: pointer;
        display: flex; align-items: flex-end; padding: 22px; transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    .cat-card:hover { transform: translateY(-6px); }

    .cat-card.cat-dark { background: var(--ink); color: #fff; box-shadow: 0 10px 28px rgba(18,18,18,0.18); }
    .cat-card.cat-light {
        background: var(--card); color: var(--ink);
        border: 2.5px solid var(--ink);
        box-shadow: 6px 6px 0 var(--ink);
    }
    .cat-card.cat-light:hover { box-shadow: 4px 4px 0 var(--ink); transform: translate(2px, -4px); }

    /* Ticket notch — the unmistakable "this is a card" signal, independent of color contrast */
    .cat-card.cat-light::before {
        content: ''; position: absolute; top: 50%; left: -11px; width: 20px; height: 20px;
        background: var(--bg); border: 2.5px solid var(--ink); border-radius: 50%; transform: translateY(-50%); z-index: 2;
    }

    .cat-card .cat-watermark { position: absolute; right: -10px; top: -24px; font-weight: 900; font-size: 130px; line-height: 1; user-select: none; }
    .cat-card.cat-dark .cat-watermark { color: #fff; opacity: 0.07; }
    .cat-card.cat-light .cat-watermark { color: var(--ink); opacity: 0.06; }

    .cat-card .cat-name { font-weight: 800; font-size: 20px; position: relative; z-index: 1; }
    .cat-card .cat-count { font-size: 11px; font-weight: 700; position: relative; z-index: 1; display: block; margin-top: 4px; }
    .cat-card.cat-dark .cat-count { color: var(--accent-soft); }
    .cat-card.cat-light .cat-count { color: var(--accent); }

    .cat-card.cat-dark::after { content: ''; position: absolute; left: 22px; right: 22px; bottom: 0; height: 4px; background: var(--accent); }

    .sec-head { display: flex; align-items: flex-end; justify-content: space-between; margin-bottom: 28px; gap: 20px; flex-wrap: wrap; }
    .sec-head h2 { font-weight: 800; font-size: clamp(24px, 3.2vw, 34px); color: var(--ink); letter-spacing: -0.01em; }
    .sec-head p { color: var(--mute); font-size: 14px; max-width: 320px; }

    /* ===== TRUST + CTA ===== */
    .trust-strip { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 18px; }
    .trust-card { border-radius: 16px; padding: 22px; display: flex; gap: 14px; align-items: flex-start; background: var(--card); border: 1.5px solid var(--stone); transition: border-color 0.2s ease; }
    .trust-card:hover { border-color: var(--accent); }
    .trust-icon { width: 40px; height: 40px; border-radius: 10px; background: var(--ink); display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
    .trust-icon svg { width: 18px; height: 18px; color: var(--accent-soft); }
    .trust-card strong { display: block; font-size: 14px; margin-bottom: 3px; font-weight: 700; }
    .trust-card span { font-size: 12.5px; color: var(--mute); line-height: 1.5; }

    .cta-band { background: var(--ink); border-radius: 24px; padding: 56px 40px; text-align: center; color: #fff; position: relative; overflow: hidden; }
    .cta-band::before { content: ''; position: absolute; inset: 0; background: radial-gradient(650px circle at 25% 20%, rgba(225,29,46,0.28), transparent 65%); }
    .cta-band h2 { font-weight: 800; font-size: clamp(24px, 3.6vw, 36px); margin-bottom: 12px; position: relative; }
    .cta-band p { color: #cfcfcf; font-size: 14px; margin-bottom: 26px; position: relative; }
    .cta-band .btn-primary { position: relative; background: var(--accent); }

    @media (max-width: 860px) {
        .hero { grid-template-columns: 1fr; padding-top: 8px; }
        .hero-visual { height: 420px; margin-top: 10px; }
        .hero-cut, .hero-ring { display: none; }
        .quote-slider { min-height: 108px; }
        .cat-grid { grid-template-columns: repeat(2, 1fr); grid-auto-rows: 130px; }
        .cat-grid .cat-card:nth-child(1), .cat-grid .cat-card:nth-child(2), .cat-grid .cat-card:nth-child(3) { grid-column: span 2; grid-row: span 1; }
    }
    @media (prefers-reduced-motion: reduce) {
        .hero-ring, .float-badge { animation: none !important; }
    }
</style>
@endpush

@section('content')

    @auth
        @if(auth()->user()->isAdmin())
            <div class="admin-banner">
                Viewing storefront as {{ auth()->user()->role }} &middot;
                <a href="{{ route('admin.dashboard') }}">Go to Admin Dashboard →</a>
            </div>
        @endif
    @endauth

    <section class="hero">
        <div class="hero-cut"></div>
        <div>
            <span class="hero-eyebrow">
                @auth
                    @if(!auth()->user()->isAdmin())
                        Welcome back, {{ explode(' ', auth()->user()->name)[0] }}
                    @else
                        {{ setting('site_tagline', 'A modern wardrobe edit') }}
                    @endif
                @else
                    {{ setting('site_tagline', 'A modern wardrobe edit') }}
                @endauth
            </span>

            <div class="quote-slider" id="quoteSlider">
                <div class="quote-slide active"><span class="q-mark">“</span><p>Cut for how you actually move.</p></div>
                <div class="quote-slide"><span class="q-mark">“</span><p>Small edits, worn every day.</p></div>
                <div class="quote-slide"><span class="q-mark">“</span><p>Fit first. Trend second.</p></div>
                <div class="quote-slide"><span class="q-mark">“</span><p>Made to be lived in, not looked at.</p></div>
            </div>
            <div class="quote-dots" id="quoteDots"></div>

            <p class="lede">
                @auth
                    @if(!auth()->user()->isAdmin())
                        Your edit is waiting. New arrivals, wardrobe staples, and pieces picked to fit your world.
                    @else
                        {{ setting('site_name', config('app.name')) }} — a considered edit of everyday fashion.
                    @endif
                @else
                    {{ setting('site_name', config('app.name')) }} is a considered edit of everyday fashion — thoughtfully sized, honestly priced.
                @endauth
            </p>

            <div class="hero-ctas">
                @auth
                    <a href="{{ route('shop.index') }}" class="btn btn-primary">{{ auth()->user()->isAdmin() ? 'View Storefront' : 'Continue Browsing' }}</a>
                    <a href="#shop-by-category" class="btn-ghost">Shop by Category</a>
                @else
                    <a href="{{ route('login') }}" class="btn btn-primary">Create Your Account</a>
                    <a href="{{ route('shop.index') }}" class="btn-ghost">Explore the Collection</a>
                @endauth
            </div>

            <div class="hero-stats">
               
                @if($reviewsCount > 0)
                    <div class="hero-stat"><strong data-count="{{ $reviewsCount }}">0</strong><span>Verified Reviews</span></div>
                    <div class="hero-stat"><strong>{{ $avgRating }}<span style="font-size:15px;">/5</span></strong><span>Average Rating</span></div>
                @endif
            </div>
        </div>

        <div class="hero-visual">
            <div class="hero-ring"></div>

            @if($featuredProducts->isNotEmpty())
                @foreach($featuredProducts as $index => $product)
                    @php $img = $product->images->first(); @endphp
                    <div class="arrivals-card {{ $index === 0 ? 'active' : '' }}" data-slide="{{ $index }}">
                        <div class="arrivals-img">
                            @if($img)
                                <img src="{{ Storage::disk('public')->url($img->path) }}" alt="{{ $product->name }}">
                            @else
                                <svg fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M20.38 3.46L16 2a4 4 0 01-8 0L3.62 3.46a2 2 0 00-1.34 2.23l.58 3.47a1 1 0 00.99.84H6v10a2 2 0 002 2h8a2 2 0 002-2V10h2.15a1 1 0 00.99-.84l.58-3.47a2 2 0 00-1.34-2.23z"/></svg>
                            @endif
                            <span class="arrivals-new-tag">New</span>
                        </div>
                        <div class="arrivals-body">
                           
                            <h3>{{ $product->name }}</h3>
                            <div class="arrivals-price-row">
                                <span class="arrivals-price">{{ money($product->discount_price ?? $product->price) }}</span>
                                @if($product->discount_price)
                                    <span class="arrivals-price-old">{{ money($product->price) }}</span>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach

                <div class="arrivals-nav" id="arrivalsNav"></div>
            @else
                <div class="arrivals-empty">
                    <strong>New arrivals soon</strong>
                    <span>Featured pieces will appear here once added from the admin panel.</span>
                </div>
            @endif

            @if($reviewsCount > 0)
                <div class="float-badge" style="top:2%; right:2%;">
                    <svg fill="currentColor" viewBox="0 0 20 20"><path d="M10 1l2.6 5.6L19 7.6l-4.5 4.3L15.6 18 10 14.8 4.4 18l1.1-6.1L1 7.6l6.4-1z"/></svg>
                    {{ $avgRating }}/5 rated
                </div>
            @endif
            
        </div>
    </section>

    <div class="marquee-wrap reveal">
        <div class="marquee-track">
            @for ($i = 0; $i < 2; $i++)
                <span>
                    <span>{{ setting('site_tagline', 'New arrivals weekly') }}</span>
                    <span>Free size exchange</span>
                    @if(setting('bkash_number') || setting('nagad_number'))<span>Manual payment verified in minutes</span>@endif
                    @if($reviewsCount > 0)<span>{{ $reviewsCount }} verified reviews</span>@endif
                    <span>{{ setting('currency_code', 'BDT') }} pricing, honestly done</span>
                </span>
            @endfor
        </div>
    </div>

    @if($categories->isNotEmpty())
        <section id="shop-by-category" class="reveal" style="margin-bottom:70px;">
            <div class="sec-head">
                <h2>Shop by Category</h2>
                <p>Fit guides, not guesswork — browse the edit by how you actually get dressed.</p>
            </div>
            <div class="cat-grid">
                @foreach($categories as $i => $category)
                    <a href="{{ route('shop.index', ['category' => $category->id]) }}"
                       class="cat-card {{ $i % 2 === 0 ? 'cat-dark' : 'cat-light' }}">
                        <span class="cat-watermark">{{ strtoupper(substr($category->name, 0, 1)) }}</span>
                        <div>
                            <span class="cat-name">{{ $category->name }}</span>
                            <span class="cat-count">{{ $category->total_count }} {{ Str::plural('piece', $category->total_count) }}</span>
                        </div>
                    </a>
                @endforeach
            </div>
        </section>
    @endif

    <section id="featured-edit" class="reveal" style="margin-bottom:20px;">
        <div class="sec-head">
            <h2>Featured Products</h2>
            <p>Hand-picked pieces from this season, tagged and ready.</p>
        </div>

        @if($featuredProducts->isNotEmpty())
            <div class="tag-row">
                @foreach($featuredProducts as $product)
                    @include('storefront.partials.product-tag-card', ['product' => $product])
                @endforeach
            </div>
        @else
            <div style="border:1.5px dashed var(--stone); border-radius:18px; padding:50px 20px; text-align:center; color:var(--mute);">
                <p style="font-size:20px; font-weight:800; color:var(--ink); margin-bottom:6px;">New arrivals dropping soon</p>
                <p style="font-size:13px;">Featured pieces will appear here once added from the admin panel.</p>
            </div>
        @endif
    </section>

    <section class="trust-strip reveal" style="margin:20px 0 70px;">
        @if($reviewsCount > 0)
            <div class="trust-card">
                <div class="trust-icon"><svg fill="currentColor" viewBox="0 0 20 20"><path d="M10 1l2.6 5.6L19 7.6l-4.5 4.3L15.6 18 10 14.8 4.4 18l1.1-6.1L1 7.6l6.4-1z"/></svg></div>
                <div>
                    <strong>{{ $avgRating }} / 5 average rating</strong>
                    <span>From {{ $reviewsCount }} verified customer {{ Str::plural('review', $reviewsCount) }}.</span>
                </div>
            </div>
        @endif

        @if(setting('bkash_number') || setting('nagad_number') || setting('bank_details'))
            <div class="trust-card">
                <div class="trust-icon"><svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M7 15h2m4 0h4M5 6h14a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2V8a2 2 0 012-2z"/></svg></div>
                <div>
                    <strong>Manual payment, verified fast</strong>
                    <span>{{ setting('payment_instructions', 'Pay via bKash, Nagad, or bank transfer — we confirm every order by hand.') }}</span>
                </div>
            </div>
        @endif

        @if(setting('cod_enabled') == '1')
            <div class="trust-card">
                <div class="trust-icon"><svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v8m-4-4h8m5 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg></div>
                <div>
                    <strong>Cash on delivery</strong>
                    <span>Pay when your order arrives at your doorstep — no card required.</span>
                </div>
            </div>
        @endif

        <div class="trust-card">
            <div class="trust-icon"><svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg></div>
            <div>
                <strong>Fit, checked by hand</strong>
                <span>Every size and measurement is reviewed before it ships — no surprises at the door.</span>
            </div>
        </div>
    </section>

    @guest
        <section class="cta-band reveal" style="margin-bottom:70px;">
            <h2>Ready to build your wardrobe?</h2>
            <p>Create your account in seconds — no forms, just sign in with Google.</p>
            <a href="{{ route('login') }}" class="btn btn-primary">Create Your Account</a>
        </section>
    @endguest

@endsection

@push('scripts')
<script>
    // Brand quote slider
    (function () {
        const slides = document.querySelectorAll('.quote-slide');
        const dotsWrap = document.getElementById('quoteDots');
        if (!slides.length || !dotsWrap) return;

        let current = 0;
        const prefersReduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

        slides.forEach((_, i) => {
            const dot = document.createElement('button');
            dot.type = 'button';
            dot.className = 'quote-dot' + (i === 0 ? ' active' : '');
            dot.addEventListener('click', () => goTo(i));
            dotsWrap.appendChild(dot);
        });
        const dots = dotsWrap.querySelectorAll('.quote-dot');

        function goTo(index) {
            slides[current].classList.remove('active');
            dots[current].classList.remove('active');
            current = index;
            slides[current].classList.add('active');
            dots[current].classList.add('active');
        }

        if (!prefersReduced) setInterval(() => goTo((current + 1) % slides.length), 4200);
    })();

    // New Arrivals hero slider
    (function () {
        const cards = document.querySelectorAll('.arrivals-card');
        const navWrap = document.getElementById('arrivalsNav');
        if (!cards.length || !navWrap) return;

        let current = 0;
        const prefersReduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

        cards.forEach((_, i) => {
            const dot = document.createElement('button');
            dot.type = 'button';
            dot.className = 'arrivals-dot' + (i === 0 ? ' active' : '');
            dot.addEventListener('click', () => goTo(i));
            navWrap.appendChild(dot);
        });
        const dots = navWrap.querySelectorAll('.arrivals-dot');

        function goTo(index) {
            cards[current].classList.remove('active');
            dots[current].classList.remove('active');
            current = index;
            cards[current].classList.add('active');
            dots[current].classList.add('active');
        }

        if (!prefersReduced && cards.length > 1) {
            setInterval(() => goTo((current + 1) % cards.length), 3800);
        }
    })();

    // Animated counters
    document.addEventListener('DOMContentLoaded', () => {
        const counters = document.querySelectorAll('[data-count]');
        const animateCount = (el) => {
            const target = parseInt(el.dataset.count, 10) || 0;
            const duration = 1200;
            const start = performance.now();
            function tick(now) {
                const progress = Math.min((now - start) / duration, 1);
                const eased = 1 - Math.pow(1 - progress, 3);
                el.textContent = Math.floor(eased * target).toLocaleString();
                if (progress < 1) requestAnimationFrame(tick);
                else el.textContent = target.toLocaleString();
            }
            requestAnimationFrame(tick);
        };

        if ('IntersectionObserver' in window && counters.length) {
            const io = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) { animateCount(entry.target); io.unobserve(entry.target); }
                });
            }, { threshold: 0.5 });
            counters.forEach(c => io.observe(c));
        } else {
            counters.forEach(c => c.textContent = c.dataset.count);
        }
    });
</script>
@endpush