<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Daily Plan') — {{ config('app.name') }}</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body { background-color: #f8f9fa; font-size: 0.925rem; }
        .navbar-brand { font-weight: 700; letter-spacing: .5px; }
        .sidebar { min-height: calc(100vh - 56px); background: #fff; border-right: 1px solid #dee2e6; }
        .sidebar .nav-link { color: #495057; border-radius: 6px; margin-bottom: 2px; }
        .sidebar .nav-link:hover, .sidebar .nav-link.active { background: #e9f0ff; color: #0d6efd; }
        .sidebar .nav-link i { width: 20px; }
        .status-dot { width: 10px; height: 10px; border-radius: 50%; display: inline-block; }
        .cal-day { min-height: 70px; cursor: pointer; transition: background .15s; border-radius: 8px; position: relative; }
        .cal-day:hover { background: #e9f0ff; }
        .cal-day.today { border: 2px solid #0d6efd !important; }
        .cal-day.weekend { background: #f1f3f5; color: #adb5bd; cursor: default; }
        .cal-day.weekend:hover { background: #f1f3f5; }
        .cal-day.other-month { color: #ced4da; }
        .cal-day .day-num { font-size: .85rem; font-weight: 600; }
        .indicator { width: 8px; height: 8px; border-radius: 50%; display: inline-block; }
        .indicator-belum    { background: #adb5bd; }
        .indicator-plan_ok  { background: #0d6efd; }
        .indicator-lengkap  { background: #198754; }
        .indicator-plan_telat,
        .indicator-laporan_telat { background: #dc3545; }
        @media (max-width: 767px) {
            .sidebar { min-height: auto; border-right: none; border-bottom: 1px solid #dee2e6; }
            .cal-day { min-height: 50px; }
        }
    </style>
    @stack('styles')
</head>
<body>
<nav class="navbar navbar-expand-md navbar-dark bg-primary sticky-top">
    <div class="container-fluid">
        <a class="navbar-brand" href="{{ route('dashboard') }}">
            <i class="bi bi-calendar-check me-1"></i>Daily Plan
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMain">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navMain">
            <ul class="navbar-nav ms-auto align-items-md-center">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle text-white" href="#" data-bs-toggle="dropdown">
                        <i class="bi bi-person-circle me-1"></i>{{ Auth::user()->name }}
                        <span class="badge bg-light text-primary ms-1">{{ Auth::user()->roleLabel() }}</span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><span class="dropdown-item-text text-muted small">{{ Auth::user()->division?->name ?? '—' }}</span></li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button class="dropdown-item text-danger"><i class="bi bi-box-arrow-right me-1"></i>Keluar</button>
                            </form>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav>

<div class="container-fluid">
    <div class="row">
        {{-- Sidebar --}}
        <div class="col-md-2 sidebar py-3 d-none d-md-block">
            @include('shared.sidebar')
        </div>

        {{-- Konten utama --}}
        <div class="col-md-10 py-3">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle me-1"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-circle me-1"></i>{{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @yield('content')
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
@stack('scripts')
</body>
</html>
