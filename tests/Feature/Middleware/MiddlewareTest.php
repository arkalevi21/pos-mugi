<?php

namespace Tests\Feature\Middleware;

use Tests\TestCase;
use App\Models\User;
use App\Http\Middleware\Authenticate;
use App\Http\Middleware\CheckRole;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;

class MiddlewareTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // --- Setup Route Dummy untuk Testing ---
        
        // 1. Route untuk ngetes Authenticate (Wajib Login)
        Route::get('/test-auth', function () {
            return 'OK';
        })->middleware('auth');

        // 2. Route untuk ngetes CheckRole (Wajib Role: Admin)
        Route::get('/test-role-admin', function () {
            return 'Admin Area';
        })->middleware([CheckRole::class . ':admin']); // Panggil class middleware langsung
    }

    // ==========================================
    // TEST 1: App\Http\Middleware\Authenticate
    // ==========================================

    public function test_authenticate_redirects_to_login_if_web_request()
    {
        // Akses route yang butuh login tanpa login
        $response = $this->get('/test-auth');

        // Assert redirect ke route 'login'
        $response->assertRedirect(route('login'));
    }

    public function test_authenticate_returns_json_error_if_json_request()
    {
        // Akses route via JSON (API style) tanpa login
        $response = $this->getJson('/test-auth');

        // Assert 401 Unauthorized (karena expectsJson() = true, redirectTo return null)
        $response->assertStatus(401);
    }

    // ==========================================
    // TEST 2: App\Http\Middleware\CheckRole
    // ==========================================

    public function test_check_role_redirects_if_user_not_logged_in()
    {
        // Akses route role tanpa login sama sekali
        // Middleware CheckRole punya logic: if (!$user) -> redirect login
        $response = $this->get('/test-role-admin');

        $response->assertRedirect(route('login'));
        $response->assertSessionHas('error', 'Silakan login terlebih dahulu.');
    }

    public function test_check_role_aborts_403_if_role_mismatch()
    {
        // Login sebagai Kasir
        $kasir = User::create([
            'nama_user' => 'Kasir',
            'username' => 'kasir',
            'password' => Hash::make('password'),
            'role' => 'kasir'
        ]);

        // Coba akses halaman Admin
        $response = $this->actingAs($kasir)
                         ->get('/test-role-admin');

        // Assert Forbidden (403)
        $response->assertStatus(403);
    }

    public function test_check_role_allows_access_if_role_matches()
    {
        // Login sebagai Admin
        $admin = User::create([
            'nama_user' => 'Admin',
            'username' => 'admin',
            'password' => Hash::make('password'),
            'role' => 'admin'
        ]);

        // Coba akses halaman Admin
        $response = $this->actingAs($admin)
                         ->get('/test-role-admin');

        // Assert OK (200)
        $response->assertStatus(200);
        $response->assertSee('Admin Area');
    }
}