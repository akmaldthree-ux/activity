<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Daily Plan') — {{ config('app.name') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        :root {
            --n-primary: #0075de;
            --n-primary-active: #005bab;
            --n-canvas-soft: #f6f5f4;
            --n-surface: #ffffff;
            --n-ink: #000000;
            --n-ink-2: #31302e;
            --n-ink-muted: #615d59;
            --n-ink-faint: #a39e98;
            --n-hairline: #e6e6e6;
            --n-r-xs: 4px;
            --n-r-md: 8px;
            --n-r-xl: 16px;
            --n-r-full: 9999px;
            --n-shadow:
                0 0.175px 1.041px rgba(0,0,0,.01),
                0 0.8px   2.925px rgba(0,0,0,.02),
                0 2.025px 7.847px rgba(0,0,0,.027),
                0 4px     18px    rgba(0,0,0,.04);
        }
        * { box-sizing: border-box; }
        body {
            font-family: 'Inter', -apple-system, system-ui, 'Segoe UI', Helvetica, Arial, sans-serif;
            background: linear-gradient(145deg, #0f1b5c 0%, #213183 35%, #1a5199 65%, #0075de 100%);
            color: var(--n-ink);
            font-size: 14px;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 24px 16px;
            position: relative;
            overflow-x: hidden;
        }
        /* Subtle radial glow — echoes the Notion "night" hero sticker constellation */
        body::before {
            content: '';
            position: fixed;
            top: -20%; left: 50%;
            transform: translateX(-50%);
            width: 70vw; height: 70vw;
            max-width: 700px; max-height: 700px;
            background: radial-gradient(ellipse, rgba(0,117,222,.35) 0%, transparent 70%);
            pointer-events: none;
        }
        body::after {
            content: '';
            position: fixed;
            bottom: -15%; right: 5%;
            width: 40vw; height: 40vw;
            max-width: 400px; max-height: 400px;
            background: radial-gradient(ellipse, rgba(214,182,246,.12) 0%, transparent 70%);
            pointer-events: none;
        }
        .g-brand {
            display: flex; align-items: center; gap: 10px;
            margin-bottom: 28px; text-decoration: none; color: #fff;
            position: relative; z-index: 1;
        }
        .g-brand-icon {
            width: 36px; height: 36px; border-radius: 8px;
            background: rgba(255,255,255,.2);
            border: 1px solid rgba(255,255,255,.25);
            display: flex; align-items: center; justify-content: center;
            color: #fff; font-size: 18px;
            backdrop-filter: blur(4px);
        }
        .g-brand-name {
            font-size: 20px; font-weight: 700; letter-spacing: -0.25px; color: #fff;
        }
        .g-card {
            background: var(--n-surface);
            border: 1px solid var(--n-hairline);
            border-radius: var(--n-r-xl);
            box-shadow: var(--n-shadow);
            padding: 32px;
            width: 100%;
            max-width: 440px;
        }
        .g-card-wide { max-width: 520px; }
        .g-card h4 { font-size: 22px; font-weight: 700; letter-spacing: -0.25px; color: var(--n-ink); }
        .g-hint { font-size: 13px; color: var(--n-ink-muted); }

        /* Form controls */
        .form-label { font-size: 13px; font-weight: 600; color: var(--n-ink-2); margin-bottom: 5px; }
        .form-control, .form-select {
            border-color: var(--n-hairline); border-radius: var(--n-r-xs) !important;
            font-size: 14px; color: var(--n-ink); background: var(--n-surface);
        }
        .form-control:focus, .form-select:focus {
            border-color: var(--n-primary); box-shadow: 0 0 0 3px rgba(0,117,222,.1);
        }
        .input-group-text {
            background: var(--n-canvas-soft); border-color: var(--n-hairline);
            color: var(--n-ink-muted); font-size: 14px;
        }
        .btn-primary {
            background: var(--n-primary) !important; border-color: var(--n-primary) !important;
            color: #fff !important; border-radius: var(--n-r-full) !important;
            font-family: 'Inter', sans-serif; font-size: 15px; font-weight: 500;
        }
        .btn-primary:hover { background: var(--n-primary-active) !important; border-color: var(--n-primary-active) !important; }
        .btn-lg { padding: 10px 24px; }
        .alert {
            border-radius: var(--n-r-md) !important; font-size: 14px;
        }
        .alert-danger  { background: #fff5f5 !important; border-color: #f5c6cb !important; color: #721c24 !important; }
        .alert-success { background: #f0faf3 !important; border-color: #c3e6cb !important; color: #155724 !important; }
        a { color: var(--n-primary); }
        a:hover { color: var(--n-primary-active); }
        .g-footer-note { font-size: 12px; color: var(--n-ink-faint); text-align: center; margin-top: 20px; }
    </style>
</head>
<body>
    <a href="/" class="g-brand">
        <span class="g-brand-icon"><i class="bi bi-calendar-check"></i></span>
        <span class="g-brand-name">Daily Plan</span>
    </a>
    @yield('content')
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>
