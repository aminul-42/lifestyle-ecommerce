@extends('layouts.app')

@section('title', 'Coupons')
@section('page-title', 'Coupons')
@section('page-subtitle', 'Create and manage discount codes')

@section('content')

    {{-- ── Stat Cards ─────────────────────────────────────── --}}
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon" style="background:#eef2ff;">
                <svg stroke="#3b5bdb" fill="none" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M20.59 13.41L11 3.83A2 2 0 009.59 3.24L4 3a1 1 0 00-1 1l.24 5.59a2 2 0 00.59 1.41l9.58 9.58a2 2 0 002.83 0l4.35-4.35a2 2 0 000-2.82z"/></svg>
            </div>
            <div class="stat-info">
                <p>Total Coupons</p>
                <strong>{{ $stats['total'] }}</strong>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="background:#d1fae5;">
                <svg stroke="#065f46" fill="none" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
            </div>
            <div class="stat-info">
                <p>Active</p>
                <strong>{{ $stats['active'] }}</strong>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="background:#fee2e2;">
                <svg stroke="#991b1b" fill="none" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div class="stat-info">
                <p>Expired</p>
                <strong>{{ $stats['expired'] }}</strong>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="background:#fef3c7;">
                <svg stroke="#92400e" fill="none" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 19V6l12-3v13M9 19a3 3 0 11-6 0 3 3 0 016 0zm12-3a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            </div>
            <div class="stat-info">
                <p>Times Redeemed</p>
                <strong>{{ $stats['redeemed'] }}</strong>
            </div>
        </div>
    </div>

    {{-- ── Table ──────────────────────────────────────────── --}}
    <div class="table-wrap">
        <div class="table-toolbar">
            <span class="table-toolbar-title">
                All Coupons
                <span class="count-badge">{{ $coupons->total() }}</span>
            </span>
            <div class="table-toolbar-actions">
                <form method="GET" action="{{ route('admin.coupons.index') }}" style="display:flex; gap:0.5rem; flex-wrap:wrap;">
                    <div class="search-box">
                        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by code...">
                    </div>
                    <select name="status" onchange="this.form.submit()" style="padding:0.45rem 0.75rem; border:1.5px solid var(--border); border-radius:9px; font-size:0.875rem; background:#fafafa;">
                        <option value="">All Status</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                        <option value="expired" {{ request('status') == 'expired' ? 'selected' : '' }}>Expired</option>
                    </select>
                    <button type="submit" class="btn btn-secondary btn-sm">Filter</button>
                </form>
                <button class="btn btn-primary" onclick="openAddModal()">
                    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                    Add Coupon
                </button>
            </div>
        </div>

        <table>
            <thead>
                <tr>
                    <th>Code</th>
                    <th>Type</th>
                    <th>Value</th>
                    <th>Min. Order</th>
                    <th>Usage</th>
                    <th>Expires</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="tableBody">
                @forelse ($coupons as $coupon)
                    @php
                        $isExpired = $coupon->expires_at && $coupon->expires_at->isPast();
                    @endphp
                    <tr>
                        <td><strong>{{ $coupon->code }}</strong></td>
                        <td>
                            @if ($coupon->type === 'percent')
                                <span class="badge badge-blue">Percent</span>
                            @else
                                <span class="badge badge-purple">Fixed</span>
                            @endif
                        </td>
                        <td>
                            @if ($coupon->type === 'percent')
                                {{ rtrim(rtrim(number_format($coupon->value, 2), '0'), '.') }}%
                            @else
                                {{ money($coupon->value) }}
                            @endif
                        </td>
                        <td>{{ $coupon->min_order_amount > 0 ? money($coupon->min_order_amount) : '—' }}</td>
                        <td>
                            {{ $coupon->used_count }}{{ $coupon->usage_limit ? ' / ' . $coupon->usage_limit : '' }}
                        </td>
                        <td>
                            @if ($coupon->expires_at)
                                {{ $coupon->expires_at->format('d M, Y') }}
                            @else
                                <span style="color:#9ca3af;">No expiry</span>
                            @endif
                        </td>
                        <td>
                            @if ($isExpired)
                                <span class="badge badge-red">Expired</span>
                            @elseif ($coupon->is_active)
                                <span class="badge badge-green" style="cursor:pointer;" onclick="toggleStatus({{ $coupon->id }})" title="Click to deactivate">Active</span>
                            @else
                                <span class="badge badge-gray" style="cursor:pointer;" onclick="toggleStatus({{ $coupon->id }})" title="Click to activate">Inactive</span>
                            @endif
                        </td>
                        <td>
                            <div class="action-btns">
                                <button class="btn btn-secondary btn-sm" onclick="openEditModal({{ $coupon->id }})">Edit</button>
                                <button class="btn btn-danger btn-sm" onclick="deleteCoupon({{ $coupon->id }}, '{{ addslashes($coupon->code) }}')">Delete</button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="empty-state">
                            <svg fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M20.59 13.41L11 3.83A2 2 0 009.59 3.24L4 3a1 1 0 00-1 1l.24 5.59a2 2 0 00.59 1.41l9.58 9.58a2 2 0 002.83 0l4.35-4.35a2 2 0 000-2.82z"/></svg>
                            <p>No coupons found. Create your first discount code.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        @if ($coupons->hasPages())
            <div class="pagination-wrap">
                @if ($coupons->onFirstPage())
                    <span class="page-btn disabled">‹</span>
                @else
                    <a href="{{ $coupons->previousPageUrl() }}" class="page-btn">‹</a>
                @endif

                @foreach ($coupons->getUrlRange(1, $coupons->lastPage()) as $page => $url)
                    @if ($page == $coupons->currentPage())
                        <span class="page-btn active">{{ $page }}</span>
                    @else
                        <a href="{{ $url }}" class="page-btn">{{ $page }}</a>
                    @endif
                @endforeach

                @if ($coupons->hasMorePages())
                    <a href="{{ $coupons->nextPageUrl() }}" class="page-btn">›</a>
                @else
                    <span class="page-btn disabled">›</span>
                @endif

                <span class="page-info">{{ $coupons->firstItem() }}–{{ $coupons->lastItem() }} of {{ $coupons->total() }}</span>
            </div>
        @endif
    </div>

    {{-- ── Add / Edit Modal ───────────────────────────────── --}}
    <div class="modal-overlay" id="couponModal" onclick="closeOnOverlay(event,'couponModal')">
        <div class="modal">
            <div class="modal-header">
                <h3 id="modalTitle">Add Coupon</h3>
                <button class="modal-close" onclick="closeCouponModal()">
                    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <form id="couponForm">
                <div class="modal-body">
                    <input type="hidden" id="couponId" value="">

                    <div class="form-group">
                        <label>Coupon Code <span class="req">*</span></label>
                        <input type="text" id="code" name="code" placeholder="e.g. SUMMER25" style="text-transform:uppercase;" maxlength="50">
                        <span class="field-error" id="err_code"></span>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label>Discount Type <span class="req">*</span></label>
                            <select id="type" name="type">
                                <option value="percent">Percentage (%)</option>
                                <option value="fixed">Fixed Amount</option>
                            </select>
                            <span class="field-error" id="err_type"></span>
                        </div>
                        <div class="form-group">
                            <label>Value <span class="req">*</span></label>
                            <input type="number" id="value" name="value" step="0.01" min="0.01" placeholder="e.g. 25">
                            <span class="field-error" id="err_value"></span>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label>Minimum Order Amount</label>
                            <input type="number" id="min_order_amount" name="min_order_amount" step="0.01" min="0" placeholder="0">
                            <span class="field-error" id="err_min_order_amount"></span>
                        </div>
                        <div class="form-group">
                            <label>Usage Limit</label>
                            <input type="number" id="usage_limit" name="usage_limit" min="1" placeholder="Unlimited">
                            <span class="field-error" id="err_usage_limit"></span>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Expiry Date</label>
                        <input type="date" id="expires_at" name="expires_at">
                        <span class="field-error" id="err_expires_at"></span>
                    </div>

                    <div class="form-group">
                        <label class="toggle-label">
                            <input type="checkbox" id="is_active" class="toggle-input" checked>
                            <span class="toggle-switch"></span>
                            Active
                        </label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="closeCouponModal()">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="saveBtn">
                        <span class="btn-text">Save Coupon</span>
                        <span class="btn-loader" style="display:none;">
                            <svg class="spin" viewBox="0 0 24 24" width="16" height="16" fill="none"><circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3" stroke-dasharray="30 70" opacity=".3"/><path d="M12 2a10 10 0 0110 10" stroke="currentColor" stroke-width="3" stroke-linecap="round"/></svg>
                            Saving...
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- ── Delete Confirm Modal ───────────────────────────── --}}
    <div class="modal-overlay" id="deleteModal" onclick="closeOnOverlay(event,'deleteModal')">
        <div class="modal" style="max-width:400px;">
            <div class="modal-header">
                <h3 style="color:#dc2626;">Confirm Delete</h3>
                <button class="modal-close" onclick="closeDeleteModal()">
                    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <div class="modal-body">
                <p style="text-align:center; color:#6b7280; font-size:0.9rem; line-height:1.6;">
                    Are you sure you want to delete <strong id="deleteName"></strong>?
                    This cannot be undone.
                </p>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" onclick="closeDeleteModal()">Cancel</button>
                <button class="btn btn-danger" id="confirmDeleteBtn" onclick="confirmDelete()">
                    <span class="btn-text">Yes, Delete</span>
                    <span class="btn-loader" style="display:none;">
                        <svg class="spin" viewBox="0 0 24 24" width="16" height="16" fill="none"><circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3" stroke-dasharray="30 70" opacity=".3"/><path d="M12 2a10 10 0 0110 10" stroke="currentColor" stroke-width="3" stroke-linecap="round"/></svg>
                        Deleting...
                    </span>
                </button>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
