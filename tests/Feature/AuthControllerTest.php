<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class AuthControllerTest extends TestCase
{
    use RefreshDatabase;

    // --- TEST HALAMAN LOGIN ---
    public function test_login_page_is_accessible()
    {
        $response = $this->get(route('login'));
        $response->assertStatus(200);
        $response->assertViewIs('auth.login');
    }

    // --- TEST LOGIN SUCCESS (ADMIN) ---
    public function test_login_success_admin_redirects_to_pegawai()
    {
        $user = User::create([
            'nama_user' => 'Admin',
            'username' => 'admin',
            'password' => Hash::make('password'),
            'role' => 'admin'
        ]);

        $response = $this->post(route('login'), [
            'username' => 'admin',
            'password' => 'password'
        ]);

        $response->assertRedirect(route('pemilik.pegawai.index'));
        $this->assertAuthenticatedAs($user);
    }

    // --- TEST LOGIN SUCCESS (KASIR) ---
    public function test_login_success_kasir_redirects_to_transaksi()
    {
        $user = User::create([
            'nama_user' => 'Kasir',
            'username' => 'kasir',
            'password' => Hash::make('password'),
            'role' => 'kasir'
        ]);

        $response = $this->post(route('login'), [
            'username' => 'kasir',
            'password' => 'password'
        ]);

        $response->assertRedirect(route('transaksi.create'));
        $this->assertAuthenticatedAs($user);
    }

    public function test_login_failed_invalid_credentials()
    {
        // 1. Buat User
        $user = User::create([
            'nama_user' => 'User',
            'username' => 'user',
            'password' => Hash::make('password'),
            'role' => 'kasir'
        ]);

        // 2. Action Login Salah
        // Gunakan password agak panjang untuk melewati validasi request
        $response = $this->from(route('login'))
            ->post(route('login'), [
                'username' => 'user',
                'password' => 'password_salah_yang_panjang',
            ]);

        // 3. Assert
        $response->assertRedirect(route('login'));
        $response->assertSessionHasErrors('username');
        $this->assertGuest();
    }

    // --- TEST LOGOUT (INI YANG MENGHIJAUKAN UNCOVERED CODE) ---
    public function test_logout_success()
    {
        // 1. Buat User
        $user = User::create([
            'nama_user' => 'User Logout',
            'username' => 'logout_user',
            'password' => Hash::make('password'),
            'role' => 'kasir'
        ]);

        // 2. Login & Panggil Route Logout
        // actingAs mensimulasikan user sedang login
        $response = $this->actingAs($user)
            ->post(route('logout'));

        // 3. Assert Redirect ke Login
        $response->assertRedirect('/login');

        // 4. Assert Session Bersih & User Guest
        $this->assertGuest();
    }
}
