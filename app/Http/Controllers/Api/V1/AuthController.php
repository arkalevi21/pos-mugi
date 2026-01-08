<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Services\AuthService;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    protected $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    public function login(LoginRequest $request)
    {
        $credentials = $request->only('username', 'password');

        // Panggil service yang sudah diperbaiki di atas
        $user = $this->authService->attemptLogin($credentials, $request->filled('remember'));

        if ($user) {
            // GENERATE TOKEN SANCTUM DISINI
            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'status' => 'success',
                'message' => 'Login berhasil',
                'data' => [
                    'user' => $user,
                    'token' => $token, // <-- PENTING: Kirim token ke client
                    'redirect_target' => $user->isAdmin() ? 'admin_dashboard' : 'transaksi_page'
                ]
            ], 200);
        }

        return response()->json([
            'status' => 'error',
            'message' => 'Username atau password salah.'
        ], 401);
    }

    public function logout(Request $request)
    {
        // Hapus token yang sedang dipakai saat ini
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Logout berhasil'
        ]);
    }
}
