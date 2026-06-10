<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use Libxa\Http\Request;
use Libxa\Http\Response;
use App\Models\User;

/**
 * Registration Controller
 */
class RegisterController
{
    /**
     * Show registration form.
     */
    public function show(): Response
    {
        return view('auth.register');
    }

    /**
     * Handle registration request.
     */
    public function store(Request $request): Response
    {
        $data = $request->validate([
            'name'                  => 'required|string|max:255',
            'email'                 => 'required|email|unique:users,email',
            'password'              => 'required|min:8|confirmed',
            'password_confirmation' => 'required',
        ]);

        // Create user via Atlas ORM (password is auto-hashed via mutator)
        $user = User::create([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'password' => $data['password'],
        ]);

        if ($user->getKey()) {
            auth()->loginUsingId($user->getKey());
            return redirect('/home')->with('success', 'Welcome to LibxaFrame!');
        }

        return back()->with('error', 'Registration failed. Please try again.');
    }
}
