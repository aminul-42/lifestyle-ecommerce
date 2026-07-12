@extends('layouts.app')

@section('title', 'Orders')
@section('page-title', 'Orders')
@section('page-subtitle', 'Track orders and verify payments')

@section('content')

    {{-- ── Stat Cards ─────────────────────────────────────── --}}
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon" style="background:#dbeafe;">
                <svg fill="none" stroke="#1e40af" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 2h6l1 4H8l1-4zM4 7h16l-1.5 13a2 2 0 01-2 2H7.5a2 2 0 01-2-2L4 7z"/></svg>
            </div>
            <div class="stat-info">
                <p>Total Orders</p>
                <strong>{{ $counts['total'] }}</strong>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="background:#fef3c7;">
                <svg fill="none" stroke="#92400e" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div class="stat-info">
                <p>Awaiting Verification</p>
                <strong>{{ $counts['awaiting_verification'] }}</strong>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="background:#ede9fe;">
                <svg fill="none" stroke="#5b21b6" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
            </div>
            <div class="stat-info">
                <p>Processing</p>
                <strong>{{ $counts['processing'] }}</strong>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="background:#d1fae5;">
                <svg fill="none" stroke="#065f46" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
            </div>
            <div class="stat-info">
                <p>Delivered</p>
                <strong>{{ $counts['delivered'] }}</strong>
            </div>
        </div>
    </div>

    <div class="table-wrap">
        <div class="table-toolbar">
            <span class="table-toolbar-title">
                All Orders
                <span class="count-badge">{{ $orders->total() }}</span>
            </span>
            <div class="table-toolbar-actions">
                <form method="GET" action="{{ route('admin.orders.index') }}" style="display:flex; gap:0.5rem; flex-wrap:wrap;">
                    <div class="search-box">
                        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Order #, name, phone...">
                    </div>
                    <select name="status" onchange="this.form.submit()" style="padding:0.45rem 0.75rem; border:1.5px solid var(--border); border-radius:9px; font-size:0.875rem; background:#fafafa;">
                        <option value="">All Statuses</option>
                        @foreach (['processing','confirmed','shipped','delivered','cancelled'] as $s)
                            <option value="{{ $s }}" {{ request('status') == $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
                        @endforeach
                    </select>
                    <select name="payment_status" onchange="this.form.submit()" style="padding:0.45rem 0.75rem; border:1.5px solid var(--border); border-radius:9px; font-size:0.875rem; background:#fafafa;">
                        <option value="">All Payment Status</option>
                        @foreach (['pending','submitted','verified','rejected'] as $p)
                            <option value="{{ $p }}" {{ request('payment_status') == $p ? 'selected' : '' }}>{{ ucfirst($p) }}</option>
                        @endforeach
                    </select>
                    <button type="submit" class="btn btn-secondary btn-sm">Filter</button>
                    @if (request()->hasAny(['search','status','payment_status']))
                        <a href="{{ route('admin.orders.index') }}" class="btn btn-secondary btn-sm">Clear</a>
                    @endif
                </form>
            </div>
        </div>

        <table>
            <thead>
                <tr>
                    <th>Order #</th>
                    <th>Customer</th>
                    <th>Total</th>
                    <th>Payment</th>
                    <th>Status</th>
                    <th>Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($orders as $order)
                    <tr>
                        <td><strong>{{ $order->order_number }}</strong></td>
                        <td>
                            {{ $order->full_name }}
                            <div style="font-size:0.75rem; color:var(--text-muted);">{{ $order->phone }}</div>
                        </td>
                        <td>{{ money($order->total) }}</td>
                        <td>
                            <span class="badge badge-{{ $order->paymentBadgeColor() }}">{{ $order->payment_status }}</span>
                        </td>
                        <td>
                            <span class="badge badge-{{ $order->statusBadgeColor() }}">{{ $order->status }}</span>
                        </td>
                        <td>{{ $order->created_at->format('M d, Y') }}</td>
                        <td>
                            <div class="action-btns">
                                <a href="{{ route('admin.orders.show', $order) }}" class="btn btn-secondary btn-sm">View</a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="empty-state">
                            <svg fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 2h6l1 4H8l1-4zM4 7h16l-1.5 13a2 2 0 01-2 2H7.5a2 2 0 01-2-2L4 7z"/></svg>
                            <p>No orders found.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        @if ($orders->hasPages())
            <div class="pagination-wrap">
                @if ($orders->onFirstPage())
                    <span class="page-btn disabled">‹</span>
                @else
                    <a href="{{ $orders->previousPageUrl() }}" class="page-btn">‹</a>
                @endif

                @foreach ($orders->getUrlRange(1, $orders->lastPage()) as $page => $url)
                    @if ($page == $orders->currentPage())
                        <span class="page-btn active">{{ $page }}</span>
                    @else
                        <a href="{{ $url }}" class="page-btn">{{ $page }}</a>
                    @endif
                @endforeach

                @if ($orders->hasMorePages())
                    <a href="{{ $orders->nextPageUrl() }}" class="page-btn">›</a>
                @else
                    <span class="page-btn disabled">›</span>
                @endif

                <span class="page-info">{{ $orders->firstItem() }}–{{ $orders->lastItem() }} of {{ $orders->total() }}</span>
            </div>
        @endif
    </div>

@endsection