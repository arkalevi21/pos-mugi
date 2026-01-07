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

        $user = $this->authService->attemptLogin($credentials, $request->filled('remember'));

        if ($user) {
         
            return response()->json([
                'status' => 'success',
                'message' => 'Login berhasil',
                'data' => [
                    'user' => $user,
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
        $this->authService->logout();

        return response()->json([
            'status' => 'success',
            'message' => 'Logout berhasil'
        ]);
    }
}