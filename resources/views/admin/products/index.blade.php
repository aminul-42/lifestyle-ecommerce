@extends('layouts.app')

@section('title', 'Products')
@section('page-title', 'Products')
@section('page-subtitle', 'Manage your product catalog')

@section('content')

    <div class="table-wrap">
        <div class="table-toolbar">
            <span class="table-toolbar-title">
                All Products
                <span class="count-badge">{{ $products->total() }}</span>
            </span>
            <div class="table-toolbar-actions">
                <form method="GET" action="{{ route('admin.products.index') }}" style="display:flex; gap:0.5rem; flex-wrap:wrap;">
                    <div class="search-box">
                        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search products...">
                    </div>
                    <select name="category_id" onchange="this.form.submit()" style="padding:0.45rem 0.75rem; border:1.5px solid var(--border); border-radius:9px; font-size:0.875rem; background:#fafafa;">
                        <option value="">All Categories</option>
                        @foreach ($categories as $c)
                            <option value="{{ $c->id }}" {{ request('category_id') == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                        @endforeach
                    </select>
                    <select name="gender" onchange="this.form.submit()" style="padding:0.45rem 0.75rem; border:1.5px solid var(--border); border-radius:9px; font-size:0.875rem; background:#fafafa;">
                        <option value="">All Genders</option>
                        @foreach (['men','women','kids','unisex'] as $g)
                            <option value="{{ $g }}" {{ request('gender') == $g ? 'selected' : '' }}>{{ ucfirst($g) }}</option>
                        @endforeach
                    </select>
                    <button type="submit" class="btn btn-secondary btn-sm">Filter</button>
                </form>
                <a href="{{ route('admin.products.create') }}" class="btn btn-primary">
                    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                    Add Product
                </a>
            </div>
        </div>

        <table>
            <thead>
                <tr>
                    <th></th>
                    <th>Name</th>
                    <th>Category</th>
                    <th>Gender</th>
                    <th>Price</th>
                    <th>Stock</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="tableBody">
                @forelse ($products as $product)
                    <tr>
                        <td>
                            @if ($product->images->first())
                                <img src="{{ Storage::disk('public')->url($product->images->first()->path) }}"
                                     alt="{{ $product->name }}"
                                     style="width:42px; height:42px; border-radius:8px; object-fit:cover; display:block;">
                            @else
                                <div style="width:42px; height:42px; border-radius:8px; background:#f3f4f6;"></div>
                            @endif
                        </td>
                        <td><strong>{{ $product->name }}</strong></td>
                        <td>{{ $product->category->name ?? '—' }}</td>
                        <td><span class="badge badge-blue">{{ ucfirst($product->gender) }}</span></td>
                        <td>
                            @if ($product->has_variants)
                                <span class="badge badge-purple">Variants</span>
                            @else
                                {{ money($product->discount_price ?? $product->price) }}
                                @if ($product->discount_price)
                                    <span style="text-decoration:line-through; color:#9ca3af; font-size:0.75rem; margin-left:4px;">{{ money($product->price) }}</span>
                                @endif
                            @endif
                        </td>
                        <td>
                            @if ($product->has_variants)
                                {{ $product->variants->sum('stock') }}
                            @else
                                {{ $product->stock }}
                            @endif
                        </td>
                        <td>
                            @if ($product->is_active)
                                <span class="badge badge-green">Active</span>
                            @else
                                <span class="badge badge-red">Inactive</span>
                            @endif
                        </td>
                        <td>
                            <div class="action-btns">
                                <a href="{{ route('admin.products.edit', $product) }}" class="btn btn-secondary btn-sm">Edit</a>
                                <button class="btn btn-danger btn-sm" onclick="deleteProduct({{ $product->id }}, '{{ addslashes($product->name) }}')">Delete</button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="empty-state">
                            <svg fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                            <p>No products found. Add your first product.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        @if ($products->hasPages())
            <div class="pagination-wrap">
                @if ($products->onFirstPage())
                    <span class="page-btn disabled">‹</span>
                @else
                    <a href="{{ $products->previousPageUrl() }}" class="page-btn">‹</a>
                @endif

                @foreach ($products->getUrlRange(1, $products->lastPage()) as $page => $url)
                    @if ($page == $products->currentPage())
                        <span class="page-btn active">{{ $page }}</span>
                    @else
                        <a href="{{ $url }}" class="page-btn">{{ $page }}</a>
                    @endif
                @endforeach

                @if ($products->hasMorePages())
                    <a href="{{ $products->nextPageUrl() }}" class="page-btn">›</a>
                @else
                    <span class="page-btn disabled">›</span>
                @endif

                <span class="page-info">{{ $products->firstItem() }}–{{ $products->lastItem() }} of {{ $products->total() }}</span>
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
                    This will also remove its images and variants. This cannot be undone.
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

    function closeDeleteModal() {
        document.getElementById('deleteModal').classList.remove('open');
        document.body.style.overflow = '';
    }
    function closeOnOverlay(e, id) {
        if (e.target === document.getElementById(id)) closeDeleteModal();
    }

    async function parseJson(res) {
        try { return (await res.json()) ?? {}; } catch { return {}; }
    }

    function deleteProduct(id, name) {
        deleteTargetId = id;
        document.getElementById('deleteName').textContent = name;
        document.getElementById('deleteModal').classList.add('open');
        document.body.style.overflow = 'hidden';
    }

    async function confirmDelete() {
        const btn = document.getElementById('confirmDeleteBtn');
        btn.disabled = true;
        btn.querySelector('.btn-text').style.display = 'none';
        btn.querySelector('.btn-loader').style.display = 'inline-flex';

        try {
            const res = await fetch(`{{ route('admin.products.index') }}/${deleteTargetId}`, {
                method: 'DELETE',
                headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
            });
            const json = await parseJson(res);
            closeDeleteModal();

            if (!res.ok) {
                showToast(json.message || 'Could not delete product.', 'error');
                return;
            }

            showToast(json.message || 'Product deleted.', 'success');
            setTimeout(() => location.reload(), 800);
        } catch {
            closeDeleteModal();
            showToast('Network error — please try again.', 'error');
        }
    }
</script>
@endpush