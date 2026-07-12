@extends('layouts.app')

@section('title', 'Categories')
@section('page-title', 'Categories')
@section('page-subtitle', 'Manage your product categories')

@section('content')

    <div class="table-wrap">
        <div class="table-toolbar">
            <span class="table-toolbar-title">
                All Categories
                <span class="count-badge">{{ $categories->total() }}</span>
            </span>
            <div class="table-toolbar-actions">
                <div class="search-box">
                    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <input type="text" id="searchInput" placeholder="Search categories...">
                </div>
                <button class="btn btn-primary" onclick="openAddModal()">
                    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                    Add Category
                </button>
            </div>
        </div>

        <table id="catTable">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Name</th>
                    <th>Parent</th>
                    <th>Products</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="tableBody">
                @forelse ($categories as $i => $cat)
                    <tr data-name="{{ strtolower($cat->name) }}">
                        <td style="color:#9ca3af;">{{ $categories->firstItem() + $i }}</td>
                        <td><strong>{{ $cat->name }}</strong></td>
                        <td>{{ $cat->parent->name ?? '—' }}</td>
                        <td>{{ $cat->products_count }}</td>
                        <td>
                            @if ($cat->is_active)
                                <span class="badge badge-green">Active</span>
                            @else
                                <span class="badge badge-red">Inactive</span>
                            @endif
                        </td>
                        <td>
                            <div class="action-btns">
                                <button class="btn btn-secondary btn-sm" onclick="openEditModal({{ $cat->id }})">Edit</button>
                                <button class="btn btn-danger btn-sm" onclick="deleteCategory({{ $cat->id }}, '{{ addslashes($cat->name) }}')">Delete</button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="empty-state">
                            <svg fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h7"/></svg>
                            <p>No categories found. Add your first category.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        @if ($categories->hasPages())
            <div class="pagination-wrap">
                @if ($categories->onFirstPage())
                    <span class="page-btn disabled">‹</span>
                @else
                    <a href="{{ $categories->previousPageUrl() }}" class="page-btn">‹</a>
                @endif

                @foreach ($categories->getUrlRange(1, $categories->lastPage()) as $page => $url)
                    @if ($page == $categories->currentPage())
                        <span class="page-btn active">{{ $page }}</span>
                    @else
                        <a href="{{ $url }}" class="page-btn">{{ $page }}</a>
                    @endif
                @endforeach

                @if ($categories->hasMorePages())
                    <a href="{{ $categories->nextPageUrl() }}" class="page-btn">›</a>
                @else
                    <span class="page-btn disabled">›</span>
                @endif

                <span class="page-info">{{ $categories->firstItem() }}–{{ $categories->lastItem() }} of {{ $categories->total() }}</span>
            </div>
        @endif
    </div>

    {{-- ── Add Modal ──────────────────────────────────────── --}}
    <div class="modal-overlay" id="addModal" onclick="closeOnOverlay(event,'addModal')">
        <div class="modal" style="max-width:480px;">
            <div class="modal-header">
                <h3>Add Category</h3>
                <button class="modal-close" onclick="closeAddModal()">
                    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <form id="addForm" onsubmit="submitAdd(event)">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label>Category Name <span class="req">*</span></label>
                        <input type="text" name="name" placeholder="e.g. Men's Shoes" required>
                        <span class="field-error" id="add-name-error"></span>
                    </div>
                    <div class="form-group">
                        <label>Parent Category</label>
                        <select name="parent_id" id="addParentSelect">
                            <option value="">— None (top-level) —</option>
                            @foreach ($allCategories as $c)
                                <option value="{{ $c->id }}">{{ $c->name }}</option>
                            @endforeach
                        </select>
                        <span class="field-error" id="add-parent_id-error"></span>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="closeAddModal()">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="addSubmitBtn">
                        <span class="btn-text">Save Category</span>
                        <span class="btn-loader" style="display:none;">
                            <svg class="spin" viewBox="0 0 24 24" width="16" height="16" fill="none"><circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3" stroke-dasharray="30 70" opacity=".3"/><path d="M12 2a10 10 0 0110 10" stroke="currentColor" stroke-width="3" stroke-linecap="round"/></svg>
                            Saving...
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- ── Edit Modal ─────────────────────────────────────── --}}
    <div class="modal-overlay" id="editModal" onclick="closeOnOverlay(event,'editModal')">
        <div class="modal" style="max-width:480px;">
            <div class="modal-header">
                <h3>Edit Category</h3>
                <button class="modal-close" onclick="closeEditModal()">
                    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <form id="editForm" onsubmit="submitEdit(event)">
                @csrf
                <input type="hidden" id="editId">
                <div class="modal-body">
                    <div class="form-group">
                        <label>Category Name <span class="req">*</span></label>
                        <input type="text" id="editName" name="name" required>
                        <span class="field-error" id="edit-name-error"></span>
                    </div>
                    <div class="form-group">
                        <label>Parent Category</label>
                        <select id="editParentSelect" name="parent_id">
                            <option value="">— None (top-level) —</option>
                            @foreach ($allCategories as $c)
                                <option value="{{ $c->id }}">{{ $c->name }}</option>
                            @endforeach
                        </select>
                        <span class="field-error" id="edit-parent_id-error"></span>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="closeEditModal()">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="editSubmitBtn">
                        <span class="btn-text">Update Category</span>
                        <span class="btn-loader" style="display:none;">
                            <svg class="spin" viewBox="0 0 24 24" width="16" height="16" fill="none"><circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3" stroke-dasharray="30 70" opacity=".3"/><path d="M12 2a10 10 0 0110 10" stroke="currentColor" stroke-width="3" stroke-linecap="round"/></svg>
                            Updating...
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
                    This action cannot be undone.
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
    let deleteTargetId = null;

    // ── Modal open/close ─────────────────────────────────────
    function openAddModal() {
        document.getElementById('addForm').reset();
        clearErrors('add');
        document.getElementById('addModal').classList.add('open');
        document.body.style.overflow = 'hidden';
    }
    function closeAddModal() {
        document.getElementById('addModal').classList.remove('open');
        document.body.style.overflow = '';
    }
    function closeEditModal() {
        document.getElementById('editModal').classList.remove('open');
        document.body.style.overflow = '';
    }
    function closeDeleteModal() {
        document.getElementById('deleteModal').classList.remove('open');
        document.body.style.overflow = '';
    }
    function closeOnOverlay(e, id) {
        if (e.target === document.getElementById(id)) {
            document.getElementById(id).classList.remove('open');
            document.body.style.overflow = '';
        }
    }

    function clearErrors(prefix) {
        document.querySelectorAll(`[id^="${prefix}-"][id$="-error"]`).forEach(el => el.textContent = '');
    }
    function showErrors(errors, prefix) {
        Object.entries(errors).forEach(([field, messages]) => {
            const el = document.getElementById(`${prefix}-${field}-error`);
            if (el) el.textContent = messages[0];
        });
    }

    function setBtnLoading(btnId, loading) {
        const btn = document.getElementById(btnId);
        btn.disabled = loading;
        btn.querySelector('.btn-text').style.display = loading ? 'none' : 'inline';
        btn.querySelector('.btn-loader').style.display = loading ? 'inline-flex' : 'none';
    }

    // Safely parse a fetch Response as JSON — never leaves us with a bare null
    // that crashes on `.message` (this was the root cause of the earlier bug).
    async function parseJson(res) {
        try {
            const data = await res.json();
            return data ?? {};
        } catch {
            return {};
        }
    }

    // ── Add ──────────────────────────────────────────────────
    async function submitAdd(e) {
        e.preventDefault();
        clearErrors('add');
        setBtnLoading('addSubmitBtn', true);

        const data = new FormData(document.getElementById('addForm'));

        try {
            const res = await fetch('{{ route('admin.categories.store') }}', {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
                body: data,
            });
            const json = await parseJson(res);

            if (!res.ok) {
                showErrors(json.errors || {}, 'add');
                showToast(json.message || 'Please check the form for errors.', 'error');
                setBtnLoading('addSubmitBtn', false);
                return;
            }

            closeAddModal();
            showToast(json.message || 'Category created.', 'success');
            setTimeout(() => location.reload(), 800);
        } catch {
            showToast('Network error — please try again.', 'error');
            setBtnLoading('addSubmitBtn', false);
        }
    }

    // ── Load for edit ────────────────────────────────────────
    async function openEditModal(id) {
        document.getElementById('editModal').classList.add('open');
        document.body.style.overflow = 'hidden';
        clearErrors('edit');

        try {
            const res = await fetch(`{{ route('admin.categories.index') }}/${id}/edit`, {
                headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF }
            });
            const cat = await parseJson(res);

            if (!res.ok) throw new Error();

            document.getElementById('editId').value = cat.id;
            document.getElementById('editName').value = cat.name;
            document.getElementById('editParentSelect').value = cat.parent_id ?? '';
        } catch {
            showToast('Could not load category data.', 'error');
            closeEditModal();
        }
    }

    // ── Update ───────────────────────────────────────────────
    async function submitEdit(e) {
        e.preventDefault();
        clearErrors('edit');
        setBtnLoading('editSubmitBtn', true);

        const id = document.getElementById('editId').value;
        const data = new FormData(document.getElementById('editForm'));
        data.append('_method', 'PUT');

        try {
            const res = await fetch(`{{ route('admin.categories.index') }}/${id}`, {
                method: 'POST', // method-spoofed via _method=PUT
                headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
                body: data,
            });
            const json = await parseJson(res);

            if (!res.ok) {
                showErrors(json.errors || {}, 'edit');
                showToast(json.message || 'Please check the form for errors.', 'error');
                setBtnLoading('editSubmitBtn', false);
                return;
            }

            closeEditModal();
            showToast(json.message || 'Category updated.', 'success');
            setTimeout(() => location.reload(), 800);
        } catch {
            showToast('Network error — please try again.', 'error');
            setBtnLoading('editSubmitBtn', false);
        }
    }

    // ── Delete ───────────────────────────────────────────────
    function deleteCategory(id, name) {
        deleteTargetId = id;
        document.getElementById('deleteName').textContent = name;
        document.getElementById('deleteModal').classList.add('open');
        document.body.style.overflow = 'hidden';
    }

    async function confirmDelete() {
        setBtnLoading('confirmDeleteBtn', true);

        try {
            const res = await fetch(`{{ route('admin.categories.index') }}/${deleteTargetId}`, {
                method: 'DELETE',
                headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
            });
            const json = await parseJson(res);

            closeDeleteModal();

            if (!res.ok) {
                showToast(json.message || 'Could not delete category.', 'error');
                setBtnLoading('confirmDeleteBtn', false);
                return;
            }

            showToast(json.message || 'Category deleted.', 'success');
            setTimeout(() => location.reload(), 800);
        } catch {
            closeDeleteModal();
            showToast('Network error — please try again.', 'error');
            setBtnLoading('confirmDeleteBtn', false);
        }
    }

    // ── Live search ──────────────────────────────────────────
    document.getElementById('searchInput').addEventListener('input', function () {
        const q = this.value.toLowerCase();
        document.querySelectorAll('#tableBody tr[data-name]').forEach(row => {
            row.style.display = row.dataset.name.includes(q) ? '' : 'none';
        });
    });
</script>
@endpush