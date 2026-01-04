<?php

namespace Tests\Feature\Pegawai;

use Tests\TestCase;
use App\Models\User;
use App\Models\Pegawai\Kategori;
use App\Models\Pegawai\Produk; // Pastikan model Produk ada
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

class KategoriControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;

    protected function setUp(): void
    {
        parent::setUp();
        // Login sebagai Admin agar bisa akses route
        $this->admin = User::create([
            'nama_user' => 'Admin',
            'username' => 'admin',
            'password' => Hash::make('password'),
            'role' => 'admin'
        ]);
    }

    // --- TEST INDEX ---
    public function test_index_displays_kategori()
    {
        // Create Data Dummy
        Kategori::create(['nama_kategori' => 'Minuman']);

        $response = $this->actingAs($this->admin)
                         ->get(route('kategori.index'));

        $response->assertStatus(200);
        $response->assertViewIs('pegawai.kategori.index');
        $response->assertViewHas('kategori');
        $response->assertSee('Minuman');
    }

    // --- TEST INDEX (EDIT MODE) ---
    public function test_index_edit_mode()
    {
        $kategori = Kategori::create(['nama_kategori' => 'Edit Me']);

        // Akses URL dengan query param ?edit=ID
        $response = $this->actingAs($this->admin)
                         ->get(route('kategori.index', ['edit' => $kategori->id_kategori])); // Sesuaikan primary key

        $response->assertStatus(200);
        $response->assertViewHas('editMode', true);
        $response->assertViewHas('kategoriEdit');
    }

    // --- TEST STORE ---
    public function test_store_creates_kategori()
    {
        $data = ['nama_kategori' => 'Snack'];

        $response = $this->actingAs($this->admin)
                         ->post(route('kategori.store'), $data);

        $response->assertRedirect(route('kategori.index'));
        $response->assertSessionHas('success');
        
        $this->assertDatabaseHas('kategori', ['nama_kategori' => 'Snack']);
    }

    // --- TEST STORE VALIDATION (DUPLICATE) ---
    public function test_store_validation_duplicate()
    {
        Kategori::create(['nama_kategori' => 'Sama']);

        $response = $this->actingAs($this->admin)
                         ->post(route('kategori.store'), ['nama_kategori' => 'Sama']);

        $response->assertSessionHasErrors('nama_kategori');
    }

    // --- TEST UPDATE ---
    public function test_update_kategori()
    {
        $kategori = Kategori::create(['nama_kategori' => 'Lama']);

        $response = $this->actingAs($this->admin)
                         ->put(route('kategori.update', $kategori->id_kategori), [
                             'nama_kategori' => 'Baru'
                         ]);

        $response->assertRedirect(route('kategori.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('kategori', ['nama_kategori' => 'Baru']);
    }

    // --- TEST DESTROY (SUCCESS) ---
    public function test_destroy_removes_kategori()
    {
        $kategori = Kategori::create(['nama_kategori' => 'Hapus Aku']);

        $response = $this->actingAs($this->admin)
                         ->delete(route('kategori.destroy', $kategori->id_kategori));

        $response->assertRedirect(route('kategori.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseMissing('kategori', ['id_kategori' => $kategori->id_kategori]);
    }

    // --- TEST DESTROY (GAGAL - DIPAKAI PRODUK) ---
    public function test_destroy_fails_if_has_product()
    {
        $kategori = Kategori::create(['nama_kategori' => 'Induk']);
        
        // Buat Produk yang memakai kategori ini
        // Asumsi kamu punya Model Produk dengan foreign key id_kategori
        // Jika pakai Factory: Produk::factory()->create(['id_kategori' => $kategori->id_kategori]);
        // Manual insert:
        Produk::create([
            'nama_produk' => 'Anak Produk',
            'id_kategori' => $kategori->id_kategori,
            'harga' => 5000,
            'stok' => 10
        ]);

        $response = $this->actingAs($this->admin)
                         ->delete(route('kategori.destroy', $kategori->id_kategori));

        // Harusnya Redirect Back dengan Error (karena catch Exception di controller)
        $response->assertRedirect(route('kategori.index'));
        $response->assertSessionHas('error'); // Controller menangkap DomainException dan return 'error'

        // Database tidak boleh hilang
        $this->assertDatabaseHas('kategori', ['id_kategori' => $kategori->id_kategori]);
    }
}