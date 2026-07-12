<a href="{{ route('admin.dashboard') }}" class="nav-item">
    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 12l9-9 9 9M5 10v10h14V10"/></svg>
    Dashboard
</a>

<div class="nav-section-label">Catalog</div>
<a href="{{ route('admin.categories.index') }}" class="nav-item">
    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h7"/></svg>
    Categories
</a>
<a href="{{ route('admin.products.index') }}" class="nav-item">
    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
    Products
</a>

<div class="nav-section-label">Sales</div>
<a href="{{ route('admin.orders.index') }}" class="nav-item">
    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 2h6l1 4H8l1-4zM4 7h16l-1.5 13a2 2 0 01-2 2H7.5a2 2 0 01-2-2L4 7z"/></svg>
    Orders
</a>
<a href="{{ route('admin.coupons.index') }}" class="nav-item">
    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M20.59 13.41L11 3.83A2 2 0 009.59 3.24L4 3a1 1 0 00-1 1l.24 5.59a2 2 0 00.59 1.41l9.58 9.58a2 2 0 002.83 0l4.35-4.35a2 2 0 000-2.82z"/></svg>
    Coupons
</a>
<a href="{{ route('admin.reviews.index') }}" class="nav-item">
    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 17.75l-6.16 3.24 1.18-6.88L2 9.24l6.92-1L12 2l3.08 6.24 6.92 1-5.02 4.87 1.18 6.88z"/></svg>
    Reviews
</a>

<div class="nav-section-label">People</div>
<a href="{{ route('admin.customers.index') }}" class="nav-item">
    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2M9 11a4 4 0 100-8 4 4 0 000 8zM23 21v-2a4 4 0 00-3-3.87M16 3.13a4 4 0 010 7.75"/></svg>
    Customers
</a>

<div class="nav-section-label">Store</div>
<a href="{{ route('admin.settings.edit') }}" class="nav-item">
    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317a1 1 0 011.35 0l1.318 1.06a1 1 0 00.66.243h1.7a1 1 0 011 1v1.7a1 1 0 00.244.66l1.06 1.318a1 1 0 010 1.35l-1.06 1.318a1 1 0 00-.243.66v1.7a1 1 0 01-1 1h-1.7a1 1 0 00-.66.244l-1.318 1.06a1 1 0 01-1.35 0l-1.318-1.06a1 1 0 00-.66-.243h-1.7a1 1 0 01-1-1v-1.7a1 1 0 00-.244-.66l-1.06-1.318a1 1 0 010-1.35l1.06-1.318a1 1 0 00.243-.66v-1.7a1 1 0 011-1h1.7a1 1 0 00.66-.244l1.318-1.06z"/></svg>
    Store Settings
</a>
<a href="{{ url('/') }}" target="_blank" class="nav-item">
    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
    View Store
</a>