<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    public function handle(Request $request, Closure $next, ...$roles)
    {
        // Debug: lihat user dan roles
        // dd(auth()->user(), $roles);
        
        $user = $request->user();
        
        // Jika tidak ada user (belum login)
        if (!$user) {
            return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu.');
        }
        
        // Jika user memiliki role yang diizinkan
        if (in_array($user->role, $roles)) {
            return $next($request);
        }
        
        // Jika tidak memiliki akses
        abort(403, 'Anda tidak memiliki izin untuk mengakses halaman ini.');
    }
}