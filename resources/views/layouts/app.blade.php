<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <meta name="csrf-token" content="{{ csrf_token() }}"/>
    <title>@yield('title', 'Dashboard') — {{ setting('site_name', config('app.name')) }}</title>
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --sidebar-w: 240px;
            --topbar-h: 60px;
            --blue: {{ setting('primary_color', '#3b5bdb') }};
            --blue-dark: {{ setting('primary_color_dark', '#2f4ac0') }};
            --blue-light: #eef2ff;
            --text: #1a1a2e;
            --text-muted: #6b7280;
            --border: #e5e7eb;
            --bg: #f5f7ff;
            --white: #ffffff;
            --success: #10b981;
            --danger: #ef4444;
            --warning: #f59e0b;
            --radius: 12px;
            --shadow: 0 2px 12px rgba(0,0,0,0.07);
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: var(--bg);
            color: var(--text);
            min-height: 100vh;
            display: flex;
        }

        /* ── Sidebar ───────────────────────────────────────── */
        .sidebar {
            width: var(--sidebar-w);
            min-height: 100vh;
            background: var(--white);
            border-right: 1px solid var(--border);
            display: flex;
            flex-direction: column;
            position: fixed;
            top: 0; left: 0;
            z-index: 100;
            transition: transform 0.25s ease;
        }

        .sidebar-brand {
            height: var(--topbar-h);
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0 1.25rem;
            border-bottom: 1px solid var(--border);
        }

        .sidebar-brand-icon {
            width: 34px; height: 34px;
            background: linear-gradient(135deg, var(--blue), #4c6ef5);
            border-radius: 9px;
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0;
            overflow: hidden;
        }

        .sidebar-brand-icon img { width: 100%; height: 100%; object-fit: cover; }
        .sidebar-brand-icon svg { width: 18px; height: 18px; color: #fff; }

        .sidebar-brand-name {
            font-size: 0.9rem;
            font-weight: 700;
            color: var(--text);
            line-height: 1.2;
        }

        .sidebar-brand-name span {
            display: block;
            font-size: 0.7rem;
            font-weight: 400;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 0.04em;
        }

        .sidebar-nav {
            flex: 1;
            padding: 1rem 0.75rem;
            overflow-y: auto;
        }

        .nav-section-label {
            font-size: 0.65rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: #9ca3af;
            padding: 0.5rem 0.625rem;
            margin-top: 0.5rem;
        }

        .nav-item {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.6rem 0.75rem;
            border-radius: 9px;
            text-decoration: none;
            color: var(--text-muted);
            font-size: 0.875rem;
            font-weight: 500;
            transition: all 0.15s;
            margin-bottom: 2px;
        }

        .nav-item svg { width: 18px; height: 18px; flex-shrink: 0; }

        .nav-item:hover { background: var(--blue-light); color: var(--blue); }
        .nav-item.active { background: var(--blue-light); color: var(--blue); font-weight: 600; }
        .nav-item.disabled { opacity: 0.45; pointer-events: none; }

        .sidebar-footer { padding: 1rem 0.75rem; border-top: 1px solid var(--border); }

        .user-card {
            display: flex; align-items: center; gap: 0.75rem;
            padding: 0.625rem 0.75rem; border-radius: 9px; background: var(--bg);
        }

        .user-avatar {
            width: 34px; height: 34px; border-radius: 50%;
            background: linear-gradient(135deg, var(--blue), #4c6ef5);
            display: flex; align-items: center; justify-content: center;
            font-size: 0.875rem; font-weight: 700; color: #fff; flex-shrink: 0;
        }

        .user-info { flex: 1; min-width: 0; }
        .user-info strong { display: block; font-size: 0.8125rem; font-weight: 600; color: var(--text); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .user-info span { font-size: 0.7rem; color: var(--text-muted); text-transform: capitalize; }

        .logout-btn { background: none; border: none; cursor: pointer; color: #9ca3af; padding: 0.25rem; border-radius: 6px; display: flex; transition: color 0.15s; }
        .logout-btn:hover { color: var(--danger); }
        .logout-btn svg { width: 18px; height: 18px; }

        /* ── Main ──────────────────────────────────────────── */
        .main-wrap { margin-left: var(--sidebar-w); flex: 1; display: flex; flex-direction: column; min-height: 100vh; }

        .topbar {
            height: var(--topbar-h); background: var(--white); border-bottom: 1px solid var(--border);
            display: flex; align-items: center; justify-content: space-between;
            padding: 0 1.75rem; position: sticky; top: 0; z-index: 50;
        }

        .topbar-left h2 { font-size: 1rem; font-weight: 700; color: var(--text); }
        .topbar-left p { font-size: 0.75rem; color: var(--text-muted); }
        .topbar-right { display: flex; align-items: center; gap: 0.75rem; }

        .topbar-date {
            font-size: 0.8rem; color: var(--text-muted); background: var(--bg);
            padding: 0.35rem 0.75rem; border-radius: 8px; border: 1px solid var(--border);
        }

        .hamburger { display: none; background: none; border: none; cursor: pointer; color: var(--text); padding: 0.25rem; }
        .hamburger svg { width: 22px; height: 22px; }

        .page-content { flex: 1; padding: 1.75rem; }

        /* ── Flash ─────────────────────────────────────────── */
        .flash { display: flex; align-items: center; gap: 0.75rem; padding: 0.75rem 1rem; border-radius: var(--radius); margin-bottom: 1.25rem; font-size: 0.875rem; font-weight: 500; }
        .flash.success { background: #d1fae5; color: #065f46; border: 1px solid #a7f3d0; }
        .flash.error { background: #fee2e2; color: #991b1b; border: 1px solid #fca5a5; }
        .flash svg { width: 18px; height: 18px; flex-shrink: 0; }

        /* ── Modal (base — pages define their own overlays reusing these classes) ── */
        .modal-overlay {
            position: fixed; inset: 0; background: rgba(0,0,0,0.45); backdrop-filter: blur(3px);
            z-index: 1000; display: none; align-items: center; justify-content: center; padding: 1rem;
        }
        .modal-overlay.open { display: flex; }

        .modal {
            background: var(--white); border-radius: 18px; width: 100%; max-width: 520px;
            max-height: 90vh; overflow-y: auto; box-shadow: 0 20px 60px rgba(0,0,0,0.18);
            animation: modalIn 0.2s ease;
        }
        @keyframes modalIn { from { opacity: 0; transform: scale(0.95) translateY(10px); } to { opacity: 1; transform: scale(1) translateY(0); } }

        .modal-header { display: flex; align-items: center; justify-content: space-between; padding: 1.25rem 1.5rem; border-bottom: 1px solid var(--border); }
        .modal-header h3 { font-size: 1rem; font-weight: 700; color: var(--text); }
        .modal-close { background: #f3f4f6; border: none; cursor: pointer; width: 30px; height: 30px; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: var(--text-muted); transition: background 0.15s; }
        .modal-close:hover { background: #e5e7eb; }
        .modal-close svg { width: 16px; height: 16px; }
        .modal-body { padding: 1.5rem; }
        .modal-footer { display: flex; justify-content: flex-end; gap: 0.75rem; padding: 1rem 1.5rem; border-top: 1px solid var(--border); }

        /* ── Buttons ───────────────────────────────────────── */
        .btn { display: inline-flex; align-items: center; gap: 0.4rem; padding: 0.55rem 1.1rem; border-radius: 9px; font-size: 0.875rem; font-weight: 600; border: none; cursor: pointer; transition: all 0.15s; text-decoration: none; }
        .btn svg { width: 16px; height: 16px; }
        .btn-primary { background: var(--blue); color: #fff; box-shadow: 0 2px 8px rgba(59,91,219,0.3); }
        .btn-primary:hover { background: var(--blue-dark); }
        .btn-primary:disabled { opacity: 0.6; cursor: not-allowed; }
        .btn-secondary { background: #f3f4f6; color: var(--text); }
        .btn-secondary:hover { background: #e5e7eb; }
        .btn-danger { background: #fee2e2; color: var(--danger); }
        .btn-danger:hover { background: #fecaca; }
        .btn-success { background: #d1fae5; color: #065f46; }
        .btn-success:hover { background: #a7f3d0; }
        .btn-sm { padding: 0.35rem 0.75rem; font-size: 0.8rem; }

        /* ── Forms ─────────────────────────────────────────── */
        .form-group { margin-bottom: 1.1rem; }
        .form-group label { display: block; font-size: 0.8rem; font-weight: 600; color: #374151; margin-bottom: 0.4rem; }
        .form-group input, .form-group select, .form-group textarea {
            width: 100%; padding: 0.65rem 0.9rem; border: 1.5px solid var(--border); border-radius: 9px;
            font-size: 0.875rem; color: var(--text); background: #fafafa; outline: none;
            transition: border-color 0.2s, box-shadow 0.2s; font-family: inherit;
        }
        .form-group input:focus, .form-group select:focus, .form-group textarea:focus {
            border-color: var(--blue); box-shadow: 0 0 0 3px rgba(59,91,219,0.1); background: #fff;
        }
        .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; }
        .req { color: var(--danger); }
        .field-error { display: block; font-size: 0.75rem; color: var(--danger); margin-top: 0.25rem; min-height: 1rem; }

        /* Toggle switch */
        .toggle-label { display: flex; align-items: center; gap: 0.75rem; cursor: pointer; font-size: 0.875rem; color: #374151; font-weight: 500; position: relative; user-select: none; }
        .toggle-input { position: absolute; opacity: 0; width: 0; height: 0; pointer-events: none; }
        .toggle-switch { width: 44px; height: 24px; background: #d1d5db; border-radius: 20px; position: relative; transition: background 0.25s ease; flex-shrink: 0; display: inline-block; cursor: pointer; }
        .toggle-switch::after { content: ''; position: absolute; top: 3px; left: 3px; width: 18px; height: 18px; background: #fff; border-radius: 50%; transition: transform 0.25s ease; box-shadow: 0 1px 4px rgba(0,0,0,0.2); }
        .toggle-input:checked ~ .toggle-switch { background: var(--blue); }
        .toggle-input:checked ~ .toggle-switch::after { transform: translateX(20px); }

        /* ── Table ─────────────────────────────────────────── */
        .table-wrap { background: var(--white); border-radius: var(--radius); border: 1px solid var(--border); overflow: hidden; box-shadow: var(--shadow); }
        .table-toolbar { display: flex; align-items: center; justify-content: space-between; padding: 1rem 1.25rem; border-bottom: 1px solid var(--border); gap: 1rem; flex-wrap: wrap; }
        .table-toolbar-title { font-size: 0.9375rem; font-weight: 700; color: var(--text); }
        .table-toolbar-actions { display: flex; gap: 0.5rem; align-items: center; flex-wrap: wrap; }
        .count-badge { display: inline-flex; align-items: center; justify-content: center; background: var(--blue-light); color: var(--blue); font-size: 0.7rem; font-weight: 700; padding: 0.1rem 0.5rem; border-radius: 20px; margin-left: 0.5rem; vertical-align: middle; }

        .search-box { display: flex; align-items: center; gap: 0.5rem; border: 1.5px solid var(--border); border-radius: 9px; padding: 0.45rem 0.75rem; background: #fafafa; }
        .search-box svg { width: 16px; height: 16px; color: #9ca3af; }
        .search-box input { border: none; background: none; outline: none; font-size: 0.875rem; color: var(--text); width: 180px; }

        table { width: 100%; border-collapse: collapse; }
        thead th { font-size: 0.75rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: var(--text-muted); padding: 0.75rem 1.25rem; background: #f9fafb; text-align: left; border-bottom: 1px solid var(--border); }
        tbody td { padding: 0.875rem 1.25rem; font-size: 0.875rem; color: var(--text); border-bottom: 1px solid #f3f4f6; }
        tbody tr:last-child td { border-bottom: none; }
        tbody tr:hover { background: #fafbff; }
        .action-btns { display: flex; gap: 0.4rem; }

        .empty-state { text-align: center; padding: 3rem !important; color: #9ca3af; }
        .empty-state svg { width: 48px; height: 48px; margin: 0 auto 0.75rem; display: block; color: #d1d5db; }
        .empty-state p { font-size: 0.9rem; }

        /* ── Badges ────────────────────────────────────────── */
        .badge { display: inline-flex; align-items: center; gap: 0.3rem; font-size: 0.7rem; font-weight: 700; padding: 0.2rem 0.55rem; border-radius: 20px; text-transform: capitalize; }
        .badge-green  { background: #d1fae5; color: #065f46; }
        .badge-red    { background: #fee2e2; color: #991b1b; }
        .badge-blue   { background: #dbeafe; color: #1e40af; }
        .badge-yellow { background: #fef3c7; color: #92400e; }
        .badge-purple { background: #ede9fe; color: #5b21b6; }
        .badge-gray   { background: #f3f4f6; color: #374151; }

        /* ── Stat Cards ────────────────────────────────────── */
        .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1.1rem; margin-bottom: 1.75rem; }
        .stat-card { background: var(--white); border-radius: var(--radius); padding: 1.25rem; border: 1px solid var(--border); box-shadow: var(--shadow); display: flex; align-items: center; gap: 1rem; }
        .stat-icon { width: 46px; height: 46px; border-radius: 12px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
        .stat-icon svg { width: 22px; height: 22px; }
        .stat-info p { font-size: 0.8rem; color: var(--text-muted); margin-bottom: 0.2rem; }
        .stat-info strong { font-size: 1.6rem; font-weight: 800; color: var(--text); line-height: 1; }
        .stat-info small { display: block; font-size: 0.7rem; color: var(--text-muted); margin-top: 0.2rem; }

        /* ── Toast ─────────────────────────────────────────── */
        .toast {
            position: fixed; bottom: 1.5rem; right: 1.5rem; background: #1f2937; color: #fff;
            padding: 0.75rem 1.25rem; border-radius: 12px; font-size: 0.875rem; font-weight: 500;
            box-shadow: 0 8px 24px rgba(0,0,0,0.2); transform: translateY(100px); opacity: 0;
            transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1); z-index: 9999;
            display: flex; align-items: center; gap: 0.5rem; max-width: 320px;
        }
        .toast.show { transform: translateY(0); opacity: 1; }
        .toast.success { background: #065f46; }
        .toast.error { background: #991b1b; }

        /* ── Spinner / button loaders ──────────────────────── */
        @keyframes spin { to { transform: rotate(360deg); } }
        .spin { animation: spin 0.8s linear infinite; display: inline-block; }
        .table-loader { display: flex; justify-content: center; padding: 2rem; }
        .spinner { width: 32px; height: 32px; border: 3px solid #e5e7eb; border-top-color: var(--blue); border-radius: 50%; animation: spin 0.7s linear infinite; }

        /* ── Pagination ────────────────────────────────────── */
        .pagination-wrap { display: flex; align-items: center; gap: 0.35rem; padding: 1rem 1.25rem; border-top: 1px solid var(--border); flex-wrap: wrap; }
        .page-btn { display: inline-flex; align-items: center; justify-content: center; min-width: 34px; height: 34px; padding: 0 0.5rem; border-radius: 8px; font-size: 0.8125rem; font-weight: 600; color: var(--text-muted); background: #f9fafb; border: 1px solid var(--border); text-decoration: none; transition: all 0.15s; cursor: pointer; }
        .page-btn:hover:not(.disabled):not(.active) { background: var(--blue-light); color: var(--blue); border-color: var(--blue); }
        .page-btn.active { background: var(--blue); color: #fff; border-color: var(--blue); }
        .page-btn.disabled { opacity: 0.4; cursor: not-allowed; }
        .page-info { margin-left: auto; font-size: 0.8rem; color: var(--text-muted); }

        /* ── Responsive ────────────────────────────────────── */
        @media (max-width: 768px) {
            .sidebar { transform: translateX(-100%); }
            .sidebar.open { transform: translateX(0); box-shadow: 4px 0 20px rgba(0,0,0,0.15); }
            .main-wrap { margin-left: 0; }
            .hamburger { display: flex; }
            .form-row { grid-template-columns: 1fr; }
            .overlay-bg { display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.4); z-index: 99; }
            .overlay-bg.open { display: block; }
        }
    </style>
    @stack('styles')
</head>
<body>

<div class="overlay-bg" id="overlayBg" onclick="closeSidebar()"></div>

{{-- ── Sidebar ────────────────────────────────────────────── --}}
<aside class="sidebar" id="sidebar">
    <div class="sidebar-brand">
        <div class="sidebar-brand-icon">
            @if (setting('site_logo'))
                 <img src="{{ setting_image('site_logo') }}" alt="{{ setting('site_name') }}">
            @else
                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                </svg>
            @endif
        </div>
        <div class="sidebar-brand-name">
            {{ setting('site_name', config('app.name', 'Store')) }}
            <span>Admin Panel</span>
        </div>
    </div>

    <nav class="sidebar-nav">
        @include('admin.partials.sidebar')
    </nav>

    <div class="sidebar-footer">
        <div class="user-card">
            <div class="user-avatar">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</div>
            <div class="user-info">
                <strong>{{ auth()->user()->name }}</strong>
                <span>{{ auth()->user()->role }}</span>
            </div>
            <form method="POST" action="{{ route('admin.logout') }}">
                @csrf
                <button type="submit" class="logout-btn" title="Logout">
                    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                    </svg>
                </button>
            </form>
        </div>
    </div>
</aside>

{{-- ── Main ───────────────────────────────────────────────── --}}
<div class="main-wrap">
    <header class="topbar">
        <div style="display:flex; align-items:center; gap:0.75rem;">
            <button class="hamburger" onclick="toggleSidebar()">
                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
            </button>
            <div class="topbar-left">
                <h2>@yield('page-title', 'Dashboard')</h2>
                <p>@yield('page-subtitle', '')</p>
            </div>
        </div>
        <div class="topbar-right">
            <span class="topbar-date" id="topbarDate"></span>
        </div>
    </header>

    <main class="page-content">
        @if (session('success'))
            <div class="flash success">
                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                {{ session('success') }}
            </div>
        @endif
        @if (session('error'))
            <div class="flash error">
                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                {{ session('error') }}
            </div>
        @endif

        @yield('content')
    </main>
</div>

{{-- Global toast (every page's JS calls showToast(message, type)) --}}
<div id="toast" class="toast"></div>

<script>
    const CSRF = document.querySelector('meta[name="csrf-token"]').content;

    function toggleSidebar() {
        document.getElementById('sidebar').classList.toggle('open');
        document.getElementById('overlayBg').classList.toggle('open');
    }
    function closeSidebar() {
        document.getElementById('sidebar').classList.remove('open');
        document.getElementById('overlayBg').classList.remove('open');
    }

    function showToast(message, type = 'success') {
        const t = document.getElementById('toast');
        t.textContent = message;
        t.className = `toast ${type} show`;
        setTimeout(() => { t.className = 'toast'; }, 3500);
    }

    function updateDate() {
        const now = new Date();
        document.getElementById('topbarDate').textContent =
            now.toLocaleDateString('en-US', { weekday: 'short', year: 'numeric', month: 'short', day: 'numeric' });
    }
    updateDate();

    document.querySelectorAll('.nav-item').forEach(link => {
        if (link.href === window.location.href) link.classList.add('active');
    });

    document.addEventListener('keydown', e => {
        if (e.key === 'Escape') {
            document.querySelectorAll('.modal-overlay.open').forEach(m => {
                m.classList.remove('open');
                document.body.style.overflow = '';
            });
        }
    });
</script>

@stack('scripts')
</body>
</html>