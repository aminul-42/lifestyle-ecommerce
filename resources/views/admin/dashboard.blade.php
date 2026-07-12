@extends('layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')
@section('page-subtitle', 'Overview of your store performance')

@section('content')

    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon" style="background:#eef2ff;">
                <svg fill="none" stroke="#3b5bdb" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                </svg>
            </div>
            <div class="stat-info">
                <p>Total Orders</p>
                <strong>{{ $stats['total_orders'] }}</strong>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon" style="background:#fef3c7;">
                <svg fill="none" stroke="#92400e" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div class="stat-info">
                <p>Pending Verification</p>
                <strong>{{ $stats['pending_payments'] }}</strong>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon" style="background:#dbeafe;">
                <svg fill="none" stroke="#1e40af" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                </svg>
            </div>
            <div class="stat-info">
                <p>Total Products</p>
                <strong>{{ $stats['total_products'] }}</strong>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon" style="background:#ede9fe;">
                <svg fill="none" stroke="#5b21b6" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a4 4 0 00-3-3.87M9 20H4v-2a4 4 0 013-3.87m6-3.13a4 4 0 10-4-4 4 4 0 004 4zm6 0a4 4 0 10-4-4"/>
                </svg>
            </div>
            <div class="stat-info">
                <p>Total Customers</p>
                <strong>{{ $stats['total_customers'] }}</strong>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon" style="background:#d1fae5;">
                <svg fill="none" stroke="#065f46" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V6m0 8v2m9-4a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div class="stat-info">
                <p>Verified Revenue</p>
                <strong>{{ money($stats['total_revenue']) }}</strong>
            </div>
        </div>
    </div>

    <div class="table-wrap">
        <div class="table-toolbar">
            <div class="table-toolbar-title">Recent Orders</div>
        </div>
        <div class="table-scroll">
            <table>
                <thead>
                    <tr>
                        <th>Order #</th>
                        <th>Customer</th>
                        <th>Total</th>
                        <th>Payment</th>
                        <th>Status</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentOrders as $order)
                        <tr>
                            <td>{{ $order->order_number }}</td>
                            <td>{{ $order->user->name ?? $order->full_name }}</td>
                            <td>{{ money($order->total) }}</td>
                            <td><span class="badge badge-{{ $order->paymentBadgeColor() }}">{{ ucfirst($order->payment_status) }}</span></td>
                            <td><span class="badge badge-{{ $order->statusBadgeColor() }}">{{ ucfirst($order->status) }}</span></td>
                            <td>{{ $order->created_at->format('d M, Y') }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="empty-state">No orders yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

@endsection