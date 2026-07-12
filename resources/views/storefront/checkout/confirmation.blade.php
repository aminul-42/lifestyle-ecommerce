@extends('layouts.storefront')

@section('title', 'Order Confirmed — ' . setting('site_name', config('app.name')))

@push('styles')
<style>
    .confirm-wrap { max-width:640px; margin:0 auto; text-align:center; padding:40px 20px; }
    .confirm-icon { width:64px; height:64px; border-radius:50%; background:var(--accent-soft); display:flex; align-items:center; justify-content:center; margin:0 auto 20px; }
    .confirm-icon svg { width:30px; height:30px; color:var(--ink); }
    .confirm-wrap h1 { font-family:'Fraunces',Georgia,serif; font-size:28px; margin-bottom:10px; }
    .order-num { font-family:'JetBrains Mono',monospace; font-weight:700; color:var(--accent); }
    .order-summary-box { border:1px solid var(--stone); border-radius:16px; padding:22px; margin-top:30px; text-align:left; }
    .order-summary-box .row { display:flex; justify-content:space-between; padding:8px 0; font-size:13.5px; border-bottom:1px solid var(--stone); }
    .order-summary-box .row:last-child { border-bottom:none; }
</style>
@endpush

@section('content')

<div class="confirm-wrap reveal">
    <div class="confirm-icon">
        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
    </div>
    <h1>Thank you for your order!</h1>
    <p style="color:var(--mute); font-size:14px;">Your order <span class="order-num">{{ $order->order_number }}</span> has been placed.</p>

    @if($order->payment_method === 'cod')
        <p style="margin-top:14px; font-size:13.5px;">You'll pay <strong>{{ money($order->total) }}</strong> in cash on delivery.</p>
    @else
        <p style="margin-top:14px; font-size:13.5px;">We're verifying your payment. You'll see the update on your Order History page shortly.</p>
    @endif

    <div class="order-summary-box">
        @foreach ($order->items as $item)
            <div class="row">
                <span>{{ $item->product_name }} @if($item->size || $item->color)<span style="color:var(--mute);">({{ trim(($item->size ?? '').' '.($item->color ?? '')) }})</span>@endif × {{ $item->quantity }}</span>
                <span>{{ money($item->line_total) }}</span>
            </div>
        @endforeach
        @if($order->discount > 0)
            <div class="row"><span>Discount</span><span>−{{ money($order->discount) }}</span></div>
        @endif
        <div class="row"><span>Shipping</span><span>{{ $order->shipping_fee > 0 ? money($order->shipping_fee) : 'Free' }}</span></div>
        <div class="row" style="font-weight:700;"><span>Total</span><span>{{ money($order->total) }}</span></div>
    </div>

    <a href="{{ route('shop.index') }}" class="btn btn-primary" style="margin-top:24px;">Continue Shopping</a>
</div>
@endsection