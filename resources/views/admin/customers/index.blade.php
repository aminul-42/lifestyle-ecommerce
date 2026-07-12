@extends('layouts.app')

@section('title', 'Customers')
@section('page-title', 'Customers')
@section('page-subtitle', 'View and manage registered customers')

@section('content')

    {{-- ── Stat Cards ─────────────────────────────────────── --}}
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon" style="background:#eef2ff;">
                <svg stroke="#3b5bdb" fill="none" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2M9 11a4 4 0 100-8 4 4 0 000 8zM23 21v-2a4 4 0 00-3-3.87M16 3.13a4 4 0 010 7.75"/></svg>
            </div>
            <div class="stat-info">
                <p>Total Customers</p>
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
                <svg stroke="#991b1b" fill="none" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M18.36 6.64a9 9 0 11-12.73 0M12 2v10"/></svg>
            </div>
            <div class="stat-info">
                <p>Inactive</p>
                <strong>{{ $stats['inactive'] }}</strong>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="background:#fef3c7;">
                <svg stroke="#92400e" fill="none" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
            </div>
            <div class="stat-info">
                <p>New This Month</p>
                <strong>{{ $stats['new_this_month'] }}</strong>
            </div>
        </div>
    </div>

    {{-- ── Table ──────────────────────────────────────────── --}}
    <div class="table-wrap">
        <div class="table-toolbar">
            <span class="table-toolbar-title">
                All Customers
                <span class="count-badge">{{ $customers->total() }}</span>
            </span>
            <div class="table-toolbar-actions">
                <form method="GET" action="{{ route('admin.customers.index') }}" style="display:flex; gap:0.5rem; flex-wrap:wrap;">
                    <div class="search-box">
                        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search name or email...">
                    </div>
                    <select name="status" onchange="this.form.submit()" style="padding:0.45rem 0.75rem; border:1.5px solid var(--border); border-radius:9px; font-size:0.875rem; background:#fafafa;">
                        <option value="">All Status</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                    <button type="submit" class="btn btn-secondary btn-sm">Filter</button>
                </form>
            </div>
        </div>

        <table>
            <thead>
                <tr>
                    <th></th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Orders</th>
                    <th>Total Spent</th>
                    <th>Joined</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="tableBody">
                @forelse ($customers as $customer)
                    <tr>
                        <td>
                            @if ($customer->avatar)
                                <img src="{{ $customer->avatar }}" alt="{{ $customer->name }}" style="width:36px; height:36px; border-radius:50%; object-fit:cover; display:block;">
                            @else
                                <div style="width:36px; height:36px; border-radius:50%; background:linear-gradient(135deg, var(--blue), #4c6ef5); color:#fff; display:flex; align-items:center; justify-content:center; font-size:0.8rem; font-weight:700;">
                                    {{ strtoupper(substr($customer->name, 0, 1)) }}
                                </div>
                            @endif
                        </td>
                        <td><strong>{{ $customer->name }}</strong></td>
                        <td>{{ $customer->email }}</td>
                        <td>{{ $customer->phone ?? '—' }}</td>
                        <td>{{ $customer->orders_count }}</td>
                        <td>{{ money($customer->total_spent ?? 0) }}</td>
                        <td>{{ $customer->created_at->format('d M, Y') }}</td>
                        <td>
                            @if ($customer->is_active)
                                <span class="badge badge-green" style="cursor:pointer;" onclick="toggleStatus({{ $customer->id }})" title="Click to deactivate">Active</span>
                            @else
                                <span class="badge badge-gray" style="cursor:pointer;" onclick="toggleStatus({{ $customer->id }})" title="Click to activate">Inactive</span>
                            @endif
                        </td>
                        <td>
                            <td>
                     <div class="action-btns">
                            <a href="{{ route('admin.customers.show', $customer) }}" class="btn btn-secondary btn-sm">View</a>
                              <button class="btn btn-danger btn-sm" onclick="deleteCustomer({{ $customer->id }}, '{{ addslashes($customer->name) }}')">Delete</button>
                     </div>
                       </td>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="empty-state">
                            <svg fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2M9 11a4 4 0 100-8 4 4 0 000 8zM23 21v-2a4 4 0 00-3-3.87M16 3.13a4 4 0 010 7.75"/></svg>
                            <p>No customers found.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        @if ($customers->hasPages())
            <div class="pagination-wrap">
                @if ($customers->onFirstPage())
                    <span class="page-btn disabled">‹</span>
                @else
                    <a href="{{ $customers->previousPageUrl() }}" class="page-btn">‹</a>
                @endif

                @foreach ($customers->getUrlRange(1, $customers->lastPage()) as $page => $url)
                    @if ($page == $customers->currentPage())
                        <span class="page-btn active">{{ $page }}</span>
                    @else
                        <a href="{{ $url }}" class="page-btn">{{ $page }}</a>
                    @endif
                @endforeach

                @if ($customers->hasMorePages())
                    <a href="{{ $customers->nextPageUrl() }}" class="page-btn">›</a>
                @else
                    <span class="page-btn disabled">›</span>
                @endif

                <span class="page-info">{{ $customers->firstItem() }}–{{ $customers->lastItem() }} of {{ $customers->total() }}</span>
            </div>
        @endif
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
        toggleBase: "{{ url('admin/customers') }}",  // + /{id}/toggle
        destroyBase: "{{ url('admin/customers') }}", // + /{id}
    };

    let deleteTargetId = null;

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

    function deleteCustomer(id, name) {
        deleteTargetId = id;
        document.getElementById('deleteName').textContent = name;
        document.getElementById('deleteModal').classList.add('open');
        document.body.style.overflow = 'hidden';
    }

    function closeDeleteModal() {
        document.getElementById('deleteModal').classList.remove('open');
        document.body.style.overflow = '';
    }

    function closeOnOverlay(e, id) {
        if (e.target === document.getElementById(id)) closeDeleteModal();
    }

    async function confirmDelete() {
        const btn = document.getElementById('confirmDeleteBtn');
        btn.disabled = true;
        btn.querySelector('.btn-text').style.display = 'none';
        btn.querySelector('.btn-loader').style.display = 'inline-flex';

        try {
            const res = await fetch(`${ROUTES.destroyBase}/${deleteTargetId}`, {
                method: 'DELETE',
                headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
            });
            const json = await parseJson(res);
            closeDeleteModal();

            if (!res.ok) {
                showToast(json.message || 'Could not delete customer.', 'error');
                return;
            }

            showToast(json.message || 'Customer deleted.', 'success');
            setTimeout(() => location.reload(), 800);
        } catch {
            closeDeleteModal();
            showToast('Network error — please try again.', 'error');
        }
    }
</script>
@endpush