<script>
    const ROUTES = {
        store:  "{{ route('admin.coupons.store') }}",
        editBase: "{{ url('admin/coupons') }}",   // + /{id}/edit
        updateBase: "{{ url('admin/coupons') }}", // + /{id}
        toggleBase: "{{ url('admin/coupons') }}", // + /{id}/toggle
    };

    let deleteTargetId = null;

    async function parseJson(res) {
        try { return (await res.json()) ?? {}; } catch { return {}; }
    }

    function clearErrors() {
        document.querySelectorAll('.field-error').forEach(el => el.textContent = '');
    }

    function showErrors(errors) {
        clearErrors();
        Object.keys(errors).forEach(field => {
            const el = document.getElementById('err_' + field);
            if (el) el.textContent = errors[field][0];
        });
    }

    function resetForm() {
        document.getElementById('couponForm').reset();
        document.getElementById('couponId').value = '';
        document.getElementById('is_active').checked = true;
        clearErrors();
    }

    function openAddModal() {
        resetForm();
        document.getElementById('modalTitle').textContent = 'Add Coupon';
        document.getElementById('couponModal').classList.add('open');
        document.body.style.overflow = 'hidden';
    }

    async function openEditModal(id) {
        resetForm();
        document.getElementById('modalTitle').textContent = 'Edit Coupon';

        try {
            const res = await fetch(`${ROUTES.editBase}/${id}/edit`, {
                headers: { 'Accept': 'application/json' },
            });
            const coupon = await parseJson(res);

            document.getElementById('couponId').value = coupon.id;
            document.getElementById('code').value = coupon.code;
            document.getElementById('type').value = coupon.type;
            document.getElementById('value').value = coupon.value;
            document.getElementById('min_order_amount').value = coupon.min_order_amount;
            document.getElementById('usage_limit').value = coupon.usage_limit ?? '';
            document.getElementById('expires_at').value = coupon.expires_at ? coupon.expires_at.split('T')[0] : '';
            document.getElementById('is_active').checked = !!coupon.is_active;

            document.getElementById('couponModal').classList.add('open');
            document.body.style.overflow = 'hidden';
        } catch {
            showToast('Could not load coupon details.', 'error');
        }
    }

    function closeCouponModal() {
        document.getElementById('couponModal').classList.remove('open');
        document.body.style.overflow = '';
    }

    function closeOnOverlay(e, id) {
        if (e.target === document.getElementById(id)) {
            id === 'deleteModal' ? closeDeleteModal() : closeCouponModal();
        }
    }

    document.getElementById('couponForm').addEventListener('submit', async function (e) {
        e.preventDefault();
        clearErrors();

        const id = document.getElementById('couponId').value;
        const isEdit = !!id;

        const formData = new FormData();
        formData.append('code', document.getElementById('code').value.trim());
        formData.append('type', document.getElementById('type').value);
        formData.append('value', document.getElementById('value').value);
        formData.append('min_order_amount', document.getElementById('min_order_amount').value || 0);
        formData.append('usage_limit', document.getElementById('usage_limit').value || '');
        formData.append('expires_at', document.getElementById('expires_at').value || '');
        formData.append('is_active', document.getElementById('is_active').checked ? 1 : 0);

        if (isEdit) formData.append('_method', 'PUT');

        const url = isEdit ? `${ROUTES.updateBase}/${id}` : ROUTES.store;

        const btn = document.getElementById('saveBtn');
        btn.disabled = true;
        btn.querySelector('.btn-text').style.display = 'none';
        btn.querySelector('.btn-loader').style.display = 'inline-flex';

        try {
            const res = await fetch(url, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
                body: formData,
            });
            const json = await parseJson(res);

            if (res.status === 422) {
                showErrors(json.errors || {});
                return;
            }

            if (!res.ok) {
                showToast(json.message || 'Something went wrong.', 'error');
                return;
            }

            closeCouponModal();
            showToast(json.message || 'Coupon saved.', 'success');
            setTimeout(() => location.reload(), 800);
        } catch {
            showToast('Network error — please try again.', 'error');
        } finally {
            btn.disabled = false;
            btn.querySelector('.btn-text').style.display = 'inline';
            btn.querySelector('.btn-loader').style.display = 'none';
        }
    });

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

    function deleteCoupon(id, code) {
        deleteTargetId = id;
        document.getElementById('deleteName').textContent = code;
        document.getElementById('deleteModal').classList.add('open');
        document.body.style.overflow = 'hidden';
    }

    function closeDeleteModal() {
        document.getElementById('deleteModal').classList.remove('open');
        document.body.style.overflow = '';
    }

    async function confirmDelete() {
        const btn = document.getElementById('confirmDeleteBtn');
        btn.disabled = true;
        btn.querySelector('.btn-text').style.display = 'none';
        btn.querySelector('.btn-loader').style.display = 'inline-flex';

        try {
            const res = await fetch(`${ROUTES.updateBase}/${deleteTargetId}`, {
                method: 'DELETE',
                headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
            });
            const json = await parseJson(res);
            closeDeleteModal();

            if (!res.ok) {
                showToast(json.message || 'Could not delete coupon.', 'error');
                return;
            }

            showToast(json.message || 'Coupon deleted.', 'success');
            setTimeout(() => location.reload(), 800);
        } catch {
            closeDeleteModal();
            showToast('Network error — please try again.', 'error');
        }
    }
</script>
@endpush