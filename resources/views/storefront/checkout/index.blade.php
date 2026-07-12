@extends('layouts.storefront')

@section('title', 'Checkout — ' . setting('site_name', config('app.name')))

@push('styles')
<style>
    .checkout-wrap { display: grid; grid-template-columns: 1.6fr 1fr; gap: 40px; align-items: start; }
    .checkout-head h1 { font-family:'Fraunces',Georgia,serif; font-size:clamp(28px,4vw,38px); font-weight:500; color:var(--ink); margin-bottom:26px; }
    .checkout-section { border:1px solid var(--stone); border-radius:18px; padding:24px; margin-bottom:24px; }
    .checkout-section h3 { font-family:'Fraunces',Georgia,serif; font-size:17px; margin-bottom:16px; color:var(--ink); }
    .addr-option { display:flex; gap:12px; align-items:flex-start; border:1.5px solid var(--stone); border-radius:12px; padding:14px; margin-bottom:10px; cursor:pointer; }
    .addr-option.selected { border-color: var(--accent); background: var(--accent-soft); }
    .addr-name { font-weight:700; font-size:14px; }
    .addr-detail { font-size:12.5px; color:var(--mute); line-height:1.5; }
    .form-group { margin-bottom:14px; }
    .form-group label { display:block; font-size:12.5px; font-weight:600; margin-bottom:6px; color:var(--ink); }
    .form-group input, .form-group textarea, .form-group select { width:100%; border:1.5px solid var(--stone); border-radius:10px; padding:10px 12px; font-size:13.5px; font-family:'Inter',sans-serif; background:#fff; }
    .form-group input:focus, .form-group textarea:focus, .form-group select:focus { outline:none; border-color:var(--accent); }
    .form-row { display:grid; grid-template-columns:1fr 1fr; gap:12px; }
    .field-error { font-size:11.5px; color:#991b1b; margin-top:4px; display:block; }

    /* Custom radio for payment methods — bypasses any native input styling conflicts */
    .pay-option { display:block; border:1.5px solid var(--stone); border-radius:12px; padding:14px; margin-bottom:10px; cursor:pointer; user-select:none; }
    .pay-option.selected { border-color:var(--accent); background:var(--accent-soft); }
    .pay-option input[type="radio"] { position:absolute; opacity:0; width:0; height:0; pointer-events:none; }
    .pay-option-head { display:flex; align-items:center; gap:10px; font-weight:700; font-size:14px; }
    .pay-radio-dot { width:18px; height:18px; border-radius:50%; border:2px solid var(--stone); flex-shrink:0; position:relative; transition: border-color .15s ease; }
    .pay-option.selected .pay-radio-dot { border-color: var(--accent); }
    .pay-option.selected .pay-radio-dot::after { content:''; position:absolute; inset:3px; border-radius:50%; background:var(--accent); }
    .pay-detail-box { margin-top:10px; padding-top:10px; border-top:1px dashed var(--stone); font-size:12.5px; color:var(--mute); }

    .review-line { display:flex; gap:12px; padding:10px 0; border-bottom:1px solid var(--stone); font-size:13px; }
    .review-line:last-child { border-bottom:none; }
    .review-line img { width:48px; height:56px; object-fit:cover; border-radius:6px; }
    .summary-card { border:1px solid var(--stone); border-radius:18px; padding:24px; position:sticky; top:90px; }
    .summary-row { display:flex; justify-content:space-between; font-size:13.5px; color:var(--mute); padding:7px 0; }
    .summary-row.total { border-top:1px solid var(--stone); margin-top:8px; padding-top:14px; font-size:16px; font-weight:700; color:var(--ink); }
    .place-order-btn { width:100%; padding:13px; font-size:15px; margin-top:16px; }
    .toggle-new-addr { font-size:13px; font-weight:600; color:var(--accent); background:none; border:none; cursor:pointer; text-decoration:underline; margin-top:4px; }
    @media (max-width:860px){ .checkout-wrap{grid-template-columns:1fr;} .form-row{grid-template-columns:1fr;} .summary-card{position:static;} }
</style>
@endpush

@section('content')
<div class="checkout-head reveal"><h1>Checkout</h1></div>

<form id="checkoutForm" class="checkout-wrap reveal">
    @csrf
    <div>
        <div class="checkout-section">
            <h3>Shipping Address</h3>
            <div id="addressList">
                @foreach ($addresses as $addr)
                    <label class="addr-option {{ $loop->first ? 'selected' : '' }}">
                        <input type="radio" name="address_id" value="{{ $addr->id }}" {{ $loop->first ? 'checked' : '' }} onchange="onAddressChange(event)">
                        <div>
                            <div class="addr-name">{{ $addr->full_name }} — {{ $addr->phone }}</div>
                            <div class="addr-detail">{{ $addr->address_line }}{{ $addr->area ? ', '.$addr->area : '' }}{{ $addr->city ? ', '.$addr->city : '' }} {{ $addr->postal_code }}</div>
                        </div>
                    </label>
                @endforeach
            </div>
            <button type="button" class="toggle-new-addr" onclick="toggleNewAddress()">+ Use a different address</button>

            <div id="newAddressForm" style="{{ $addresses->isEmpty() ? '' : 'display:none;' }} margin-top:14px;">
                <div class="form-row">
                    <div class="form-group">
                        <label>Full Name</label>
                        <input type="text" name="full_name" value="{{ auth()->user()->name }}">
                        <span class="field-error" data-error="full_name"></span>
                    </div>
                    <div class="form-group">
                        <label>Phone</label>
                        <input type="text" name="phone">
                        <span class="field-error" data-error="phone"></span>
                    </div>
                </div>
                <div class="form-group">
                    <label>Address Line</label>
                    <input type="text" name="address_line">
                    <span class="field-error" data-error="address_line"></span>
                </div>
                <div class="form-row">
                    <div class="form-group"><label>Area</label><input type="text" name="area"></div>
                    <div class="form-group"><label>City</label><input type="text" name="city"></div>
                </div>
                <div class="form-group"><label>Postal Code</label><input type="text" name="postal_code"></div>
                <label style="font-size:12.5px; display:flex; gap:6px; align-items:center;">
                    <input type="checkbox" name="save_address" value="1" checked style="width:auto;"> Save this address for future orders
                </label>
            </div>
        </div>

        <div class="checkout-section">
            <h3>Order Review</h3>
            @foreach ($cartItems as $item)
                @php $img = $item->product->images->first(); @endphp
                <div class="review-line">
                    @if($img)<img src="{{ Storage::disk('public')->url($img->path) }}" alt="">@endif
                    <div style="flex:1;">
                        <div style="font-weight:600;">{{ $item->product->name }}</div>
                        @if($item->variant)<div style="font-size:11.5px; color:var(--mute);">{{ $item->variant->label }}</div>@endif
                        <div style="font-size:11.5px; color:var(--mute);">Qty: {{ $item->quantity }}</div>
                    </div>
                    <div style="font-weight:700; font-family:'JetBrains Mono',monospace;">{{ money($item->line_total) }}</div>
                </div>
            @endforeach
            <div class="form-group" style="margin-top:14px;">
                <label>Order Note (optional)</label>
                <textarea name="order_note" rows="2" placeholder="Delivery instructions, gift note, etc."></textarea>
            </div>
        </div>

        <div class="checkout-section">
            <h3>Payment Method</h3>
            @foreach ($paymentMethods as $key => $method)
                <label class="pay-option {{ $loop->first ? 'selected' : '' }}" data-method="{{ $key }}" onclick="selectPayment(this)">
                    <input type="radio" name="payment_method" value="{{ $key }}" {{ $loop->first ? 'checked' : '' }}>
                    <div class="pay-option-head">
                        <span class="pay-radio-dot"></span>
                        {{ $method['label'] }}
                    </div>
                    @if($key !== 'cod')
                        <div class="pay-detail-box">Send payment to: <strong>{{ $method['number'] ?? $method['details'] }}</strong></div>
                    @endif
                </label>
            @endforeach

            <div id="proofFields" style="margin-top:14px; display:none;">
                <div class="form-row">
                    <div class="form-group">
                        <label>Transaction ID</label>
                        <input type="text" name="transaction_id">
                        <span class="field-error" data-error="transaction_id"></span>
                    </div>
                    <div class="form-group"><label>Number you paid from (optional)</label><input type="text" name="sender_number"></div>
                </div>
                <div class="form-group">
                    <label>Payment Screenshot (optional)</label>
                    <input type="file" name="screenshot" accept="image/*">
                    <span class="field-error" data-error="screenshot"></span>
                </div>
            </div>
        </div>
    </div>

    <div class="summary-card">
        <h3>Order Summary</h3>
        <div class="summary-row"><span>Subtotal</span><span id="summarySubtotalValue">{{ money($subtotal) }}</span></div>
        @if($coupon)
            <div class="summary-row" style="color:#166534;"><span>Discount ({{ $coupon->code }})</span><span>−{{ money($discount) }}</span></div>
        @endif

        <div class="summary-row" style="align-items:center;">
            <span>Shipping</span>
            @if($hasPaidShipping)
                <span id="summaryShippingValue">Free</span>
            @else
                <span id="summaryShippingValue">Free</span>
            @endif
        </div>

        @if($hasPaidShipping)
            <div class="form-group" style="margin-top:10px; margin-bottom:0;">
                <label>Shipping Option</label>
                <select name="shipping_option" id="shippingSelect" onchange="onShippingChange()">
                    <option value="free">Free Shipping — ৳0.00</option>
                    <option value="paid">Standard Delivery — {{ money($shippingFeeAmount) }}</option>
                </select>
            </div>
        @else
            <input type="hidden" name="shipping_option" value="free">
        @endif

        <div class="summary-row total"><span>Total</span><span id="summaryTotalValue">{{ money($total) }}</span></div>

        <button type="submit" class="btn btn-primary place-order-btn" id="placeOrderBtn">Place Order</button>
        <p style="font-size:11.5px; color:var(--mute); margin-top:10px;">Payment is manually verified before dispatch.</p>
    </div>
</form>
@endsection

@push('scripts')
<script>
    const SUBTOTAL = {{ $subtotal }};
    const DISCOUNT = {{ $discount }};
    const SHIPPING_FEE_AMOUNT = {{ $hasPaidShipping ? $shippingFeeAmount : 0 }};

    function formatMoney(amount) {
        return '৳' + Number(amount).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }

    function toggleNewAddress() {
        const form = document.getElementById('newAddressForm');
        const isHidden = form.style.display === 'none';
        form.style.display = isHidden ? 'block' : 'none';
        if (isHidden) {
            document.querySelectorAll('input[name="address_id"]').forEach(r => r.checked = false);
            document.querySelectorAll('.addr-option').forEach(el => el.classList.remove('selected'));
        }
    }

    function onAddressChange(e) {
        document.getElementById('newAddressForm').style.display = 'none';
        document.querySelectorAll('.addr-option').forEach(el => el.classList.remove('selected'));
        e.target.closest('.addr-option').classList.add('selected');
    }

    // Entire label click drives selection — not dependent on native radio hitbox
    function selectPayment(labelEl) {
        const radio = labelEl.querySelector('input[type="radio"]');
        radio.checked = true;

        document.querySelectorAll('.pay-option').forEach(el => el.classList.remove('selected'));
        labelEl.classList.add('selected');

        document.getElementById('proofFields').style.display = labelEl.dataset.method === 'cod' ? 'none' : 'block';
    }

    function onShippingChange() {
        const select = document.getElementById('shippingSelect');
        const isPaid = select && select.value === 'paid';
        const shippingFee = isPaid ? SHIPPING_FEE_AMOUNT : 0;

        document.getElementById('summaryShippingValue').textContent = shippingFee > 0 ? formatMoney(shippingFee) : 'Free';

        const total = Math.max(SUBTOTAL - DISCOUNT + shippingFee, 0);
        document.getElementById('summaryTotalValue').textContent = formatMoney(total);
    }

    document.addEventListener('DOMContentLoaded', () => {
        const checked = document.querySelector('input[name="payment_method"]:checked');
        if (checked) document.getElementById('proofFields').style.display = checked.value === 'cod' ? 'none' : 'block';
    });

    document.getElementById('checkoutForm').addEventListener('submit', async (e) => {
        e.preventDefault();
        document.querySelectorAll('.field-error').forEach(el => el.textContent = '');

        const btn = document.getElementById('placeOrderBtn');
        btn.disabled = true;
        const originalText = btn.textContent;
        btn.textContent = 'Placing order...';

        const formData = new FormData(e.target);

        try {
            const res = await fetch("{{ route('checkout.store') }}", {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
                body: formData,
            });
            const json = await res.json().catch(() => ({}));

            if (!res.ok) {
                if (json.errors) {
                    Object.entries(json.errors).forEach(([field, messages]) => {
                        const errEl = document.querySelector(`[data-error="${field}"]`);
                        if (errEl) errEl.textContent = messages[0];
                    });
                }
                showToast(json.message || 'Could not place order. Please check the form.', 'error');
                btn.disabled = false;
                btn.textContent = originalText;
                return;
            }

            showToast('Order placed successfully!');
            window.location.href = json.redirect;
        } catch {
            showToast('Network error — please try again.', 'error');
            btn.disabled = false;
            btn.textContent = originalText;
        }
    });
</script>
@endpush