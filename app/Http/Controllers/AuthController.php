<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Services\AuthService; 
use Illuminate\Http\Request;

class AuthController extends Controller
{
    protected $authService;

    // 1. Inject AuthService via Constructor
    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(LoginRequest $request)
    {
        $credentials = $request->only('username', 'password');

       
        $user = $this->authService->attemptLogin(
            $credentials,
            $request->filled('remember')
        );

        
        if ($user) {
           
            if ($user->isAdmin()) {
                return redirect()->intended(route('pemilik.pegawai.index'));
            }

            return redirect()->intended(route('transaksi.create'));
        }

        // Jika gagal
        return back()->withErrors([
            'username' => 'Username atau password salah.',
        ])->onlyInput('username');
    }

    public function logout(Request $request)
    {
        
        $this->authService->logout();

        return redirect('/login');
    }
}