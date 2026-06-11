@extends('layouts.app')

@section('title', 'Create Account — LibxaFrame')

@section('no-footer', true)

@section('content')
<div class="auth-card">
    <div class="auth-header">
        <div class="auth-logo">
            <span class="logo-mark" style="width:48px;height:48px;border-radius:12px;">
                <span class="l-letter" style="font-size:24px;">L</span>
                <svg class="star" style="width:18px;height:18px;top:-6px;right:-4px;" viewBox="0 0 24 24" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                    <polygon points="12,1 14.6,7.5 21.5,7.5 16,11.8 18.1,18.5 12,14.4 5.9,18.5 8,11.8 2.5,7.5 9.4,7.5"/>
                </svg>
            </span>
        </div>
        <h1>Create your account</h1>
        <p>Join thousands of developers building with LibxaFrame.</p>
    </div>

    @if(session('error'))
        <div class="alert alert-error">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink:0;margin-top:1px;"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-error">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink:0;margin-top:1px;"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
            <div>
                @foreach ($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        </div>
    @endif

    <form action="/register" method="POST">
        @csrf
        <div class="form-group">
            <label for="name">Full name</label>
            <input type="text" name="name" id="name" required autofocus placeholder="Jane Doe" value="{{ old('name') }}">
        </div>

        <div class="form-group">
            <label for="email">Email address</label>
            <input type="email" name="email" id="email" required placeholder="you@example.com" value="{{ old('email') }}">
        </div>

        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" name="password" id="password" required placeholder="••••••••">
        </div>

        <div class="form-group">
            <label for="password_confirmation">Confirm password</label>
            <input type="password" name="password_confirmation" id="password_confirmation" required placeholder="••••••••">
        </div>

        <button type="submit" class="btn-primary submit-btn">
            Create Account
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
        </button>
    </form>

    <div class="auth-footer">
        Already have an account? <a href="/login">Sign in</a>
    </div>

    <p style="margin-top: 1.25rem; text-align: center; font-size: 0.75rem; color: var(--text-faint); line-height: 1.6;">
        By continuing, you agree to our
        <a href="#" style="color: var(--text-muted); text-decoration: underline; text-underline-offset: 2px;">Terms</a>
        and
        <a href="#" style="color: var(--text-muted); text-decoration: underline; text-underline-offset: 2px;">Privacy Policy</a>.
    </p>
</div>
@endsection
