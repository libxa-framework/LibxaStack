@extends('layouts.app')

@section('title', 'Create Account — LibxaFrame')

@section('content')
<div class="auth-card">
    <div class="auth-header">
        <h1>Join LibxaFrame</h1>
        <p>Get started with our premium authentication experience.</p>
    </div>

    @if(session('error'))
        <div class="alert alert-error">
            {{ session('error') }}
        </div>
    @endif

    <form action="/register" method="POST">
        @csrf
        <div class="form-group">
            <label for="name">Full Name</label>
            <input type="text" name="name" id="name" required placeholder="John Doe">
        </div>

        <div class="form-group">
            <label for="email">Email Address</label>
            <input type="email" name="email" id="email" required placeholder="john@example.com">
        </div>

        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" name="password" id="password" required placeholder="••••••••">
        </div>

        <div class="form-group">
            <label for="password_confirmation">Confirm Password</label>
            <input type="password" name="password_confirmation" id="password_confirmation" required placeholder="••••••••">
        </div>

        <button type="submit" class="btn-primary" style="width: 100%; margin-top: 1rem;">Create Account</button>
    </form>

    <div style="margin-top: 1.5rem; text-align: center; font-size: 0.9rem; color: var(--text-muted);">
        Already have an account? <a href="/login" style="color: var(--primary); font-weight: 500;">Sign in</a>
    </div>
</div>
@endsection
