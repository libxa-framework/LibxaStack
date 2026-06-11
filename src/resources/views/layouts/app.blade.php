<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'LibxaFrame')</title>

    @vite(['src/resources/js/app.js', 'src/resources/css/app.css'])

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&family=JetBrains+Mono:wght@400;500;700&display=swap" rel="stylesheet">

    <!-- Favicon -->
    <link rel="icon" type="image/svg+xml" href="/favicon.svg">
    <meta name="theme-color" content="#f59e0b">

    <!-- Prevent flash of wrong theme -->
    <script>
        (function () {
            var stored = localStorage.getItem('libxaframe-theme');
            var theme = stored || 'light';
            document.documentElement.classList.add(theme);
        })();
    </script>

    <style>
        :root {
            --primary: #f59e0b;
            --primary-hover: #fbbf24;
            --primary-active: #b45309;

            --bg: #ffffff;
            --bg-subtle: #fafafa;
            --surface: #ffffff;
            --surface-hover: #fafafa;
            --border: #f0f0f0;
            --border-strong: #e5e5e5;
            --text: #171717;
            --text-muted: #737373;
            --text-faint: #a3a3a3;
        }

        html.dark {
            --bg: #0a0a0a;
            --bg-subtle: #0d0d0d;
            --surface: #0d0d0d;
            --surface-hover: #141414;
            --border: rgba(255, 255, 255, 0.06);
            --border-strong: rgba(255, 255, 255, 0.1);
            --text: #ffffff;
            --text-muted: #a3a3a3;
            --text-faint: #737373;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Inter', ui-sans-serif, system-ui, sans-serif;
        }

        html {
            scroll-behavior: smooth;
            scrollbar-width: thin;
            scrollbar-color: #d4d4d4 transparent;
        }

        html.dark { scrollbar-color: #2a2a2a transparent; }

        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background-color: #d4d4d4; border-radius: 999px; transition: background-color .2s ease; }
        ::-webkit-scrollbar-thumb:hover { background-color: var(--primary); }
        html.dark ::-webkit-scrollbar-thumb { background-color: #2a2a2a; }
        html.dark ::-webkit-scrollbar-thumb:hover { background-color: var(--primary); }

        body {
            background-color: var(--bg);
            color: var(--text-muted);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            overflow-x: hidden;
            transition: background-color .15s ease, color .15s ease;
        }

        :focus-visible {
            outline: 2px solid var(--primary);
            outline-offset: 2px;
            border-radius: 6px;
        }

        a { color: inherit; }

        /* ===== Header ===== */
        header.site-header {
            position: sticky;
            top: 0;
            z-index: 50;
            border-bottom: 1px solid var(--border);
            background-color: color-mix(in srgb, var(--bg) 90%, transparent);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
        }

        nav.site-nav {
            max-width: 80rem;
            margin: 0 auto;
            padding: 0 1.5rem;
            height: 3.5rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
        }

        .logo-mark {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 28px;
            height: 28px;
            background: var(--primary);
            border-radius: 7px;
            position: relative;
            box-shadow: 0 1px 2px rgba(245,158,11,.3);
            flex-shrink: 0;
        }

        .logo-mark span.l-letter {
            color: #000;
            font-weight: 900;
            font-size: 15px;
            line-height: 1;
            font-family: Arial, sans-serif;
        }

        .logo-mark .star {
            position: absolute;
            top: -4px;
            right: -2px;
            width: 11px;
            height: 11px;
            color: #000;
            opacity: .82;
        }

        .logo-link {
            display: flex;
            align-items: center;
            gap: 0.625rem;
            text-decoration: none;
            color: var(--text);
        }

        .logo-text {
            font-weight: 700;
            font-size: 0.875rem;
            letter-spacing: -0.01em;
            color: var(--text);
        }

        .logo-version {
            font-family: 'JetBrains Mono', monospace;
            font-size: 10px;
            color: var(--primary);
            margin-left: 2px;
        }

        .nav-links { display: flex; align-items: center; gap: 0.25rem; }

        .nav-links a.nav-link {
            padding: 0.375rem 0.875rem;
            border-radius: 8px;
            text-decoration: none;
            font-size: 0.875rem;
            font-weight: 500;
            color: var(--text-muted);
            transition: color .15s, background-color .15s;
        }

        .nav-links a.nav-link:hover { color: var(--text); background-color: var(--surface-hover); }

        .nav-actions { display: flex; align-items: center; gap: 0.5rem; }

        .icon-btn {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 32px;
            height: 32px;
            border-radius: 8px;
            border: none;
            background: transparent;
            color: var(--text-faint);
            cursor: pointer;
            transition: color .15s, background-color .15s;
        }

        .icon-btn:hover { color: var(--text); background-color: var(--surface-hover); }

        .vsep { width: 1px; height: 16px; background: var(--border-strong); margin: 0 0.25rem; }

        .btn-primary {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            background: var(--primary);
            color: #000 !important;
            padding: 0.5rem 1.125rem;
            border-radius: 8px;
            font-weight: 700;
            font-size: 0.875rem;
            text-decoration: none;
            border: none;
            cursor: pointer;
            transition: background-color .15s, transform .1s;
            box-shadow: 0 1px 2px rgba(245,158,11,.25);
        }

        .btn-primary:hover { background: var(--primary-hover); }
        .btn-primary:active { background: var(--primary-active); transform: translateY(1px); }

        .btn-secondary {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            background: var(--surface);
            color: var(--text) !important;
            padding: 0.5rem 1.125rem;
            border-radius: 8px;
            font-weight: 600;
            font-size: 0.875rem;
            text-decoration: none;
            border: 1px solid var(--border-strong);
            cursor: pointer;
            transition: background-color .15s;
        }

        .btn-secondary:hover { background: var(--surface-hover); }

        /* ===== Main / Background ===== */
        main { flex: 1; display: flex; justify-content: center; align-items: center; padding: 2rem 1.5rem; position: relative; }
        main.full-width { display: block; padding: 0; }

        .bg-grid {
            position: fixed;
            inset: 0;
            pointer-events: none;
            background-image:
                linear-gradient(to right, color-mix(in srgb, var(--text) 4%, transparent) 1px, transparent 1px),
                linear-gradient(to bottom, color-mix(in srgb, var(--text) 4%, transparent) 1px, transparent 1px);
            background-size: 48px 48px;
            -webkit-mask-image: radial-gradient(ellipse 70% 60% at 50% 30%, black 40%, transparent 100%);
            mask-image: radial-gradient(ellipse 70% 60% at 50% 30%, black 40%, transparent 100%);
            z-index: -1;
        }

        /* ===== Auth card ===== */
        .auth-card {
            position: relative;
            z-index: 1;
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 16px;
            padding: 2.25rem;
            width: 100%;
            max-width: 420px;
            box-shadow: 0 1px 3px rgba(0,0,0,.04);
        }

        html.dark .auth-card { box-shadow: none; }

        .auth-header { text-align: center; margin-bottom: 2rem; }
        .auth-logo { display: flex; justify-content: center; margin-bottom: 1.25rem; }

        .auth-header h1 {
            font-size: 1.5rem;
            font-weight: 800;
            letter-spacing: -0.02em;
            color: var(--text);
            margin-bottom: 0.375rem;
        }

        .auth-header p { color: var(--text-faint); font-size: 0.875rem; }

        form .form-group { margin-bottom: 1.125rem; }

        form label {
            display: block;
            margin-bottom: 0.375rem;
            font-size: 0.75rem;
            font-weight: 600;
            color: var(--text-muted);
        }

        form input {
            width: 100%;
            background: var(--bg);
            border: 1px solid var(--border-strong);
            border-radius: 10px;
            padding: 0.7rem 0.875rem;
            color: var(--text);
            font-size: 0.875rem;
            font-family: inherit;
            transition: border-color .15s, box-shadow .15s;
        }

        html.dark form input { background: var(--surface-hover); }

        form input::placeholder { color: var(--text-faint); }

        form input:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(245,158,11,.12);
        }

        .submit-btn { width: 100%; margin-top: 0.375rem; padding: 0.75rem; }

        .auth-footer { margin-top: 1.5rem; text-align: center; font-size: 0.875rem; color: var(--text-faint); }
        .auth-footer a { color: var(--primary); font-weight: 600; text-decoration: none; }
        .auth-footer a:hover { text-decoration: underline; text-underline-offset: 3px; }

        /* ===== Alerts ===== */
        .alert {
            display: flex;
            align-items: flex-start;
            gap: 0.625rem;
            padding: 0.75rem 1rem;
            border-radius: 10px;
            margin-bottom: 1.25rem;
            font-size: 0.8125rem;
            line-height: 1.5;
            border: 1px solid;
        }

        .alert-error {
            background: rgba(239,68,68,.06);
            border-color: rgba(239,68,68,.2);
            color: #dc2626;
        }

        html.dark .alert-error {
            background: rgba(239,68,68,.1);
            border-color: rgba(239,68,68,.25);
            color: #fca5a5;
        }

        .alert-success {
            background: rgba(34,197,94,.06);
            border-color: rgba(34,197,94,.2);
            color: #16a34a;
        }

        html.dark .alert-success {
            background: rgba(34,197,94,.1);
            border-color: rgba(34,197,94,.25);
            color: #86efac;
        }

        @media (max-width: 720px) {
            .nav-links { display: none; }
        }

        /* ===== Footer ===== */
        footer.site-footer {
            border-top: 1px solid var(--border);
            background: var(--bg-subtle);
            padding: 2.5rem 1.5rem;
            margin-top: auto;
        }

        .footer-inner {
            max-width: 80rem;
            margin: 0 auto;
            display: flex;
            flex-direction: column;
            sm:flex-direction: row;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            text-align: center;
        }

        @media (min-width: 640px) {
            .footer-inner { flex-direction: row; text-align: left; }
        }

        .footer-brand { display: flex; align-items: center; gap: 0.625rem; }

        .footer-text { font-size: 0.75rem; color: var(--text-faint); }

        .footer-status {
            display: flex;
            align-items: center;
            gap: 0.375rem;
            font-size: 0.75rem;
            font-family: 'JetBrains Mono', monospace;
            color: var(--text-muted);
        }

        .status-dot {
            width: 8px; height: 8px; border-radius: 999px;
            background: #4ade80;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.4; }
        }
    </style>
</head>
<body>
    <div class="bg-grid"></div>

    <header class="site-header">
        <nav class="site-nav">
            <a href="/" class="logo-link">
                <span class="logo-mark">
                    <span class="l-letter">L</span>
                    <svg class="star" viewBox="0 0 24 24" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                        <polygon points="12,1 14.6,7.5 21.5,7.5 16,11.8 18.1,18.5 12,14.4 5.9,18.5 8,11.8 2.5,7.5 9.4,7.5"/>
                    </svg>
                </span>
                <span class="logo-text">LibxaFrame</span>
                <span class="logo-version">v2.0</span>
            </a>

            <div class="nav-links">
                @guest
                    <a href="/login" class="nav-link">Login</a>
                @endguest
                @auth
                    <a href="/home" class="nav-link">Dashboard</a>
                @endauth
            </div>

            <div class="nav-actions">
                <button type="button" class="icon-btn" id="theme-toggle" aria-label="Toggle theme">
                    <svg id="icon-sun" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="4"/><path d="M12 2v2M12 20v2M4.93 4.93l1.41 1.41M17.66 17.66l1.41 1.41M2 12h2M20 12h2M6.34 17.66l-1.41 1.41M19.07 4.93l-1.41 1.41"/></svg>
                    <svg id="icon-moon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display:none"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/></svg>
                </button>

                <div class="vsep"></div>

                @guest
                    <a href="/register" class="btn-primary">Sign Up</a>
                @endguest
                @auth
                    <form action="/logout" method="POST" style="display:inline;">
                        @csrf
                        <button type="submit" class="btn-secondary" style="background:transparent;border:none;">Logout</button>
                    </form>
                @endauth
            </div>
        </nav>
    </header>

    <main class="@yield('main-class')">
        @yield('content')
    </main>

    @hasSection('no-footer')
    @else
    <footer class="site-footer">
        <div class="footer-inner">
            <div class="footer-brand">
                <span class="logo-mark" style="width:24px;height:24px;border-radius:6px;">
                    <span class="l-letter" style="font-size:13px;">L</span>
                    <svg class="star" style="width:10px;height:10px;top:-3px;right:-2px;" viewBox="0 0 24 24" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                        <polygon points="12,1 14.6,7.5 21.5,7.5 16,11.8 18.1,18.5 12,14.4 5.9,18.5 8,11.8 2.5,7.5 9.4,7.5"/>
                    </svg>
                </span>
                <span class="footer-text">© {{ date('Y') }} LibxaFrame. All rights reserved.</span>
            </div>
            <div class="footer-status">
                <span class="status-dot"></span>
                All systems operational
            </div>
        </div>
    </footer>
    @endif

    <script>
        (function () {
            var root = document.documentElement;
            var btn = document.getElementById('theme-toggle');
            var sun = document.getElementById('icon-sun');
            var moon = document.getElementById('icon-moon');

            function syncIcons() {
                var isDark = root.classList.contains('dark');
                sun.style.display = isDark ? 'block' : 'none';
                moon.style.display = isDark ? 'none' : 'block';
            }

            syncIcons();

            btn.addEventListener('click', function () {
                var isDark = root.classList.toggle('dark');
                root.classList.toggle('light', !isDark);
                localStorage.setItem('libxaframe-theme', isDark ? 'dark' : 'light');
                syncIcons();
            });
        })();
    </script>
</body>
</html>
