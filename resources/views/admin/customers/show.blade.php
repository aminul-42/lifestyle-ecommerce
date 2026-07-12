@extends('layouts.app')

@section('title', 'Customer Details')
@section('page-title', $customer->name)
@section('page-subtitle', 'Customer profile & order history')

@section('content')

    <a href="{{ route('admin.customers.index') }}" style="display:inline-flex; align-items:center; gap:0.4rem; color:var(--text-muted); font-size:0.85rem; font-weight:600; text-decoration:none; margin-bottom:1.25rem;">
        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
        Back to Customers
    </a>

    <div style="display:grid; grid-template-columns: 320px 1fr; gap:1.5rem; align-items:start;">

        {{-- ── Profile Card ──────────────────────────────── --}}
        <div class="table-wrap" style="padding:1.5rem;">
            <div style="text-align:center; margin-bottom:1.25rem;">
                @if ($customer->avatar)
                    <img src="{{ $customer->avatar }}" alt="{{ $customer->name }}" style="width:72px; height:72px; border-radius:50%; object-fit:cover; margin:0 auto 0.75rem;">
                @else
                    <div style="width:72px; height:72px; border-radius:50%; background:linear-gradient(135deg, var(--blue), #4c6ef5); color:#fff; display:flex; align-items:center; justify-content:center; font-size:1.5rem; font-weight:700; margin:0 auto 0.75rem;">
                        {{ strtoupper(substr($customer->name, 0, 1)) }}
                    </div>
                @endif
                <h3 style="font-size:1.05rem; font-weight:700;">{{ $customer->name }}</h3>
                <p style="font-size:0.8rem; color:var(--text-muted);">{{ $customer->email }}</p>
                <div style="margin-top:0.6rem;">
                    @if ($customer->is_active)
                        <span class="badge badge-green" style="cursor:pointer;" onclick="toggleStatus({{ $customer->id }})" title="Click to deactivate">Active</span>
                    @else
                        <span class="badge badge-gray" style="cursor:pointer;" onclick="toggleStatus({{ $customer->id }})" title="Click to activate">Inactive</span>
                    @endif
                </div>
            </div>

            <div style="border-top:1px solid var(--border); padding-top:1rem; display:flex; flex-direction:column; gap:0.75rem; font-size:0.85rem;">
                <div style="display:flex; justify-content:space-between;">
                    <span style="color:var(--text-muted);">Phone</span>
                    <strong>{{ $customer->phone ?? '—' }}</strong>
                </div>
                <div style="display:flex; justify-content:space-between;">
                    <span style="color:var(--text-muted);">Joined</span>
                    <strong>{{ $customer->created_at->format('d M, Y') }}</strong>
                </div>
                <div style="display:flex; justify-content:space-between;">
                    <span style="color:var(--text-muted);">Login Method</span>
                    <strong>{{ $customer->google_id ? 'Google' : 'Email' }}</strong>
                </div>
                <div style="display:flex; justify-content:space-between;">
                    <span style="color:var(--text-muted);">Total Orders</span>
                    <strong>{{ $stats['orders_count'] }}</strong>
                </div>
                <div style="display:flex; justify-content:space-between;">
                    <span style="color:var(--text-muted);">Total Spent</span>
                    <strong style="color:var(--blue);">{{ money($stats['total_spent']) }}</strong>
                </div>
                <div style="display:flex; justify-content:space-between;">
                    <span style="color:var(--text-muted);">Reviews Written</span>
                    <strong>{{ $stats['reviews_count'] }}</strong>
                </div>
            </div>

            {{-- ── Addresses ─────────────────────────────── --}}
            @if ($customer->addresses->isNotEmpty())
                <div style="border-top:1px solid var(--border); padding-top:1rem; margin-top:1rem;">
                    <p style="font-size:0.75rem; font-weight:700; text-transform:uppercase; letter-spacing:0.05em; color:var(--text-muted); margin-bottom:0.65rem;">Saved Addresses</p>
                    @foreach ($customer->addresses as $address)
                        @php
                            $parts = array_filter([
                                $address->address_line ?? $address->line1 ?? null,
                                $address->area ?? null,
                                $address->city ?? null,
                            ]);
                        @endphp
                        <div style="font-size:0.8rem; color:#374151; line-height:1.6; padding:0.5rem 0; border-bottom:1px solid #f3f4f6;">
                            {{ implode(', ', $parts) ?: '—' }}
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- ── Orders + Reviews ──────────────────────────── --}}
        <div style="display:flex; flex-direction:column; gap:1.5rem;">

            <div class="table-wrap">
                <div class="table-toolbar">
                    <span class="table-toolbar-title">
                        Order History
                        <span class="count-badge">{{ $customer->orders->count() }}</span>
                    </span>
                </div>
                <table>
                    <thead>
                        <tr>
                            <th>Order #</th>
                            <th>Date</th>
                            <th>Total</th>
                            <th>Payment</th>
                            <th>Status</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($customer->orders as $order)
                            <tr>
                                <td><strong>{{ $order->order_number }}</strong></td>
                                <td>{{ $order->created_at->format('d M, Y') }}</td>
                                <td>{{ money($order->total) }}</td>
                                <td><span class="badge badge-{{ $order->paymentBadgeColor() }}">{{ ucfirst($order->payment_status) }}</span></td>
                                <td><span class="badge badge-{{ $order->statusBadgeColor() }}">{{ ucfirst($order->status) }}</span></td>
                                <td>
                                    <a href="{{ route('admin.orders.show', $order) }}" class="btn btn-secondary btn-sm">View</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="empty-state">
                                    <p>No orders placed yet.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="table-wrap">
                <div class="table-toolbar">
                    <span class="table-toolbar-title">
                        Reviews Written
                        <span class="count-badge">{{ $customer->reviews->count() }}</span>
                    </span>
                </div>
                <table>
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Rating</th>
                            <th>Comment</th>
                            <th>Status</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($customer->reviews as $review)
                            <tr>
                                <td><strong>{{ $review->product->name ?? '—' }}</strong></td>
                                <td>
                                    <span style="color:#f59e0b; letter-spacing:1px;">
                                        {{ str_repeat('★', $review->rating) }}{{ str_repeat('☆', 5 - $review->rating) }}
                                    </span>
                                </td>
                                <td style="max-width:260px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">{{ $review->comment ?? '—' }}</td>
                                <td>
                                    @if ($review->is_approved)
                                        <span class="badge badge-green">Approved</span>
                                    @else
                                        <span class="badge badge-yellow">Pending</span>
                                    @endif
                                </td>
                                <td>{{ $review->created_at->format('d M, Y') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="empty-state">
                                    <p>No reviews written yet.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

        </div>
    </div>

@endsection

@push('scripts')
<script>
    const ROUTES = {
        toggleBase: "{{ url('admin/customers') }}", // + /{id}/toggle
    };

    async function parseJson(res) {
        try { return (await res.json()) ?? {}; } catch { return {}; }
    }

    async function toggleStatus(id) {
        try {
            const res = await fetch(`${ROUTES.toggleBase}/${id}/toggle`, {
                method: 'PATCH',
                headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
            });
            const json = await parseJson(res);

            if (!res.ok) {
                showToast(json.message || 'Could not update status.', 'error');
                return;
            }

            showToast(json.message || 'Status updated.', 'success');
            setTimeout(() => location.reload(), 600);
        } catch {
            showToast('Network error — please try again.', 'error');
        }
    }
</script>
@endpush