<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use Libxa\Http\Request;
use Libxa\Http\Response;
use App\Models\User;
use Libxa\Atlas\DB;

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
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users',
            'password' => 'required|min:8|confirmed',
        ]);

        $user = DB::table('users')->insertGetId([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'password' => password_hash($data['password'], PASSWORD_DEFAULT),
            'created_at' => now()->format('Y-m-d H:i:s'),
            'updated_at' => now()->format('Y-m-d H:i:s'),
        ]);

        if ($user) {
            auth()->loginUsingId($user);
            return redirect('/home')->with('success', 'Registration successful!');
        }

        return back()->with('error', 'Registration failed.');
    }
}
