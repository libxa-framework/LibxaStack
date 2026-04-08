@extends('layouts.app')

@section('title', 'LibxaFrame — Modern PHP Framework')

@section('content')
<div style="text-align: center; max-width: 800px;">
    <h1 style="font-size: 4rem; margin-bottom: 1.5rem; letter-spacing: -0.02em;">
        The Next Generation <br>
        <span style="background: linear-gradient(to right, #818cf8, #c084fc); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">Auth Experience</span>
    </h1>
    
    <p style="font-size: 1.25rem; color: var(--text-muted); margin-bottom: 3rem; line-height: 1.6;">
       sss11 Welcome to your new LibxaFrame application. We've just initialized a powerful, multi-driver 
        authentication system with persistent sessions and a premium UI component library.
    </p>
  
    <div style="display: flex; gap: 1.5rem; justify-content: center;">
        @guest
            <a href="/register" class="btn-primary" style="padding: 1rem 2rem; font-size: 1.1rem;">Get Started</a>
            <a href="/login" style="padding: 1rem 2rem; font-size: 1.1rem; color: var(--text); text-decoration: none; border: 1px solid var(--border); border-radius: 8px;">Sign In</a>
        @endguest
        @auth
            <a href="/home" class="btn-primary" style="padding: 1rem 2rem; font-size: 1.1rem;">Go to Dashboard</a>
        @endauth
    </div>

    <div style="margin-top: 5rem; display: grid; grid-template-columns: repeat(3, 1fr); gap: 2rem; text-align: left;">
        <div style="background: var(--card-bg); padding: 2rem; border-radius: 16px; border: 1px solid var(--border);">
            <h3 style="margin-bottom: 1rem;">Session Auth</h3>
            <p style="color: var(--text-muted); font-size: 0.95rem;">Seamless web sessions with cross-process support via the Atlas connection pool.</p>
        </div>
        <div style="background: var(--card-bg); padding: 2rem; border-radius: 16px; border: 1px solid var(--border);">
            <h3 style="margin-bottom: 1rem;">Token Guard</h3>
            <p style="color: var(--text-muted); font-size: 0.95rem;">Ready-to-use API authentication for your mobile and desktop clients.</p>
        </div>
        <div style="background: var(--card-bg); padding: 2rem; border-radius: 16px; border: 1px solid var(--border);">
            <h3 style="margin-bottom: 1rem;">Blade Components</h3>
            <p style="color: var(--text-muted); font-size: 0.95rem;">Full-featured template engine with reactive hydration for modern interfaces.</p>
        </div>
    </div>
</div>
@endsection
