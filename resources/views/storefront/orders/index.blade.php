@extends('layouts.storefront')

@section('title', 'My Orders — ' . setting('site_name', config('app.name')))

@push('styles')
<style>
    .order-card { border:1px solid var(--stone); border-radius:16px; padding:20px; margin-bottom:16px; display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:12px; }
    .order-card .num { font-family:'JetBrains Mono',monospace; font-weight:700; }
    .order-card .meta { font-size:12.5px; color:var(--mute); }
    .badge { font-size:11px; font-weight:700; padding:4px 10px; border-radius:999px; text-transform:capitalize; }
    .badge-yellow { background:#fef3c7; color:#92400e; }
    .badge-blue { background:#dbeafe; color:#1e40af; }
    .badge-purple { background:#ede9fe; color:#5b21b6; }
    .badge-green { background:#dcfce7; color:#166534; }
    .badge-red { background:#fee2e2; color:#991b1b; }
    .badge-gray { background:#f3f4f6; color:#374151; }
</style>
@endpush

@section('content')
<h1 class="zf-serif reveal" style="font-size:28px; margin-bottom:24px;">My Orders</h1>

@forelse ($orders as $order)
    <a href="{{ route('orders.show', $order->order_number) }}" class="order-card reveal">
        <div>
            <div class="num">{{ $order->order_number }}</div>
            <div class="meta">{{ $order->created_at->format('M d, Y') }} · {{ $order->items->count() }} item(s)</div>
        </div>
        <div style="display:flex; gap:8px;">
            <span class="badge badge-{{ $order->statusBadgeColor() }}">{{ $order->status }}</span>
            <span class="badge badge-{{ $order->paymentBadgeColor() }}">{{ $order->payment_status }}</span>
        </div>
        <div style="font-weight:700; font-family:'JetBrains Mono',monospace;">{{ money($order->total) }}</div>
    </a>
@empty
    <p style="color:var(--mute);">You haven't placed any orders yet.</p>
@endforelse

{{ $orders->links() }}
@endsection