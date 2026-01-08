<?php

namespace App\Services;

use Illuminate\Support\Facades\Auth;

class AuthService
{
    public function attemptLogin(array $credentials, bool $remember): ?\App\Models\User
    {
        // Auth::attempt tetap bisa dipake buat cek password
        if (Auth::attempt($credentials, $remember)) {
            // HAPUS baris session()->regenerate();
            return Auth::user();
        }

        return null;
    }

    public function logout(): void
    {
        // Untuk API token, logout itu biasanya hapus token di database
        // Auth::user()->currentAccessToken()->delete(); 
        // Tapi logic itu sebaiknya di Controller atau di sini.
        // HAPUS baris session()->invalidate() dll.
    }
}
