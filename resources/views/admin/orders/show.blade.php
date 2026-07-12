@extends('layouts.app')

@section('title', 'Order '.$order->order_number)
@section('page-title', $order->order_number)
@section('page-subtitle', 'Placed '.$order->created_at->format('M d, Y \a\t h:i A'))

@section('content')

    <div style="margin-bottom:1.25rem;">
        <a href="{{ route('admin.orders.index') }}" style="display:inline-flex; align-items:center; gap:0.4rem; font-size:0.8125rem; color:var(--text-muted); text-decoration:none; font-weight:600;">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
            Back to Orders
        </a>
    </div>

    <div style="display:grid; grid-template-columns: 2fr 1fr; gap:1.5rem; align-items:start;">

        {{-- ── Left column ─────────────────────────────────── --}}
        <div style="display:flex; flex-direction:column; gap:1.5rem;">

            {{-- Order Items --}}
            <div class="table-wrap">
                <div class="table-toolbar">
                    <span class="table-toolbar-title">Order Items <span class="count-badge">{{ $order->items->count() }}</span></span>
                </div>
                <table>
                    <thead>
                        <tr>
                            <th></th>
                            <th>Product</th>
                            <th>Size / Color</th>
                            <th>Price</th>
                            <th>Qty</th>
                            <th>Line Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($order->items as $item)
                            <tr>
                                <td>
                                    <img src="{{ $item->image_url }}" alt="{{ $item->product_name }}"
                                         style="width:42px; height:42px; border-radius:8px; object-fit:cover; display:block;">
                                </td>
                                <td>
                                    <strong>{{ $item->product_name }}</strong>
                                    @if (!$item->product)
                                        <div style="font-size:0.7rem; color:var(--text-muted);">(product removed)</div>
                                    @endif
                                </td>
                                <td>
                                    @if ($item->size || $item->color)
                                        {{ implode(' / ', array_filter([$item->size, $item->color])) }}
                                    @else
                                        —
                                    @endif
                                </td>
                                <td>{{ money($item->price) }}</td>
                                <td>{{ $item->quantity }}</td>
                                <td><strong>{{ money($item->line_total) }}</strong></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                {{-- Totals --}}
                <div style="padding:1rem 1.25rem; border-top:1px solid var(--border); display:flex; flex-direction:column; gap:0.5rem; max-width:280px; margin-left:auto; font-size:0.875rem;">
                    <div style="display:flex; justify-content:space-between; color:var(--text-muted);">
                        <span>Subtotal</span><span>{{ money($order->subtotal) }}</span>
                    </div>
                    @if ($order->discount > 0)
                        <div style="display:flex; justify-content:space-between; color:var(--success);">
                            <span>Discount @if($order->coupon)({{ $order->coupon->code }})@endif</span><span>-{{ money($order->discount) }}</span>
                        </div>
                    @endif
                    <div style="display:flex; justify-content:space-between; color:var(--text-muted);">
                        <span>Shipping</span><span>{{ money($order->shipping_fee) }}</span>
                    </div>
                    <div style="display:flex; justify-content:space-between; font-weight:700; font-size:1rem; padding-top:0.5rem; border-top:1px solid var(--border);">
                        <span>Total</span><span>{{ money($order->total) }}</span>
                    </div>
                </div>
            </div>

            {{-- Payment Proofs --}}
            <div class="table-wrap">
                <div class="table-toolbar">
                    <span class="table-toolbar-title">Payment Verification</span>
                    <span class="badge badge-{{ $order->paymentBadgeColor() }}">{{ ucfirst($order->payment_status) }}</span>
                </div>

                <div style="padding:1.25rem;">
                    @forelse ($order->paymentProofs as $proof)
                        <div style="border:1px solid var(--border); border-radius:var(--radius); padding:1.25rem; {{ !$loop->last ? 'margin-bottom:1rem;' : '' }}">
                            <div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:1rem;">
                                <div>
                                    <strong style="text-transform:uppercase; font-size:0.8125rem;">{{ $proof->method }}</strong>
                                    <div style="font-size:0.75rem; color:var(--text-muted); margin-top:0.15rem;">
                                        Submitted {{ $proof->created_at->format('M d, Y h:i A') }}
                                    </div>
                                </div>
                                <span class="badge badge-{{ $proof->status === 'verified' ? 'green' : ($proof->status === 'rejected' ? 'red' : 'yellow') }}">
                                    {{ ucfirst($proof->status) }}
                                </span>
                            </div>

                            <div class="form-row" style="margin-bottom:0.75rem;">
                                <div>
                                    <div style="font-size:0.75rem; color:var(--text-muted); margin-bottom:0.2rem;">Transaction ID</div>
                                    <strong style="font-size:0.9rem;">{{ $proof->transaction_id }}</strong>
                                </div>
                                @if ($proof->sender_number)
                                    <div>
                                        <div style="font-size:0.75rem; color:var(--text-muted); margin-bottom:0.2rem;">Sent From</div>
                                        <strong style="font-size:0.9rem;">{{ $proof->sender_number }}</strong>
                                    </div>
                                @endif
                            </div>

                            @if ($proof->customer_note)
                                <div style="font-size:0.8125rem; color:var(--text-muted); background:var(--bg); padding:0.6rem 0.75rem; border-radius:8px; margin-bottom:0.75rem;">
                                    "{{ $proof->customer_note }}"
                                </div>
                            @endif

                            @if ($proof->screenshot_url)
                                <a href="{{ $proof->screenshot_url }}" target="_blank" style="display:inline-block; margin-bottom:0.75rem;">
                                    <img src="{{ $proof->screenshot_url }}" alt="Payment screenshot"
                                         style="max-width:180px; border-radius:8px; border:1px solid var(--border); display:block;">
                                </a>
                            @endif

                            @if ($proof->status === 'rejected' && $proof->admin_note)
                                <div style="font-size:0.8125rem; color:#991b1b; background:#fee2e2; padding:0.6rem 0.75rem; border-radius:8px; margin-bottom:0.75rem;">
                                    <strong>Rejection reason:</strong> {{ $proof->admin_note }}
                                </div>
                            @endif

                            @if ($proof->status !== 'pending')
                                <div style="font-size:0.75rem; color:var(--text-muted);">
                                    {{ ucfirst($proof->status) }} by {{ $proof->verifier->name ?? '—' }} on {{ $proof->verified_at?->format('M d, Y h:i A') }}
                                </div>
                            @endif

                           @if ($proof->status === 'pending' && in_array($order->payment_status, ['pending', 'submitted']))
                                <div style="display:flex; gap:0.6rem; margin-top:0.5rem;">
                                    <button class="btn btn-success btn-sm" onclick="verifyPayment({{ $proof->id }})" id="verifyBtn{{ $proof->id }}">
                                        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                        Verify Payment
                                    </button>
                                    <button class="btn btn-danger btn-sm" onclick="openRejectModal({{ $proof->id }})">
                                        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                                        Reject
                                    </button>
                                </div>
                            @endif
                        </div>
                    @empty
                        <p style="color:var(--text-muted); font-size:0.875rem; text-align:center; padding:1rem 0;">
                            No payment proof submitted yet{{ $order->payment_method === 'cod' ? ' (Cash on Delivery).' : '.' }}
                        </p>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- ── Right column ────────────────────────────────── --}}
        <div style="display:flex; flex-direction:column; gap:1.5rem;">

            {{-- Order Status --}}
            <div class="table-wrap">
                <div class="table-toolbar">
                    <span class="table-toolbar-title">Fulfillment Status</span>
                </div>
                <form id="statusForm" style="padding:1.25rem;">
                    <div class="form-group">
                        <label>Status</label>
                        <select name="status" id="statusSelect">
                            @foreach (['processing','confirmed','shipped','delivered','cancelled'] as $s)
                                <option value="{{ $s }}" {{ $order->status === $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Tracking Number</label>
                        <input type="text" name="tracking_number" id="trackingInput" value="{{ $order->tracking_number }}" placeholder="Optional">
                        <span class="field-error" id="err-tracking_number"></span>
                    </div>
                    <button type="button" class="btn btn-primary" style="width:100%; justify-content:center;" id="saveStatusBtn" onclick="saveStatus()">
                        <span class="btn-text">Save Status</span>
                        <span class="btn-loader" style="display:none;">
                            <svg class="spin" viewBox="0 0 24 24" width="16" height="16" fill="none"><circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3" stroke-dasharray="30 70" opacity=".3"/><path d="M12 2a10 10 0 0110 10" stroke="currentColor" stroke-width="3" stroke-linecap="round"/></svg>
                            Saving...
                        </span>
                    </button>
                </form>
            </div>

            {{-- Customer & Shipping --}}
            <div class="table-wrap">
                <div class="table-toolbar">
                    <span class="table-toolbar-title">Customer & Shipping</span>
                </div>
                <div style="padding:1.25rem; display:flex; flex-direction:column; gap:0.85rem; font-size:0.875rem;">
                    <div>
                        <div style="font-size:0.75rem; color:var(--text-muted); margin-bottom:0.15rem;">Customer</div>
                        <strong>{{ $order->user->name ?? $order->full_name }}</strong>
                        @if ($order->user)
                            <div style="font-size:0.8rem; color:var(--text-muted);">{{ $order->user->email }}</div>
                        @endif
                    </div>
                    <div>
                        <div style="font-size:0.75rem; color:var(--text-muted); margin-bottom:0.15rem;">Phone</div>
                        {{ $order->phone }}
                    </div>
                    <div>
                        <div style="font-size:0.75rem; color:var(--text-muted); margin-bottom:0.15rem;">Shipping Address</div>
                        {{ $order->address_line }}
                        @if ($order->area), {{ $order->area }}@endif
                        @if ($order->city)<br>{{ $order->city }}@endif
                        @if ($order->postal_code) - {{ $order->postal_code }}@endif
                    </div>
                    @if ($order->order_note)
                        <div>
                            <div style="font-size:0.75rem; color:var(--text-muted); margin-bottom:0.15rem;">Order Note</div>
                            {{ $order->order_note }}
                        </div>
                    @endif
                    <div>
                        <div style="font-size:0.75rem; color:var(--text-muted); margin-bottom:0.15rem;">Payment Method</div>
                        <span class="badge badge-blue">{{ strtoupper($order->payment_method) }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ── Reject Reason Modal ─────────────────────────────── --}}
    <div class="modal-overlay" id="rejectModal" onclick="closeOnOverlay(event,'rejectModal')">
        <div class="modal" style="max-width:420px;">
            <div class="modal-header">
                <h3 style="color:#dc2626;">Reject Payment</h3>
                <button class="modal-close" onclick="closeRejectModal()">
                    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label>Reason (optional, shown to customer context)</label>
                    <textarea id="rejectReason" rows="3" placeholder="e.g. Transaction ID does not match any received payment"></textarea>
                    <span class="field-error" id="err-reason"></span>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" onclick="closeRejectModal()">Cancel</button>
                <button class="btn btn-danger" id="confirmRejectBtn" onclick="confirmReject()">
                    <span class="btn-text">Reject Payment</span>
                    <span class="btn-loader" style="display:none;">
                        <svg class="spin" viewBox="0 0 24 24" width="16" height="16" fill="none"><circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3" stroke-dasharray="30 70" opacity=".3"/><path d="M12 2a10 10 0 0110 10" stroke="currentColor" stroke-width="3" stroke-linecap="round"/></svg>
                        Rejecting...
                    </span>
                </button>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
<script>
    const paymentVerifyBase = "{{ route('admin.orders.payment.verify', ['paymentProof' => '__ID__']) }}";
    const paymentRejectBase = "{{ route('admin.orders.payment.reject', ['paymentProof' => '__ID__']) }}";

    async function parseJson(res) {
        try { return (await res.json()) ?? {}; } catch { return {}; }
    }

    function clearErrors() {
        document.querySelectorAll('.field-error').forEach(e => e.textContent = '');
    }

    // ── Status update ───────────────────────────────────────
    async function saveStatus() {
        clearErrors();
        const btn = document.getElementById('saveStatusBtn');
        btn.disabled = true;
        btn.querySelector('.btn-text').style.display = 'none';
        btn.querySelector('.btn-loader').style.display = 'inline-flex';

        try {
            const res = await fetch("{{ route('admin.orders.status', $order) }}", {
                method: 'PATCH',
                headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json', 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    status: document.getElementById('statusSelect').value,
                    tracking_number: document.getElementById('trackingInput').value,
                }),
            });
            const json = await parseJson(res);

            if (!res.ok) {
                if (json.errors) {
                    Object.entries(json.errors).forEach(([field, msgs]) => {
                        const el = document.getElementById(`err-${field}`);
                        if (el) el.textContent = msgs[0];
                    });
                }
                showToast(json.message || 'Could not update status.', 'error');
                return;
            }

            showToast(json.message || 'Status updated.', 'success');
            setTimeout(() => location.reload(), 800);
        } catch {
            showToast('Network error — please try again.', 'error');
        } finally {
            btn.disabled = false;
            btn.querySelector('.btn-text').style.display = 'inline';
            btn.querySelector('.btn-loader').style.display = 'none';
        }
    }

    // ── Verify payment ──────────────────────────────────────
    async function verifyPayment(proofId) {
        const btn = document.getElementById(`verifyBtn${proofId}`);
        btn.disabled = true;

        try {
            const url = paymentVerifyBase.replace('__ID__', proofId);
            const res = await fetch(url, {
                method: 'PATCH',
                headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
            });
            const json = await parseJson(res);

            if (!res.ok) {
                showToast(json.message || 'Could not verify payment.', 'error');
                btn.disabled = false;
                return;
            }

            showToast(json.message || 'Payment verified.', 'success');
            setTimeout(() => location.reload(), 800);
        } catch {
            showToast('Network error — please try again.', 'error');
            btn.disabled = false;
        }
    }

    // ── Reject payment ──────────────────────────────────────
    let rejectTargetId = null;

    function openRejectModal(proofId) {
        rejectTargetId = proofId;
        document.getElementById('rejectReason').value = '';
        document.getElementById('err-reason').textContent = '';
        document.getElementById('rejectModal').classList.add('open');
        document.body.style.overflow = 'hidden';
    }
    function closeRejectModal() {
        document.getElementById('rejectModal').classList.remove('open');
        document.body.style.overflow = '';
    }
    function closeOnOverlay(e, id) {
        if (e.target === document.getElementById(id)) closeRejectModal();
    }

    async function confirmReject() {
        const btn = document.getElementById('confirmRejectBtn');
        btn.disabled = true;
        btn.querySelector('.btn-text').style.display = 'none';
        btn.querySelector('.btn-loader').style.display = 'inline-flex';

        try {
            const url = paymentRejectBase.replace('__ID__', rejectTargetId);
            const res = await fetch(url, {
                method: 'PATCH',
                headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json', 'Content-Type': 'application/json' },
                body: JSON.stringify({ reason: document.getElementById('rejectReason').value }),
            });
            const json = await parseJson(res);

            if (!res.ok) {
                if (json.errors?.reason) {
                    document.getElementById('err-reason').textContent = json.errors.reason[0];
                }
                showToast(json.message || 'Could not reject payment.', 'error');
                return;
            }

            closeRejectModal();
            showToast(json.message || 'Payment rejected.', 'success');
            setTimeout(() => location.reload(), 800);
        } catch {
            showToast('Network error — please try again.', 'error');
        } finally {
            btn.disabled = false;
            btn.querySelector('.btn-text').style.display = 'inline';
            btn.querySelector('.btn-loader').style.display = 'none';
        }
    }
</script>
@endpush