@extends('layouts.app')

@section('title', 'Login — LibxaFrame')

@section('content')
<div class="auth-card">
    <div class="auth-header">
        <h1>Welcome Back</h1>
        <p>Login to your account to continue.</p>
    </div>

    @if(session('error'))
        <div class="alert alert-error">
            {{ session('error') }}
        </div>
    @endif

    <form action="/login" method="POST">
        @csrf
        <div class="form-group">
            <label for="email">Email Address</label>
            <input type="email" name="email" id="email" required autofocus placeholder="name@company.com">
        </div>

        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" name="password" id="password" required placeholder="••••••••">
        </div>

        <button type="submit" class="btn-primary" style="width: 100%; margin-top: 1rem;">Sign In</button>
    </form>

    <div style="margin-top: 1.5rem; text-align: center; font-size: 0.9rem; color: var(--text-muted);">
        Don't have an account? <a href="/register" style="color: var(--primary); font-weight: 500;">Create one</a>
    </div>
</div>
@endsection
