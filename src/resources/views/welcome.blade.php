@extends('layouts.app')

@section('title', 'LibxaFrame — The Modern PHP Framework for Next-Gen Web')

@section('main-class', 'full-width')

@section('content')
<style>
    .hero {
        position: relative;
        padding: 6rem 1.5rem 4rem;
        text-align: center;
        max-width: 64rem;
        margin: 0 auto;
    }

    .hero-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.375rem 0.875rem;
        border-radius: 999px;
        background: #fffbeb;
        border: 1px solid #fde68a;
        color: #d97706;
        font-size: 0.75rem;
        font-family: 'JetBrains Mono', monospace;
        letter-spacing: 0.02em;
        margin-bottom: 2.5rem;
    }

    html.dark .hero-badge {
        background: rgba(245,158,11,.1);
        border-color: rgba(245,158,11,.25);
        color: #fcd34d;
    }

    .pulse-dot {
        width: 6px; height: 6px; border-radius: 999px;
        background: var(--primary);
        animation: pulse 2s infinite;
    }

    .hero h1 {
        font-size: clamp(2.5rem, 7vw, 5.5rem);
        font-weight: 900;
        letter-spacing: -0.04em;
        line-height: 1;
        color: var(--text);
        margin-bottom: 2rem;
    }

    .hero h1 .accent {
        position: relative;
        color: var(--primary);
        display: inline-block;
    }

    .hero p.lead {
        font-size: clamp(1.0625rem, 2vw, 1.25rem);
        color: var(--text-faint);
        max-width: 38rem;
        margin: 0 auto 3rem;
        line-height: 1.6;
    }

    .hero-actions {
        display: flex;
        flex-wrap: wrap;
        gap: 0.75rem;
        justify-content: center;
        margin-bottom: 4rem;
    }

    .btn-lg {
        padding: 0.875rem 1.75rem;
        font-size: 0.9375rem;
        border-radius: 10px;
    }

    /* Terminal */
    .terminal {
        max-width: 32rem;
        margin: 0 auto;
        text-align: left;
        background: #171717;
        border: 1px solid #262626;
        border-radius: 12px;
        padding: 1.25rem;
        box-shadow: 0 25px 50px -12px rgba(0,0,0,.25);
    }

    .terminal-dots { display: flex; gap: 6px; margin-bottom: 1rem; }
    .terminal-dots span { width: 11px; height: 11px; border-radius: 999px; }
    .terminal-dots span:nth-child(1) { background: rgba(239,68,68,.7); }
    .terminal-dots span:nth-child(2) { background: rgba(234,179,8,.7); }
    .terminal-dots span:nth-child(3) { background: rgba(34,197,94,.7); }

    .terminal pre {
        font-family: 'JetBrains Mono', monospace;
        font-size: 0.8125rem;
        line-height: 1.9;
        color: #d4d4d4;
        white-space: pre-wrap;
    }

    .terminal .cmd { color: #fbbf24; }
    .terminal .muted { color: #737373; font-size: 0.75rem; }
    .terminal .ok { color: #4ade80; font-size: 0.75rem; }

    /* ===== Sections ===== */
    .section {
        max-width: 80rem;
        margin: 0 auto;
        padding: 5rem 1.5rem;
    }

    .section-bordered {
        border-top: 1px solid var(--border);
    }

    .section-bg-subtle { background: var(--bg-subtle); }

    .eyebrow {
        font-family: 'JetBrains Mono', monospace;
        font-size: 0.75rem;
        letter-spacing: 0.15em;
        text-transform: uppercase;
        color: var(--primary);
        margin-bottom: 1rem;
    }

    .section h2 {
        font-size: clamp(1.875rem, 4vw, 2.75rem);
        font-weight: 900;
        letter-spacing: -0.03em;
        color: var(--text);
        line-height: 1.15;
    }

    /* Stats */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        border: 1px solid var(--border);
        border-radius: 12px;
        overflow: hidden;
    }

    @media (min-width: 768px) {
        .stats-grid { grid-template-columns: repeat(4, 1fr); }
    }

    .stat-cell {
        padding: 2rem 1.75rem;
        display: flex;
        align-items: center;
        gap: 1rem;
        border-right: 1px solid var(--border);
        border-bottom: 1px solid var(--border);
    }

    .stat-cell:nth-child(2n) { border-right: none; }
    @media (min-width: 768px) {
        .stat-cell:nth-child(2n) { border-right: 1px solid var(--border); }
        .stat-cell:nth-child(4n) { border-right: none; }
        .stat-cell { border-bottom: none; }
    }
    .stat-cell:nth-last-child(-n+2) { border-bottom: none; }

    .stat-icon {
        width: 40px; height: 40px; border-radius: 8px;
        background: #fffbeb;
        border: 1px solid #fef3c7;
        display: flex; align-items: center; justify-content: center;
        flex-shrink: 0;
        color: var(--primary);
    }

    html.dark .stat-icon {
        background: rgba(245,158,11,.1);
        border-color: rgba(245,158,11,.2);
    }

    .stat-value { font-size: 1.5rem; font-weight: 900; color: var(--text); letter-spacing: -0.02em; }
    .stat-label { font-size: 0.75rem; color: var(--text-faint); margin-top: 2px; }

    /* Feature cards */
    .features-grid {
        display: grid;
        grid-template-columns: 1fr;
        gap: 1.25rem;
        margin-top: 3.5rem;
    }

    @media (min-width: 768px) {
        .features-grid { grid-template-columns: repeat(3, 1fr); }
    }

    .feature-card {
        position: relative;
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: 12px;
        padding: 2rem;
        transition: border-color .2s, box-shadow .2s;
    }

    .feature-card:hover {
        border-color: rgba(245,158,11,.25);
        box-shadow: 0 8px 24px -8px rgba(245,158,11,.12);
    }

    .feature-badge {
        position: absolute;
        top: 1rem; right: 1rem;
        font-size: 0.625rem;
        font-family: 'JetBrains Mono', monospace;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.1em;
        padding: 0.25rem 0.625rem;
        border-radius: 999px;
        background: #fffbeb;
        border: 1px solid #fde68a;
        color: #d97706;
    }

    html.dark .feature-badge {
        background: rgba(245,158,11,.1);
        border-color: rgba(245,158,11,.2);
        color: #fcd34d;
    }

    .feature-icon {
        width: 44px; height: 44px; border-radius: 12px;
        background: #fffbeb;
        border: 1px solid #fde68a;
        display: flex; align-items: center; justify-content: center;
        margin-bottom: 1.5rem;
        color: var(--primary);
    }

    html.dark .feature-icon {
        background: rgba(245,158,11,.05);
        border-color: rgba(245,158,11,.3);
    }

    .feature-card h3 {
        font-size: 1.0625rem;
        font-weight: 700;
        color: var(--text);
        margin-bottom: 0.75rem;
    }

    .feature-card p { font-size: 0.875rem; line-height: 1.7; color: var(--text-faint); }

    /* CTA */
    .cta-section {
        background: var(--text);
        color: var(--bg);
        text-align: center;
        padding: 6rem 1.5rem;
    }

    html.dark .cta-section { background: var(--bg-subtle); }

    .cta-section h2 {
        color: var(--bg);
        font-size: clamp(1.875rem, 5vw, 3rem);
        font-weight: 900;
        letter-spacing: -0.03em;
        margin-bottom: 1.25rem;
    }

    html.dark .cta-section h2 { color: var(--text); }

    .cta-section p {
        color: color-mix(in srgb, var(--bg) 60%, var(--text-faint));
        font-size: 1.0625rem;
        margin-bottom: 2.5rem;
        max-width: 32rem;
        margin-left: auto;
        margin-right: auto;
        line-height: 1.6;
    }

    html.dark .cta-section p { color: var(--text-faint); }

    .btn-on-dark {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.875rem 1.75rem;
        border-radius: 10px;
        font-weight: 600;
        font-size: 0.9375rem;
        text-decoration: none;
        background: color-mix(in srgb, var(--bg) 12%, transparent);
        color: var(--bg) !important;
        border: 1px solid color-mix(in srgb, var(--bg) 16%, transparent);
        transition: background-color .15s;
    }

    html.dark .btn-on-dark { color: var(--text) !important; background: rgba(255,255,255,.06); border-color: rgba(255,255,255,.1); }
    .btn-on-dark:hover { background: color-mix(in srgb, var(--bg) 20%, transparent); }
    html.dark .btn-on-dark:hover { background: rgba(255,255,255,.1); }

    @media (min-width: 768px) {
        .auth-showcase-grid { grid-template-columns: 1fr 1fr; }
    }
</style>

<!-- ============ HERO ============ -->
<section class="hero">
    <div class="hero-badge">
        <span class="pulse-dot"></span>
        LibxaFrame 2.0 is here — read the announcement
        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
    </div>

    <h1>
        Build the web<br>
        <span class="accent">faster.</span>
    </h1>

    <p class="lead">
        A modern PHP framework engineered for speed, AI-native features, and developer joy.
        From prototype to production — in minutes.
    </p>

    <div class="hero-actions">
        @guest
            <a href="/register" class="btn-primary btn-lg">
                Get Started Free
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
            </a>
            <a href="/login" class="btn-secondary btn-lg">Sign In</a>
        @endguest
        @auth
            <a href="/home" class="btn-primary btn-lg">
                Go to Dashboard
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
            </a>
        @endauth
        <a href="https://github.com/libxa/framework" target="_blank" rel="noreferrer" class="btn-secondary btn-lg">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M12 0c-6.626 0-12 5.373-12 12 0 5.302 3.438 9.8 8.207 11.387.599.111.793-.261.793-.577v-2.234c-3.338.726-4.033-1.416-4.033-1.416-.546-1.387-1.333-1.756-1.333-1.756-1.089-.745.083-.729.083-.729 1.205.084 1.839 1.237 1.839 1.237 1.07 1.834 2.807 1.304 3.492.997.107-.775.418-1.305.762-1.604-2.665-.305-5.467-1.334-5.467-5.931 0-1.311.469-2.381 1.236-3.221-.124-.303-.535-1.524.117-3.176 0 0 1.008-.322 3.301 1.23.957-.266 1.983-.399 3.003-.404 1.02.005 2.047.138 3.006.404 2.291-1.552 3.297-1.23 3.297-1.23.653 1.653.242 2.874.118 3.176.77.84 1.235 1.911 1.235 3.221 0 4.609-2.807 5.624-5.479 5.921.43.372.823 1.102.823 2.222v3.293c0 .319.192.694.801.576 4.765-1.589 8.199-6.086 8.199-11.386 0-6.627-5.373-12-12-12z"/></svg>
            View on GitHub
            <span style="margin-left:0.25rem; padding:1px 8px; font-size:0.6875rem; font-family:'JetBrains Mono',monospace; background: var(--surface-hover); border-radius: 999px; color: var(--text-faint);">48k ★</span>
        </a>
    </div>

    <div class="terminal">
        <div class="terminal-dots"><span></span><span></span><span></span></div>
        <pre><span class="muted">$ </span><span class="cmd">composer</span> create-project libxa/app my-app
<span class="muted">→ Resolving dependencies...</span>
<span class="ok">✓ LibxaFrame 2.0.0 installed in 3.2s</span>

<span class="muted">$ </span><span class="cmd">cd</span> my-app <span class="cmd">&amp;&amp; php</span> artisan serve
<span class="ok">✓ Server running on http://localhost:8000</span></pre>
    </div>
</section>

<!-- ============ STATS ============ -->
<section class="section-bordered section-bg-subtle">
    <div class="section" style="padding-top:0; padding-bottom:0;">
        <div class="stats-grid">
            <div class="stat-cell">
                <div class="stat-icon">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                </div>
                <div>
                    <div class="stat-value">2.1M</div>
                    <div class="stat-label">Monthly downloads</div>
                </div>
            </div>
            <div class="stat-cell">
                <div class="stat-icon">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                </div>
                <div>
                    <div class="stat-value">48K</div>
                    <div class="stat-label">GitHub stars</div>
                </div>
            </div>
            <div class="stat-cell">
                <div class="stat-icon">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                </div>
                <div>
                    <div class="stat-value">3,200+</div>
                    <div class="stat-label">Contributors</div>
                </div>
            </div>
            <div class="stat-cell">
                <div class="stat-icon">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                </div>
                <div>
                    <div class="stat-value">&lt;0.8ms</div>
                    <div class="stat-label">Avg. response time</div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ============ FEATURES ============ -->
<section class="section" style="text-align:center;">
    <p class="eyebrow">Why LibxaFrame</p>
    <h2>Everything you need.<br>Nothing you don't.</h2>
    <p style="color:var(--text-faint); max-width:32rem; margin: 1.25rem auto 0;">
        Carefully crafted features that solve real problems — without the bloat.
    </p>

    <div class="features-grid" style="text-align:left;">
        <div class="feature-card">
            <span class="feature-badge">Performance</span>
            <div class="feature-icon">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/></svg>
            </div>
            <h3>Lightning Fast</h3>
            <p>Optimized routing and native Fiber support for asynchronous, non-blocking I/O — benchmarked at sub-millisecond response times under load.</p>
        </div>
        <div class="feature-card">
            <span class="feature-badge">Security</span>
            <div class="feature-icon">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
            </div>
            <h3>Secure by Default</h3>
            <p>Enterprise-grade security built-in: CSRF protection, encrypted sessions, SQL injection prevention, and automatic XSS escaping.</p>
        </div>
        <div class="feature-card">
            <span class="feature-badge">AI-Powered</span>
            <div class="feature-icon">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="4" y="4" width="16" height="16" rx="2"/><rect x="9" y="9" width="6" height="6"/><line x1="9" y1="1" x2="9" y2="4"/><line x1="15" y1="1" x2="15" y2="4"/><line x1="9" y1="20" x2="9" y2="23"/><line x1="15" y1="20" x2="15" y2="23"/><line x1="20" y1="9" x2="23" y2="9"/><line x1="20" y1="14" x2="23" y2="14"/><line x1="1" y1="9" x2="4" y2="9"/><line x1="1" y1="14" x2="4" y2="14"/></svg>
            </div>
            <h3>AI-Native ORM</h3>
            <p>The Atlas ORM understands natural language. Write queries in plain English and let the framework translate them to optimized SQL.</p>
        </div>
    </div>
</section>

<!-- ============ AUTH SHOWCASE ============ -->
<section class="section-bordered section-bg-subtle">
    <div class="section">
        <div style="display:grid; grid-template-columns:1fr; gap:3rem; align-items:center;" class="auth-showcase-grid">
            <div>
                <p class="eyebrow">Built-in auth</p>
                <h2 style="margin-bottom:1.5rem;">Multi-driver authentication,<br>ready on day one.</h2>
                <p style="color:var(--text-faint); line-height:1.7; margin-bottom:2rem;">
                    Session-based web auth, API token guards, and persistent sessions via the Atlas connection pool — all configured out of the box. No packages to install, no boilerplate to write.
                </p>
                @guest
                    <a href="/register" class="btn-primary btn-lg">
                        Create your account
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
                    </a>
                @else
                    <a href="/home" class="btn-primary btn-lg">
                        Go to Dashboard
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
                    </a>
                @endguest
            </div>

            <div class="features-grid" style="grid-template-columns:1fr; margin-top:0;">
                <div class="feature-card">
                    <div class="feature-icon">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                    </div>
                    <h3>Session Auth</h3>
                    <p>Seamless web sessions with cross-process support via the Atlas connection pool.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 2l-2 2m-7.61 7.61a5.5 5.5 0 1 1-7.778 7.778 5.5 5.5 0 0 1 7.777-7.777zm0 0L15.5 7.5m0 0l3 3L22 7l-3-3m-3.5 3.5L19 4"/></svg>
                    </div>
                    <h3>Token Guard</h3>
                    <p>Ready-to-use API authentication for your mobile and desktop clients.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="16 18 22 12 16 6"/><polyline points="8 6 2 12 8 18"/></svg>
                    </div>
                    <h3>Blade Components</h3>
                    <p>Full-featured template engine with reactive hydration for modern interfaces.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ============ CTA ============ -->
<section class="cta-section">
    <h2>Ready to build something great?</h2>
    <p>Start your next project with LibxaFrame and ship faster than ever before.</p>
    <div class="hero-actions" style="margin-bottom:0;">
        @guest
            <a href="/register" class="btn-primary btn-lg">
                Start Building
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
            </a>
        @else
            <a href="/home" class="btn-primary btn-lg">
                Go to Dashboard
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
            </a>
        @endguest
        <a href="https://github.com/libxa/framework" target="_blank" rel="noreferrer" class="btn-on-dark btn-lg">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M12 0c-6.626 0-12 5.373-12 12 0 5.302 3.438 9.8 8.207 11.387.599.111.793-.261.793-.577v-2.234c-3.338.726-4.033-1.416-4.033-1.416-.546-1.387-1.333-1.756-1.333-1.756-1.089-.745.083-.729.083-.729 1.205.084 1.839 1.237 1.839 1.237 1.07 1.834 2.807 1.304 3.492.997.107-.775.418-1.305.762-1.604-2.665-.305-5.467-1.334-5.467-5.931 0-1.311.469-2.381 1.236-3.221-.124-.303-.535-1.524.117-3.176 0 0 1.008-.322 3.301 1.23.957-.266 1.983-.399 3.003-.404 1.02.005 2.047.138 3.006.404 2.291-1.552 3.297-1.23 3.297-1.23.653 1.653.242 2.874.118 3.176.77.84 1.235 1.911 1.235 3.221 0 4.609-2.807 5.624-5.479 5.921.43.372.823 1.102.823 2.222v3.293c0 .319.192.694.801.576 4.765-1.589 8.199-6.086 8.199-11.386 0-6.627-5.373-12-12-12z"/></svg>
            Star on GitHub
        </a>
    </div>
</section>

@endsection
