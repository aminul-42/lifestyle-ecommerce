@extends('layouts.storefront')

@section('title', 'Your Cart — ' . setting('site_name', config('app.name')))

@push('styles')
<style>
    .cart-wrap { display: grid; grid-template-columns: 1.6fr 1fr; gap: 40px; align-items: start; }
    .cart-head h1 { font-family: 'Fraunces', Georgia, serif; font-size: clamp(28px, 4vw, 38px); font-weight: 500; color: var(--ink); margin-bottom: 26px; }

    .cart-line { display: grid; grid-template-columns: 84px 1fr auto; gap: 16px; padding: 18px 0; border-bottom: 1px solid var(--stone); align-items: center; }
    .cart-line:last-child { border-bottom: none; }
    .cart-line-img { width: 84px; height: 100px; border-radius: 10px; overflow: hidden; background: var(--primary-soft2); display: flex; align-items: center; justify-content: center; }
    .cart-line-img img { width: 100%; height: 100%; object-fit: cover; }
    .cart-line-img svg { width: 28px; height: 28px; color: var(--mute); opacity: 0.5; }
    .cart-line-name { font-family: 'Fraunces', Georgia, serif; font-size: 15.5px; color: var(--ink); margin-bottom: 4px; }
    .cart-line-variant { font-family: 'JetBrains Mono', monospace; font-size: 11px; color: var(--mute); text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 8px; display: block; }
    .cart-line-price { font-family: 'JetBrains Mono', monospace; font-size: 13px; color: var(--ink); font-weight: 600; }
    .cart-qty-stepper { display: flex; align-items: center; border: 1.5px solid var(--stone); border-radius: 999px; overflow: hidden; margin-top: 8px; width: fit-content; }
    .cart-qty-stepper button { width: 30px; height: 30px; border: none; background: #fff; font-size: 14px; cursor: pointer; color: var(--ink); }
    .cart-qty-stepper span { width: 30px; text-align: center; font-family: 'JetBrains Mono', monospace; font-weight: 600; font-size: 13px; }
    .cart-line-right { text-align: right; display: flex; flex-direction: column; align-items: flex-end; gap: 10px; }
    .cart-line-total { font-family: 'JetBrains Mono', monospace; font-weight: 700; font-size: 14.5px; color: var(--ink); }
    .cart-remove-btn { background: none; border: none; cursor: pointer; color: var(--mute); font-size: 12px; text-decoration: underline; padding: 0; }
    .cart-remove-btn:hover { color: #991b1b; }
    .stock-warning { font-size: 11px; color: #92400e; margin-top: 4px; }

    .cart-empty { text-align: center; padding: 70px 20px; border: 1.5px dashed var(--stone); border-radius: 18px; }
    .cart-empty svg { width: 48px; height: 48px; color: var(--mute); opacity: 0.5; margin-bottom: 14px; }
    .cart-empty p.zf-serif { font-size: 20px; color: var(--ink); margin-bottom: 6px; }

    .summary-card { border: 1px solid var(--stone); border-radius: 18px; padding: 24px; position: sticky; top: 90px; }
    .summary-card h3 { font-family: 'Fraunces', Georgia, serif; font-size: 18px; margin-bottom: 18px; color: var(--ink); }
    .summary-row { display: flex; justify-content: space-between; font-size: 13.5px; color: var(--mute); padding: 7px 0; }
    .summary-row.discount { color: #166534; }
    .summary-row.total { border-top: 1px solid var(--stone); margin-top: 8px; padding-top: 14px; font-size: 16px; font-weight: 700; color: var(--ink); }
    .summary-row.total span:last-child { font-family: 'JetBrains Mono', monospace; }

    .coupon-box { display: flex; gap: 8px; margin: 18px 0; }
    .coupon-box input { flex: 1; border: 1.5px solid var(--stone); border-radius: 999px; padding: 9px 14px; font-size: 13px; font-family: 'JetBrains Mono', monospace; text-transform: uppercase; }
    .coupon-box input:focus { outline: none; border-color: var(--accent); }
    .coupon-applied { display: flex; justify-content: space-between; align-items: center; background: var(--accent-soft); border-radius: 10px; padding: 10px 14px; margin: 18px 0; font-size: 13px; font-weight: 600; }
    .coupon-applied button { background: none; border: none; cursor: pointer; color: var(--mute); font-size: 11px; text-decoration: underline; }
    .coupon-error { font-size: 12px; color: #991b1b; margin-top: 6px; display: block; }

    .checkout-btn { width: 100%; padding: 13px; font-size: 15px; margin-top: 6px; }

    @media (max-width: 860px) {
        .cart-wrap { grid-template-columns: 1fr; }
        .summary-card { position: static; }
    }
</style>
@endpush

@section('content')

    <div class="cart-head reveal"><h1>Your Cart</h1></div>

    @if ($cartItems->isEmpty())
        <div class="cart-empty reveal">
            <svg fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" style="margin:0 auto;"><path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
            <p class="zf-serif">Your cart is empty</p>
            <p style="font-size:13px; color:var(--mute); margin-bottom:20px;">Nothing tagged yet — go find something you'll love.</p>
            <a href="{{ route('shop.index') }}" class="btn btn-primary">Browse the Shop</a>
        </div>
    @else
        <div class="cart-wrap reveal">
            <div>
                <div id="cartLines">
                    @foreach ($cartItems as $item)
                        @php
                            $stock = $item->variant ? $item->variant->stock : $item->product->stock;
                            $img = $item->product->images->first();
                        @endphp
                        <div class="cart-line" data-cart-id="{{ $item->id }}">
                            <div class="cart-line-img">
                                @if($img)
                                    <img src="{{ Storage::disk('public')->url($img->path) }}" alt="{{ $item->product->name }}">
                                @else
                                    <svg fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M20.38 3.46L16 2a4 4 0 01-8 0L3.62 3.46a2 2 0 00-1.34 2.23l.58 3.47a1 1 0 00.99.84H6v10a2 2 0 002 2h8a2 2 0 002-2V10h2.15a1 1 0 00.99-.84l.58-3.47a2 2 0 00-1.34-2.23z"/></svg>
                                @endif
                            </div>
                            <div>
                                <div class="cart-line-name">{{ $item->product->name }}</div>
                                @if($item->variant)
                                    <span class="cart-line-variant">{{ $item->variant->label }}</span>
                                @endif
                                <span class="cart-line-price">{{ money($item->unit_price) }} each</span>
                                <div class="cart-qty-stepper">
                                    <button type="button" onclick="cartChangeQty({{ $item->id }}, -1)">−</button>
                                    <span id="qty-{{ $item->id }}">{{ $item->quantity }}</span>
                                    <button type="button" onclick="cartChangeQty({{ $item->id }}, 1)">+</button>
                                </div>
                                @if($stock <= 5)
                                    <span class="stock-warning">Only {{ $stock }} left in stock</span>
                                @endif
                            </div>
                            <div class="cart-line-right">
                                <span class="cart-line-total" id="line-total-{{ $item->id }}">{{ money($item->line_total) }}</span>
                                <button type="button" class="cart-remove-btn" onclick="cartRemove({{ $item->id }})">Remove</button>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="summary-card">
                <h3>Order Summary</h3>

                <div class="summary-row">
                    <span>Subtotal</span>
                    <span id="summarySubtotal">{{ money($subtotal) }}</span>
                </div>

                @if ($coupon)
                    <div class="coupon-applied">
                        <span>{{ $coupon->code }} applied</span>
                        <button type="button" onclick="cartRemoveCoupon()">Remove</button>
                    </div>
                    <div class="summary-row discount">
                        <span>Discount</span>
                        <span id="summaryDiscount">−{{ money($discount) }}</span>
                    </div>
                @else
                    <div class="coupon-box">
                        <input type="text" id="couponInput" placeholder="Coupon code">
                        <button type="button" class="btn-ghost" onclick="cartApplyCoupon()">Apply</button>
                    </div>
                    <span class="coupon-error" id="couponError"></span>
                @endif

                <div class="summary-row total">
                    <span>Total</span>
                    <span id="summaryTotal">{{ money($total) }}</span>
                </div>

                <button type="button" class="btn btn-primary checkout-btn" onclick="window.location.href='{{ route('checkout.index') }}'">Proceed to Checkout</button>
            </div>
        </div>
    @endif

@endsection

@push('scripts')
<script>
    async function parseJson(res) {
        try { return (await res.json()) ?? {}; } catch { return {}; }
    }

    function formatMoney(amount) {
        // Adjust to match your money() helper's format exactly
        return '৳' + Number(amount).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }

    function recalcSubtotal() {
        let subtotal = 0;
        document.querySelectorAll('.cart-line').forEach(line => {
            const totalEl = line.querySelector('.cart-line-total');
            subtotal += parseFloat(totalEl.dataset.raw || 0);
        });
        return subtotal;
    }

    function updateSummary(subtotal, discount) {
        const total = Math.max(subtotal - discount, 0);
        const subEl = document.getElementById('summarySubtotal');
        const discEl = document.getElementById('summaryDiscount');
        const totEl = document.getElementById('summaryTotal');
        if (subEl) subEl.textContent = formatMoney(subtotal);
        if (discEl) discEl.textContent = '−' + formatMoney(discount);
        if (totEl) totEl.textContent = formatMoney(total);
    }

    function updateCartCountBadge(count) {
        document.querySelectorAll('.cart-count-badge').forEach(el => {
            el.textContent = count > 9 ? '9+' : count;
            el.style.display = count > 0 ? 'flex' : 'none';
        });
    }

    function checkEmptyCart() {
        const lines = document.querySelectorAll('.cart-line');
        if (lines.length === 0) location.reload(); // simplest safe path once cart is truly empty
    }

    async function cartChangeQty(cartId, delta) {
        const qtyEl = document.getElementById(`qty-${cartId}`);
        const newQty = parseInt(qtyEl.textContent, 10) + delta;
        if (newQty < 1) { cartRemove(cartId); return; }

        let res;
        try {
            const formData = new FormData();
            formData.append('quantity', newQty);
            const url = "{{ route('cart.update', ':id') }}".replace(':id', cartId);

            res = await fetch(url, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
                body: formData,
            });
        } catch {
            showToast('Network error — please try again.', 'error');
            return;
        }

        const json = await parseJson(res);
        if (!res.ok) {
            showToast(json.message || 'Could not update quantity.', 'error');
            return;
        }

        qtyEl.textContent = json.quantity;

        const totalEl = document.getElementById(`line-total-${cartId}`);
        if (totalEl) {
            totalEl.textContent = formatMoney(json.line_total);
            totalEl.dataset.raw = json.line_total;
        }

        if (json.capped) showToast('Quantity capped by available stock.');

        // Recalc summary live, no reload
        const subtotal = recalcSubtotal();
        const discountEl = document.getElementById('summaryDiscount');
        const currentDiscount = discountEl ? parseFloat(discountEl.textContent.replace(/[^0-9.]/g, '')) || 0 : 0;
        updateSummary(subtotal, currentDiscount);

        if (json.cart_count !== undefined) updateCartCountBadge(json.cart_count);
        document.dispatchEvent(new CustomEvent('cart:updated', { detail: { count: json.cart_count } }));
    }

    async function cartRemove(cartId) {
        try {
            const url = "{{ route('cart.destroy', ':id') }}".replace(':id', cartId);

            const res = await fetch(url, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
            });
            const json = await parseJson(res);
            if (!res.ok) { showToast(json.message || 'Could not remove item.', 'error'); return; }

            showToast('Item removed.');

            const lineEl = document.querySelector(`.cart-line[data-cart-id="${cartId}"]`);
            if (lineEl) {
                lineEl.style.transition = 'opacity 0.25s ease';
                lineEl.style.opacity = '0';
                setTimeout(() => {
                    lineEl.remove();
                    const subtotal = recalcSubtotal();
                    const discountEl = document.getElementById('summaryDiscount');
                    const currentDiscount = discountEl ? parseFloat(discountEl.textContent.replace(/[^0-9.]/g, '')) || 0 : 0;
                    updateSummary(subtotal, currentDiscount);
                    if (json.cart_count !== undefined) updateCartCountBadge(json.cart_count);
                    document.dispatchEvent(new CustomEvent('cart:updated', { detail: { count: json.cart_count } }));
                    checkEmptyCart();
                }, 250);
            }
        } catch {
            showToast('Network error — please try again.', 'error');
        }
    }

    async function cartApplyCoupon() {
        const input = document.getElementById('couponInput');
        const errorEl = document.getElementById('couponError');
        const code = input.value.trim();

        if (errorEl) errorEl.textContent = '';
        if (!code) {
            if (errorEl) errorEl.textContent = 'Please enter a coupon code.';
            return;
        }

        let res;
        try {
            const formData = new FormData();
            formData.append('code', code);

            res = await fetch("{{ route('cart.coupon.apply') }}", {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
                body: formData,
            });
        } catch {
            showToast('Network error — please try again.', 'error');
            return;
        }

        const json = await parseJson(res);
        if (!res.ok) {
            if (errorEl) errorEl.textContent = json.message || 'Could not apply coupon.';
            return;
        }

        showToast(json.message || 'Coupon applied!');
        // Coupon changes the layout (box -> applied badge), simplest to reload here
        location.reload();
    }

    async function cartRemoveCoupon() {
        try {
            const res = await fetch("{{ route('cart.coupon.remove') }}", {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
            });
            const json = await parseJson(res);
            if (!res.ok) { showToast(json.message || 'Could not remove coupon.', 'error'); return; }
            showToast('Coupon removed.');
            location.reload();
        } catch {
            showToast('Network error — please try again.', 'error');
        }
    }
</script>
@endpush