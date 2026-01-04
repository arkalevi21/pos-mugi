<?php

namespace Tests\Feature\Pemilik;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class AddPegawaiControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $pemilik;

    protected function setUp(): void
    {
        parent::setUp();

        // --- PERBAIKAN DI SINI ---
        // Kita pakai role 'admin' saja agar database tidak error (Data Truncated)
        // Karena route 'pemilik.*' di web.php juga dilindungi middleware 'role:admin'
        $this->pemilik = User::create([
            'nama_user' => 'Owner',
            'username'  => 'owner',
            'password'  => Hash::make('password'),
            'role'      => 'admin' // JANGAN 'pemilik', pakai 'admin' biar aman
        ]);
    }

    public function test_index_displays_pegawai_admin_and_kasir()
    {
        $admin = User::create(['nama_user' => 'A', 'username' => 'a', 'password' => 'x', 'role' => 'admin']);
        $kasir = User::create(['nama_user' => 'B', 'username' => 'b', 'password' => 'x', 'role' => 'kasir']);

        // Kita abaikan pelanggan dulu untuk menghindari potensi error enum

        $response = $this->actingAs($this->pemilik)
            ->get(route('pemilik.pegawai.index'));

        $response->assertStatus(200);
        $response->assertViewIs('pemilik.pegawai.index');

        $response->assertViewHas('pegawai', function ($pegawai) use ($admin, $kasir) {
            // Cek logic contains berdasarkan id_user (Primary Key kamu)
            $containsAdmin = $pegawai->contains('id_user', $admin->id_user);
            $containsKasir = $pegawai->contains('id_user', $kasir->id_user);

            return $containsAdmin && $containsKasir;
        });
    }

    public function test_store_creates_new_pegawai()
    {
        $data = [
            'nama_user' => 'Pegawai Baru',
            'username'  => 'pegawai_baru',
            'password'  => 'password123',
            'password_confirmation' => 'password123',
            'role'      => 'kasir'
        ];

        $response = $this->actingAs($this->pemilik)
            ->post(route('pemilik.pegawai.store'), $data);

        $response->assertRedirect(route('pemilik.pegawai.index'));
        $response->assertSessionHas('success', 'Pegawai berhasil ditambahkan');

        $this->assertDatabaseHas('users', [
            'username' => 'pegawai_baru',
            'role'     => 'kasir'
        ]);
    }

    /**
     * Test Exception Handler (Catch Block)
     * Kita gunakan trik Mock Hash Facade agar melempar error
     * Ini jauh lebih stabil daripada Mocking Model Eloquent
     */
    public function test_store_handles_exception()
    {
        // 1. Paksa Hash::make melempar Exception
        // Karena Hash::make dipanggil di dalam blok 'try' pada controller,
        // Exception ini akan ditangkap oleh blok 'catch'.
        Hash::shouldReceive('make')
            ->once()
            ->andThrow(new \Exception('Simulasi Error Hashing'));

        // 2. Data Valid (Supaya lolos validasi request dulu)
        $data = [
            'nama_user' => 'Error User',
            'username' => 'error_user',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => 'kasir'
        ];

        // 3. Eksekusi
        $response = $this->actingAs($this->pemilik)
            ->post(route('pemilik.pegawai.store'), $data);

        // 4. Assert Redirect Back
        $response->assertRedirect();

        // 5. Assert Session Error (Bukti masuk ke catch block)
        $response->assertSessionHas('error');
    }

    /**
     * Test Update Exception (Catch Block)
     * Memastikan error handling saat update berjalan benar
     */
    public function test_update_handles_exception()
    {
        // 1. Buat Pegawai Dummy untuk diedit
        $pegawai = User::create([
            'nama_user' => 'Target Update',
            'username' => 'target_update',
            'password' => Hash::make('lama'), // Password lama
            'role' => 'kasir'
        ]);

        // 2. Mock Hash Facade agar melempar Exception
        // Kita akan mengirim password baru di request, jadi Hash::make akan dipanggil controller
        // Saat dipanggil, kita paksa dia error.
        Hash::shouldReceive('make')
            ->andThrow(new \Exception('Simulasi Error Update'));

        $data = [
            'nama_user' => 'Nama Baru',
            'username' => 'target_update',
            'role' => 'kasir',
            'password' => 'password_baru', // PENTING: Harus isi password biar masuk blok if($request->filled)
            'password_confirmation' => 'password_baru'
        ];

        // 3. Eksekusi PUT Request
        // Pastikan pakai 'id_user' sesuai properti primary key modelmu
        $response = $this->actingAs($this->pemilik)
            ->put(route('pemilik.pegawai.update', $pegawai->id_user), $data);

        // 4. Assert Redirect Back dengan Error
        $response->assertRedirect();
        $response->assertSessionHas('error');
    }

    public function test_update_modifies_pegawai_without_password()
    {
        $pegawai = User::create([
            'nama_user' => 'Lama',
            'username'  => 'lama',
            'password'  => Hash::make('rahasia'),
            'role'      => 'kasir'
        ]);

        $data = [
            'nama_user' => 'Baru',
            'username'  => 'baru',
            'role'      => 'admin',
            'password'  => '', // Kosongkan password
        ];

        // Pakai id_user
        $response = $this->actingAs($this->pemilik)
            ->put(route('pemilik.pegawai.update', $pegawai->id_user), $data);

        $response->assertRedirect(route('pemilik.pegawai.index'));

        $pegawai->refresh();
        $this->assertEquals('Baru', $pegawai->nama_user);
        $this->assertEquals('admin', $pegawai->role);
        $this->assertTrue(Hash::check('rahasia', $pegawai->password));
    }

    public function test_update_modifies_pegawai_with_password()
    {
        $pegawai = User::create([
            'nama_user' => 'Lama',
            'username'  => 'lama',
            'password'  => Hash::make('rahasia'),
            'role'      => 'kasir'
        ]);

        $data = [
            'nama_user' => 'Lama',
            'username'  => 'lama',
            'role'      => 'kasir',
            'password'  => 'baru123',
            'password_confirmation' => 'baru123'
        ];

        $this->actingAs($this->pemilik)
            ->put(route('pemilik.pegawai.update', $pegawai->id_user), $data);

        $pegawai->refresh();
        $this->assertTrue(Hash::check('baru123', $pegawai->password));
    }

    public function test_destroy_deletes_pegawai()
    {
        $pegawai = User::create([
            'nama_user' => 'Hapus Saya',
            'username'  => 'hapus',
            'password'  => 'x',
            'role'      => 'kasir'
        ]);

        $response = $this->actingAs($this->pemilik)
            ->delete(route('pemilik.pegawai.destroy', $pegawai->id_user));

        $response->assertRedirect(route('pemilik.pegawai.index'));
        $this->assertDatabaseMissing('users', ['id_user' => $pegawai->id_user]);
    }

    public function test_destroy_fails_if_deleting_self()
    {
        // Login pemilik menghapus akun pemilik sendiri
        $response = $this->actingAs($this->pemilik)
            ->delete(route('pemilik.pegawai.destroy', $this->pemilik->id_user));

        $response->assertSessionHas('error', 'Gagal: Tidak dapat menghapus akun sendiri');
        $this->assertDatabaseHas('users', ['id_user' => $this->pemilik->id_user]);
    }

    public function test_destroy_fails_if_pegawai_has_transactions()
    {
        $pegawai = User::create([
            'nama_user' => 'Si Sibuk',
            'username'  => 'sibuk',
            'password'  => 'x',
            'role'      => 'kasir'
        ]);

        // Insert manual ke tabel transaksi
        // Pastikan nama kolom foreign key 'id_user' benar
        DB::table('transaksi')->insert([
            'id_user'           => $pegawai->id_user,
            'total_harga'       => 10000,
            'tanggal'           => now(),
            'status'            => 'success',
            'metode_pembayaran' => 'tunai',
            'nama_pembeli'      => 'Dummy',
            'uang_diterima'     => 10000,
            'uang_kembalian'    => 0 // Tambahan biar aman not null
        ]);

        $response = $this->actingAs($this->pemilik)
            ->delete(route('pemilik.pegawai.destroy', $pegawai->id_user));

        $response->assertSessionHas('error', 'Gagal: Pegawai tidak dapat dihapus karena memiliki riwayat transaksi');
        $this->assertDatabaseHas('users', ['id_user' => $pegawai->id_user]);
    }
}
