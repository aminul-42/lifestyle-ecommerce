@extends('layouts.app')

@section('title', 'Reviews')
@section('page-title', 'Reviews')
@section('page-subtitle', 'Moderate customer product reviews')

@section('content')

    {{-- ── Stat Cards ─────────────────────────────────────── --}}
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon" style="background:#eef2ff;">
                <svg stroke="#3b5bdb" fill="none" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 17.75l-6.16 3.24 1.18-6.88L2 9.24l6.92-1L12 2l3.08 6.24 6.92 1-5.02 4.87 1.18 6.88z"/></svg>
            </div>
            <div class="stat-info">
                <p>Total Reviews</p>
                <strong>{{ $stats['total'] }}</strong>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="background:#d1fae5;">
                <svg stroke="#065f46" fill="none" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
            </div>
            <div class="stat-info">
                <p>Approved</p>
                <strong>{{ $stats['approved'] }}</strong>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="background:#fef3c7;">
                <svg stroke="#92400e" fill="none" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div class="stat-info">
                <p>Pending</p>
                <strong>{{ $stats['pending'] }}</strong>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="background:#fee2e2;">
                <svg stroke="#991b1b" fill="none" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 17.75l-6.16 3.24 1.18-6.88L2 9.24l6.92-1L12 2l3.08 6.24 6.92 1-5.02 4.87 1.18 6.88z"/></svg>
            </div>
            <div class="stat-info">
                <p>Average Rating</p>
                <strong>{{ $stats['avg_rating'] }} <span style="font-size:1rem; color:#9ca3af;">/ 5</span></strong>
            </div>
        </div>
    </div>

    {{-- ── Table ──────────────────────────────────────────── --}}
    <div class="table-wrap">
        <div class="table-toolbar">
            <span class="table-toolbar-title">
                All Reviews
                <span class="count-badge">{{ $reviews->total() }}</span>
            </span>
            <div class="table-toolbar-actions">
                <form method="GET" action="{{ route('admin.reviews.index') }}" style="display:flex; gap:0.5rem; flex-wrap:wrap;">
                    <div class="search-box">
                        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search customer, product, comment...">
                    </div>
                    <select name="status" onchange="this.form.submit()" style="padding:0.45rem 0.75rem; border:1.5px solid var(--border); border-radius:9px; font-size:0.875rem; background:#fafafa;">
                        <option value="">All Status</option>
                        <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    </select>
                    <select name="rating" onchange="this.form.submit()" style="padding:0.45rem 0.75rem; border:1.5px solid var(--border); border-radius:9px; font-size:0.875rem; background:#fafafa;">
                        <option value="">All Ratings</option>
                        @for ($i = 5; $i >= 1; $i--)
                            <option value="{{ $i }}" {{ request('rating') == $i ? 'selected' : '' }}>{{ $i }} Star{{ $i > 1 ? 's' : '' }}</option>
                        @endfor
                    </select>
                    <button type="submit" class="btn btn-secondary btn-sm">Filter</button>
                </form>
            </div>
        </div>

        <table>
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Customer</th>
                    <th>Rating</th>
                    <th>Comment</th>
                    <th>Purchase</th>
                    <th>Date</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="tableBody">
                @forelse ($reviews as $review)
                    <tr>
                        <td><strong>{{ $review->product->name ?? '—' }}</strong></td>
                        <td>
                            {{ $review->user->name ?? '—' }}
                            <br><span style="font-size:0.75rem; color:#9ca3af;">{{ $review->user->email ?? '' }}</span>
                        </td>
                        <td>
                            <span style="color:#f59e0b; letter-spacing:1px;">
                                {{ str_repeat('★', $review->rating) }}{{ str_repeat('☆', 5 - $review->rating) }}
                            </span>
                        </td>
                        <td style="max-width:220px;">
                            @if ($review->comment)
                                <span style="display:block; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">{{ $review->comment }}</span>
                                @if (strlen($review->comment) > 40)
                                    <button type="button" onclick="viewComment('{{ addslashes($review->product->name ?? '') }}', {{ $review->rating }}, {{ Js::from($review->comment) }})" style="background:none; border:none; color:var(--blue); font-size:0.75rem; font-weight:600; cursor:pointer; padding:0;">Read full</button>
                                @endif
                            @else
                                <span style="color:#9ca3af;">No comment</span>
                            @endif
                        </td>
                        <td>
                            @if ($review->order_id)
                                <span class="badge badge-blue">Verified</span>
                            @else
                                <span class="badge badge-gray">Unverified</span>
                            @endif
                        </td>
                        <td>{{ $review->created_at->format('d M, Y') }}</td>
                        <td>
                            @if ($review->is_approved)
                                <span class="badge badge-green">Approved</span>
                            @else
                                <span class="badge badge-yellow">Pending</span>
                            @endif
                        </td>
                        <td>
                            <div class="action-btns">
                                @if ($review->is_approved)
                                    <button class="btn btn-secondary btn-sm" onclick="setStatus({{ $review->id }}, 'reject')">Unapprove</button>
                                @else
                                    <button class="btn btn-success btn-sm" onclick="setStatus({{ $review->id }}, 'approve')">Approve</button>
                                @endif
                                <button class="btn btn-danger btn-sm" onclick="deleteReview({{ $review->id }})">Delete</button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="empty-state">
                            <svg fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 17.75l-6.16 3.24 1.18-6.88L2 9.24l6.92-1L12 2l3.08 6.24 6.92 1-5.02 4.87 1.18 6.88z"/></svg>
                            <p>No reviews found.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        @if ($reviews->hasPages())
            <div class="pagination-wrap">
                @if ($reviews->onFirstPage())
                    <span class="page-btn disabled">‹</span>
                @else
                    <a href="{{ $reviews->previousPageUrl() }}" class="page-btn">‹</a>
                @endif

                @foreach ($reviews->getUrlRange(1, $reviews->lastPage()) as $page => $url)
                    @if ($page == $reviews->currentPage())
                        <span class="page-btn active">{{ $page }}</span>
                    @else
                        <a href="{{ $url }}" class="page-btn">{{ $page }}</a>
                    @endif
                @endforeach

                @if ($reviews->hasMorePages())
                    <a href="{{ $reviews->nextPageUrl() }}" class="page-btn">›</a>
                @else
                    <span class="page-btn disabled">›</span>
                @endif

                <span class="page-info">{{ $reviews->firstItem() }}–{{ $reviews->lastItem() }} of {{ $reviews->total() }}</span>
            </div>
        @endif
    </div>

    {{-- ── Full Comment Modal ─────────────────────────────── --}}
    <div class="modal-overlay" id="commentModal" onclick="closeOnOverlay(event,'commentModal')">
        <div class="modal" style="max-width:480px;">
            <div class="modal-header">
                <h3 id="commentModalProduct">Review</h3>
                <button class="modal-close" onclick="closeCommentModal()">
                    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <div class="modal-body">
                <div id="commentModalRating" style="color:#f59e0b; letter-spacing:2px; font-size:1.1rem; margin-bottom:0.75rem;"></div>
                <p id="commentModalText" style="color:#374151; font-size:0.9rem; line-height:1.7; white-space:pre-wrap;"></p>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" onclick="closeCommentModal()">Close</button>
            </div>
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
                    Are you sure you want to delete this review? This cannot be undone.
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
        approveBase: "{{ url('admin/reviews') }}", // + /{id}/approve
        rejectBase:  "{{ url('admin/reviews') }}", // + /{id}/reject
        destroyBase: "{{ url('admin/reviews') }}", // + /{id}
    };

    let deleteTargetId = null;

    async function parseJson(res) {
        try { return (await res.json()) ?? {}; } catch { return {}; }
    }

    function viewComment(productName, rating, comment) {
        document.getElementById('commentModalProduct').textContent = productName || 'Review';
        document.getElementById('commentModalRating').textContent = '★'.repeat(rating) + '☆'.repeat(5 - rating);
        document.getElementById('commentModalText').textContent = comment;
        document.getElementById('commentModal').classList.add('open');
        document.body.style.overflow = 'hidden';
    }

    function closeCommentModal() {
        document.getElementById('commentModal').classList.remove('open');
        document.body.style.overflow = '';
    }

    function closeOnOverlay(e, id) {
        if (e.target === document.getElementById(id)) {
            id === 'deleteModal' ? closeDeleteModal() : closeCommentModal();
        }
    }

    async function setStatus(id, action) {
        const base = action === 'approve' ? ROUTES.approveBase : ROUTES.rejectBase;

        try {
            const res = await fetch(`${base}/${id}/${action}`, {
                method: 'PATCH',
                headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
            });
            const json = await parseJson(res);

            if (!res.ok) {
                showToast(json.message || 'Could not update review.', 'error');
                return;
            }

            showToast(json.message || 'Review updated.', 'success');
            setTimeout(() => location.reload(), 600);
        } catch {
            showToast('Network error — please try again.', 'error');
        }
    }

    function deleteReview(id) {
        deleteTargetId = id;
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
            const res = await fetch(`${ROUTES.destroyBase}/${deleteTargetId}`, {
                method: 'DELETE',
                headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
            });
            const json = await parseJson(res);
            closeDeleteModal();

            if (!res.ok) {
                showToast(json.message || 'Could not delete review.', 'error');
                return;
            }

            showToast(json.message || 'Review deleted.', 'success');
            setTimeout(() => location.reload(), 800);
        } catch {
            closeDeleteModal();
            showToast('Network error — please try again.', 'error');
        }
    }
</script>
@endpush