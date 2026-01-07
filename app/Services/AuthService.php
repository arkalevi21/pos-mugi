<?php

namespace App\Services;

use Illuminate\Support\Facades\Auth;

class AuthService
{
    /**
     * Coba login user.
     * Return User object jika sukses, null jika gagal.
     */
    public function attemptLogin(array $credentials, bool $remember): ?\App\Models\User
    {
        if (Auth::attempt($credentials, $remember)) {
            
            request()->session()->regenerate();

            return Auth::user();
        }

        return null;
    }

    /**
     * Logout user
     */
    public function logout(): void
    {
        Auth::logout();

        request()->session()->invalidate();
        request()->session()->regenerateToken();
    }
}