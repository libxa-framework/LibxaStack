<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use Libxa\Http\Request;
use Libxa\Http\Response;

/**
 * Login Controller
 */
class LoginController
{
    /**
     * Show login form.
     */
    public function show(): Response
    {
        return view('auth.login');
    }

    /**
     * Handle login request.
     */
    public function login(Request $request): Response
    {
        $credentials = $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        if (auth()->attempt($credentials)) {
            $request->session()->regenerate();
            return redirect('/home');
        }

        return back()->with('error', 'Invalid login credentials.');
    }

    /**
     * Logout user.
     */
    public function logout(Request $request): Response
    {
        auth()->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }
}
