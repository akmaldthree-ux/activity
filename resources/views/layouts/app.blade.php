<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Daily Plan') — {{ config('app.name') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        /* ─── Design tokens ─────────────────────────────────────────── */
        :root {
            --n-primary:        #0075de;
            --n-primary-active: #005bab;
            --n-secondary:      #213183;
            --n-canvas:         #ffffff;
            --n-canvas-soft:    #f6f5f4;
            --n-surface:        #ffffff;
            --n-ink:            #000000;
            --n-ink-2:          #31302e;
            --n-ink-muted:      #615d59;
            --n-ink-faint:      #a39e98;
            --n-hairline:       #e6e6e6;
            --n-teal:           #2a9d99;
            --n-green:          #1aae39;
            --n-r-xs:   4px;
            --n-r-sm:   5px;
            --n-r-md:   8px;
            --n-r-lg:   12px;
            --n-r-xl:   16px;
            --n-r-full: 9999px;
            --n-shadow:
                0 0.175px 1.041px rgba(0,0,0,.01),
                0 0.8px   2.925px rgba(0,0,0,.02),
                0 2.025px 7.847px rgba(0,0,0,.027),
                0 4px     18px    rgba(0,0,0,.04);
            /* Bootstrap token overrides */
            --bs-primary:        #0075de;
            --bs-primary-rgb:    0,117,222;
            --bs-body-bg:        #f6f5f4;
            --bs-body-color:     #000000;
            --bs-border-color:   #e6e6e6;
        }

        /* ─── Base ───────────────────────────────────────────────────── */
        body {
            font-family: 'Inter', -apple-system, system-ui, 'Segoe UI', Helvetica, Arial, sans-serif;
            background: var(--n-canvas-soft);
            color: var(--n-ink);
            font-size: 14px;
            line-height: 1.5;
        }

        /* ─── Top nav ────────────────────────────────────────────────── */
        .n-topnav {
            position: sticky; top: 0; z-index: 1030;
            background: var(--n-canvas);
            border-bottom: 1px solid var(--n-hairline);
            height: 56px;
            display: flex; align-items: center;
            padding: 0 16px; gap: 6px;
        }
        .n-brand {
            display: flex; align-items: center; gap: 8px;
            font-size: 15px; font-weight: 700;
            letter-spacing: -0.125px;
            color: var(--n-ink); text-decoration: none;
        }
        .n-brand:hover { color: var(--n-ink); }
        .n-brand-icon {
            width: 28px; height: 28px; border-radius: 6px;
            background: var(--n-primary);
            display: flex; align-items: center; justify-content: center;
            color: #fff; font-size: 14px; flex-shrink: 0;
        }
        .n-topnav-right {
            margin-left: auto;
            display: flex; align-items: center; gap: 2px;
        }
        .n-iconbtn {
            width: 34px; height: 34px; border-radius: var(--n-r-full);
            display: flex; align-items: center; justify-content: center;
            background: transparent; border: none; cursor: pointer;
            color: var(--n-ink-muted); font-size: 17px;
            text-decoration: none; transition: background .12s; position: relative;
        }
        .n-iconbtn:hover { background: rgba(0,0,0,.05); color: var(--n-ink); }
        .n-notif-badge {
            position: absolute; top: 2px; right: 2px;
            min-width: 16px; height: 16px;
            background: #e03131; border-radius: 9999px;
            font-size: 10px; font-weight: 700; color: #fff;
            display: flex; align-items: center; justify-content: center;
            padding: 0 3px; line-height: 1;
        }
        .n-userbtn {
            display: flex; align-items: center; gap: 7px;
            padding: 4px 10px; border-radius: var(--n-r-full);
            background: transparent; border: none; cursor: pointer;
            color: var(--n-ink); font-size: 14px; font-weight: 500;
            transition: background .12s;
        }
        .n-userbtn:hover { background: rgba(0,0,0,.05); }
        .n-userbtn.dropdown-toggle::after { display: none; }
        .n-avatar {
            width: 26px; height: 26px; border-radius: 50%;
            background: var(--n-primary); color: #fff;
            font-size: 11px; font-weight: 700;
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0;
        }
        .n-hamburger { display: none; }
        @media (max-width: 767px) {
            .n-hamburger { display: flex; }
            .n-username  { display: none; }
        }

        /* ─── Shell ──────────────────────────────────────────────────── */
        .n-shell { display: flex; min-height: calc(100vh - 56px); }

        /* ─── Sidebar ────────────────────────────────────────────────── */
        .n-sidebar {
            width: 216px; flex-shrink: 0;
            background: var(--n-canvas);
            border-right: 1px solid var(--n-hairline);
            padding: 10px 6px; overflow-y: auto;
        }
        .n-nav-item { margin-bottom: 1px; }
        .n-nav-item > a {
            display: flex; align-items: center; gap: 9px;
            padding: 7px 10px; border-radius: var(--n-r-sm);
            color: var(--n-ink-2); font-size: 13.5px; font-weight: 400;
            text-decoration: none; transition: background .1s; position: relative;
        }
        .n-nav-item > a i { font-size: 14px; color: var(--n-ink-faint); width: 16px; flex-shrink: 0; text-align: center; }
        .n-nav-item > a:hover { background: rgba(0,0,0,.04); color: var(--n-ink); }
        .n-nav-item > a:hover i { color: var(--n-ink-muted); }
        .n-nav-item > a.active { background: rgba(0,117,222,.07); color: var(--n-primary); font-weight: 500; }
        .n-nav-item > a.active i { color: var(--n-primary); }
        .n-nav-item > a.active::before {
            content: ''; position: absolute;
            left: 0; top: 6px; bottom: 6px;
            width: 3px; background: var(--n-primary);
            border-radius: 0 2px 2px 0;
        }
        .n-nav-section {
            font-size: 11px; font-weight: 600;
            letter-spacing: 0.125px; text-transform: uppercase;
            color: var(--n-ink-faint); padding: 10px 10px 4px; margin-top: 4px;
        }
        .n-nav-divider { height: 1px; background: var(--n-hairline); margin: 6px 10px; }
        .n-nav-badge {
            margin-left: auto;
            background: var(--n-primary); color: #fff;
            font-size: 11px; font-weight: 600; border-radius: 9999px;
            padding: 1px 6px; min-width: 18px; text-align: center;
        }
        .n-nav-badge-warn { background: #c47a00; }

        /* Mobile sidebar */
        @media (max-width: 767px) {
            .n-sidebar {
                position: fixed; top: 56px; left: 0; bottom: 0; z-index: 1020;
                width: 240px; transform: translateX(-100%);
                transition: transform .2s; box-shadow: 2px 0 20px rgba(0,0,0,.12);
            }
            .n-sidebar.open { transform: translateX(0); }
            .n-backdrop { display: none; position: fixed; inset: 0; background: rgba(0,0,0,.3); z-index: 1010; }
            .n-backdrop.open { display: block; }
        }
        @media (min-width: 768px) { .n-backdrop { display: none !important; } }

        /* ─── Main ───────────────────────────────────────────────────── */
        .n-main { flex: 1; min-width: 0; padding: 28px 32px; }
        @media (max-width: 991px) { .n-main { padding: 20px 20px; } }
        @media (max-width: 767px) { .n-main { padding: 16px; } }

        /* ─── Cards ──────────────────────────────────────────────────── */
        .card {
            background: var(--n-surface) !important;
            border: 1px solid var(--n-hairline) !important;
            border-radius: var(--n-r-lg) !important;
        }
        .card-header {
            background: var(--n-surface) !important;
            border-bottom: 1px solid var(--n-hairline) !important;
            font-size: 14px; font-weight: 600; color: var(--n-ink);
            padding: 13px 16px;
        }
        .card-body { padding: 16px; }
        .shadow-sm, .shadow { box-shadow: var(--n-shadow) !important; }

        /* ─── Tables ─────────────────────────────────────────────────── */
        .table { --bs-table-border-color: var(--n-hairline); }
        .table th {
            font-size: 11px; font-weight: 600;
            letter-spacing: 0.125px; text-transform: uppercase;
            color: var(--n-ink-muted) !important; padding: 10px 14px;
        }
        .table td { padding: 10px 14px; font-size: 14px; vertical-align: middle; }
        .table-light, .table-light > * { background-color: var(--n-canvas-soft) !important; }
        .table-hover > tbody > tr:hover > * { background-color: rgba(0,0,0,.02) !important; }

        /* ─── Buttons ────────────────────────────────────────────────── */
        .btn { font-family: 'Inter', sans-serif; font-size: 14px; font-weight: 500; line-height: 1.5; }
        .btn-primary {
            background: var(--n-primary) !important; border-color: var(--n-primary) !important;
            color: #fff !important; border-radius: var(--n-r-full) !important;
        }
        .btn-primary:hover, .btn-primary:focus {
            background: var(--n-primary-active) !important; border-color: var(--n-primary-active) !important;
        }
        .btn-success {
            background: var(--n-green) !important; border-color: var(--n-green) !important;
            border-radius: var(--n-r-full) !important;
        }
        .btn-success:hover { background: #14922f !important; border-color: #14922f !important; }
        .btn-secondary { border-radius: var(--n-r-full) !important; }
        .btn-outline-primary {
            color: var(--n-primary) !important; border-color: var(--n-primary) !important;
            border-radius: var(--n-r-md) !important;
        }
        .btn-outline-primary:hover { background: var(--n-primary) !important; color: #fff !important; }
        .btn-outline-secondary {
            border-color: var(--n-hairline) !important; color: var(--n-ink-2) !important;
            border-radius: var(--n-r-md) !important;
        }
        .btn-outline-secondary:hover { background: rgba(0,0,0,.05) !important; color: var(--n-ink) !important; }
        .btn-outline-danger  { border-radius: var(--n-r-md) !important; }
        .btn-outline-success { border-radius: var(--n-r-md) !important; }
        .btn-outline-warning { border-radius: var(--n-r-md) !important; }
        .btn-sm { font-size: 13px; padding: 4px 12px; }
        .btn-sm.btn-primary, .btn-sm.btn-success, .btn-sm.btn-secondary { border-radius: var(--n-r-full) !important; }
        .btn-sm.btn-outline-primary, .btn-sm.btn-outline-secondary,
        .btn-sm.btn-outline-danger, .btn-sm.btn-outline-success { border-radius: var(--n-r-sm) !important; }
        .btn-lg { border-radius: var(--n-r-full) !important; }

        /* ─── Forms ──────────────────────────────────────────────────── */
        .form-control, .form-select {
            border-color: var(--n-hairline); border-radius: var(--n-r-xs) !important;
            font-size: 14px; color: var(--n-ink); background: var(--n-surface);
        }
        .form-control:focus, .form-select:focus {
            border-color: var(--n-primary); box-shadow: 0 0 0 3px rgba(0,117,222,.1);
        }
        .form-control-sm, .form-select-sm { font-size: 13px; border-radius: var(--n-r-xs) !important; }
        .form-label { font-size: 13px; font-weight: 600; color: var(--n-ink-2); margin-bottom: 5px; }
        .form-text { font-size: 12px; color: var(--n-ink-muted); }
        .input-group-text {
            background: var(--n-canvas-soft); border-color: var(--n-hairline);
            color: var(--n-ink-muted); font-size: 14px;
        }

        /* ─── Alerts ─────────────────────────────────────────────────── */
        .alert { border-radius: var(--n-r-md) !important; font-size: 14px; }
        .alert-success { background: #f0faf3 !important; border-color: #c3e6cb !important; color: #155724 !important; }
        .alert-danger  { background: #fff5f5 !important; border-color: #f5c6cb !important; color: #721c24 !important; }
        .alert-warning { background: #fffbf0 !important; border-color: #fde8a1 !important; color: #7d5a00 !important; }
        .alert-info    { background: #f0f7ff !important; border-color: #bfdbfe !important; color: #1e40af !important; }

        /* ─── Badges ─────────────────────────────────────────────────── */
        .badge { font-size: 11px; font-weight: 600; border-radius: var(--n-r-xs); letter-spacing: 0.05px; }
        .badge.rounded-pill { border-radius: 9999px !important; }
        .bg-primary { background-color: var(--n-primary) !important; }
        .bg-success { background-color: var(--n-green)   !important; }
        .bg-info    { background-color: var(--n-teal)    !important; }

        /* ─── Dropdowns ──────────────────────────────────────────────── */
        .dropdown-menu {
            border: 1px solid var(--n-hairline); border-radius: var(--n-r-md);
            box-shadow: var(--n-shadow), 0 8px 24px rgba(0,0,0,.08);
            font-size: 14px; padding: 4px;
        }
        .dropdown-item { border-radius: 5px; padding: 7px 12px; color: var(--n-ink-2); }
        .dropdown-item:hover, .dropdown-item:focus { background: rgba(0,0,0,.04); color: var(--n-ink); }
        .dropdown-item.text-danger:hover { background: #fff5f5; }
        .dropdown-divider { border-color: var(--n-hairline); margin: 4px 0; }

        /* ─── Progress ───────────────────────────────────────────────── */
        .progress { background: rgba(0,0,0,.06); border-radius: 9999px; }
        .progress-bar { border-radius: 9999px; }
        .progress-bar.bg-success { background-color: var(--n-green) !important; }

        /* ─── Typography ─────────────────────────────────────────────── */
        .text-muted   { color: var(--n-ink-muted) !important; }
        .text-primary { color: var(--n-primary)   !important; }
        .text-success { color: var(--n-green)      !important; }
        .text-info    { color: var(--n-teal)       !important; }
        .text-warning { color: #c47a00             !important; }
        h1, h2, h3, h4, h5, h6 { letter-spacing: -0.25px; }
        h5.fw-bold, h5.fw-semibold { font-size: 18px; letter-spacing: -0.25px; }

        /* ─── Calendar ───────────────────────────────────────────────── */
        .cal-day {
            min-height: 72px; cursor: pointer; transition: background .12s;
            border-radius: var(--n-r-md) !important; position: relative;
            border-color: var(--n-hairline) !important;
        }
        .cal-day:hover { background: rgba(0,117,222,.05); }
        .cal-day.today { border: 2px solid var(--n-primary) !important; }
        .cal-day.weekend { background: rgba(0,0,0,.025); color: var(--n-ink-faint); cursor: default; }
        .cal-day.weekend:hover { background: rgba(0,0,0,.025); }
        .cal-day.other-month { color: var(--n-ink-faint); }
        .cal-day .day-num { font-size: .8rem; font-weight: 600; }
        .indicator { width: 7px; height: 7px; border-radius: 50%; display: inline-block; }
        .indicator-belum         { background: var(--n-ink-faint); }
        .indicator-plan_ok       { background: var(--n-primary); }
        .indicator-lengkap       { background: var(--n-green); }
        .indicator-plan_telat,
        .indicator-laporan_telat { background: #e03131; }
        .status-dot { width: 8px; height: 8px; border-radius: 50%; display: inline-block; }

        /* ─── Pagination ─────────────────────────────────────────────── */
        .page-link { color: var(--n-primary); border-color: var(--n-hairline); border-radius: var(--n-r-xs) !important; }
        .page-item.active .page-link { background: var(--n-primary); border-color: var(--n-primary); }

        /* ─── Background utilities ───────────────────────────────────── */
        .bg-light     { background-color: var(--n-canvas-soft) !important; }
        .bg-white     { background-color: var(--n-surface)     !important; }
        .bg-primary   { background-color: var(--n-primary)     !important; }
        .bg-success   { background-color: var(--n-green)       !important; }
        .bg-info      { background-color: var(--n-teal)        !important; }
        .bg-warning   { background-color: #c47a00              !important; }
        .bg-secondary { background-color: #6b6869              !important; }
        .bg-danger    { background-color: #c92a2a              !important; }

        /* ─── Extra buttons ──────────────────────────────────────────── */
        .btn-warning {
            background: #c47a00 !important; border-color: #c47a00 !important;
            color: #fff !important; border-radius: var(--n-r-full) !important;
        }
        .btn-warning:hover { background: #a86500 !important; border-color: #a86500 !important; }
        .btn-info {
            background: var(--n-teal) !important; border-color: var(--n-teal) !important;
            color: #fff !important; border-radius: var(--n-r-full) !important;
        }
        .btn-info:hover { background: #228a86 !important; }
        .btn-danger {
            background: #c92a2a !important; border-color: #c92a2a !important;
            border-radius: var(--n-r-full) !important;
        }
        .btn-danger:hover { background: #b02020 !important; }
        .btn-outline-danger  { border-color: #c92a2a !important; color: #c92a2a !important; }
        .btn-sm.btn-warning, .btn-sm.btn-info, .btn-sm.btn-danger { border-radius: var(--n-r-full) !important; }

        /* ─── Modals ─────────────────────────────────────────────────── */
        .modal-content { border: 1px solid var(--n-hairline); border-radius: var(--n-r-xl) !important; }
        .modal-header { border-bottom: 1px solid var(--n-hairline); padding: 16px 20px; }
        .modal-footer { border-top: 1px solid var(--n-hairline); padding: 12px 20px; }
        .modal-body { padding: 20px; }

        /* ─── Initials avatar helper ─────────────────────────────────── */
        .n-initials {
            border-radius: 50%; display: flex; align-items: center; justify-content: center;
            color: #fff; font-weight: 700; flex-shrink: 0;
        }

        /* ─── Misc ───────────────────────────────────────────────────── */
        .rounded { border-radius: var(--n-r-md) !important; }
        .border { border-color: var(--n-hairline) !important; }
        @media (max-width: 767px) { .cal-day { min-height: 48px; } }
    </style>
    @stack('styles')
</head>
<body>

{{-- ─ Top nav ─────────────────────────────────────────────────────── --}}
<header class="n-topnav">
    <button class="n-iconbtn n-hamburger" onclick="sidebarOpen()" aria-label="Menu">
        <i class="bi bi-list" style="font-size:20px"></i>
    </button>
    <a href="{{ route('dashboard') }}" class="n-brand">
        <span class="n-brand-icon"><i class="bi bi-calendar-check"></i></span>
        Daily Plan
    </a>
    <div class="n-topnav-right">
        @php $unreadCount = \App\Models\InAppNotification::where('user_id', Auth::id())->whereNull('read_at')->count(); @endphp
        <a href="{{ route('notifications.index') }}" class="n-iconbtn" title="Notifikasi">
            <i class="bi bi-bell"></i>
            @if($unreadCount > 0)
                <span class="n-notif-badge">{{ $unreadCount > 9 ? '9+' : $unreadCount }}</span>
            @endif
        </a>
        <div class="dropdown">
            <button class="n-userbtn dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                <span class="n-avatar">{{ strtoupper(substr(Auth::user()->name, 0, 1)) }}</span>
                <span class="n-username" style="max-width:140px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap">
                    {{ \Str::words(Auth::user()->name, 2, '') }}
                </span>
                <i class="bi bi-chevron-down" style="font-size:10px;color:var(--n-ink-faint)"></i>
            </button>
            <ul class="dropdown-menu dropdown-menu-end mt-1" style="min-width:210px">
                <li class="px-3 pt-2 pb-1">
                    <div style="font-weight:600;font-size:14px;color:var(--n-ink)">{{ Auth::user()->name }}</div>
                    <div style="font-size:12px;color:var(--n-ink-muted);margin-top:1px">{{ Auth::user()->division?->name ?? '—' }}</div>
                    <span style="display:inline-block;margin-top:5px;font-size:11px;font-weight:600;letter-spacing:.125px;text-transform:uppercase;color:var(--n-ink-muted);background:rgba(0,0,0,.05);padding:2px 7px;border-radius:4px">
                        {{ Auth::user()->roleLabel() }}
                    </span>
                </li>
                <li><hr class="dropdown-divider"></li>
                <li><a class="dropdown-item" href="{{ route('profile.show') }}">
                    <i class="bi bi-person-circle me-2" style="color:var(--n-ink-muted)"></i>Profil Saya
                </a></li>
                <li><hr class="dropdown-divider"></li>
                <li>
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="dropdown-item text-danger">
                            <i class="bi bi-box-arrow-right me-2"></i>Keluar
                        </button>
                    </form>
                </li>
            </ul>
        </div>
    </div>
</header>

{{-- Mobile backdrop --}}
<div class="n-backdrop" id="nBackdrop" onclick="sidebarClose()"></div>

{{-- ─ Shell ─────────────────────────────────────────────────────────── --}}
<div class="n-shell">
    <aside class="n-sidebar" id="nSidebar">
        @include('shared.sidebar')
    </aside>

    <main class="n-main">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
                <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
                <i class="bi bi-exclamation-circle me-2"></i>{{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @yield('content')
    </main>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
function sidebarOpen()  { document.getElementById('nSidebar').classList.add('open');    document.getElementById('nBackdrop').classList.add('open'); }
function sidebarClose() { document.getElementById('nSidebar').classList.remove('open'); document.getElementById('nBackdrop').classList.remove('open'); }
</script>
@stack('scripts')
</body>
</html>
