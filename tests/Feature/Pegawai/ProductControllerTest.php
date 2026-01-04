<?php

namespace Tests\Feature\Pegawai;

use Tests\TestCase;
use App\Models\User;
use App\Models\Pegawai\Produk;
use App\Models\Pegawai\Kategori; // Pastikan model ini ada
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Mockery;
use App\Models\Pegawai\DetailTransaksi; // Untuk skenario gagal hapus

class ProductControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;
    protected $kategori;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::create([
            'nama_user' => 'Admin',
            'username' => 'admin',
            'password' => Hash::make('password'),
            'role' => 'admin'
        ]);

        $this->kategori = Kategori::create(['nama_kategori' => 'Minuman']);
    }

    public function test_index_displays_products()
    {
        Produk::create([
            'nama_produk' => 'Teh Manis',
            'id_kategori' => $this->kategori->id_kategori,
            'harga' => 5000,
            'stok' => 100,
            'is_active' => true
        ]);

        $response = $this->actingAs($this->admin)
            ->get(route('produk.index'));

        $response->assertStatus(200);
        $response->assertViewHas('product');
    }

    public function test_store_creates_product()
    {
        $data = [
            'nama_produk' => 'Kopi Hitam',
            'id_kategori' => $this->kategori->id_kategori,
            'harga' => 3000
        ];

        $response = $this->actingAs($this->admin)
            ->post(route('produk.store'), $data);

        $response->assertRedirect(route('produk.index'));
        $this->assertDatabaseHas('produk', ['nama_produk' => 'Kopi Hitam']);
    }

    public function test_update_modifies_product()
    {
        $produk = Produk::create([
            'nama_produk' => 'Lama',
            'id_kategori' => $this->kategori->id_kategori,
            'harga' => 5000,
            'stok' => 10,
            'is_active' => true
        ]);

        $response = $this->actingAs($this->admin)
            ->put(route('produk.update', $produk->id_produk), [
                'nama_produk' => 'Baru',
                'id_kategori' => $this->kategori->id_kategori,
                'harga' => 6000
            ]);

        $response->assertRedirect(route('produk.index'));
        $this->assertDatabaseHas('produk', ['nama_produk' => 'Baru', 'harga' => 6000]);
    }

    public function test_destroy_deletes_product()
    {
        $produk = Produk::create([
            'nama_produk' => 'Hapus',
            'id_kategori' => $this->kategori->id_kategori,
            'harga' => 5000,
            'stok' => 10,
            'is_active' => true
        ]);

        $response = $this->actingAs($this->admin)
            ->delete(route('produk.destroy', $produk->id_produk));

        $response->assertRedirect(route('produk.index'));
        $this->assertDatabaseMissing('produk', ['id_produk' => $produk->id_produk]);
    }

    // --- GANTI NAMA METHOD BIAR LEBIH RELEVAN ---
    public function test_destroy_handles_exception_when_id_not_found()
    {
        // Kita coba hapus ID ngawur (misal: 999999)
        // Ini akan memicu ModelNotFoundException dari 'findOrFail'
        // Exception tersebut akan ditangkap oleh blok 'catch' di controller

        $response = $this->actingAs($this->admin)
            ->delete(route('produk.destroy', 999999));

        // Assert: Harus redirect kembali ke index
        $response->assertRedirect(route('produk.index'));

        // Assert: Harus membawa pesan error di session
        // (Sesuai logic controller: 'Gagal menghapus produk: ...')
        $response->assertSessionHas('error');
    }
}
