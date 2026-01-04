<?php

namespace Tests\Unit\Repositories;

use Tests\TestCase;
use App\Models\Pegawai\Produk;
use App\Models\Pegawai\Kategori; // Pastikan model Kategori ada/dibuat
use App\Repositories\Pegawai\ProductRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ProductRepositoryTest extends TestCase
{
    use RefreshDatabase;

    protected $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = new ProductRepository();
    }

    // Helper untuk buat dummy data
    private function createDummyProduct()
    {
        $kategori = Kategori::create(['nama_kategori' => 'Minuman']);
        
        return Produk::create([
            'nama_produk' => 'Teh Botol',
            'id_kategori' => $kategori->id_kategori,
            'harga' => 5000,
            'stok' => 10,
            'is_active' => true
        ]);
    }

    public function test_get_all_with_category()
    {
        $this->createDummyProduct();
        $result = $this->repository->getAllWithCategory();
        
        $this->assertNotEmpty($result);
        $this->assertNotNull($result->first()->kategori);
    }

    public function test_find_by_id()
    {
        $produk = $this->createDummyProduct();
        $result = $this->repository->findById($produk->id_produk);

        $this->assertEquals($produk->id_produk, $result->id_produk);
    }

    public function test_create()
    {
        $kategori = Kategori::create(['nama_kategori' => 'Snack']);
        
        $data = [
            'nama_produk' => 'Keripik',
            'id_kategori' => $kategori->id_kategori,
            'harga' => 2000,
            'stok' => 100,
            'is_active' => true
        ];

        $result = $this->repository->create($data);

        $this->assertInstanceOf(Produk::class, $result);
        $this->assertDatabaseHas('produk', ['nama_produk' => 'Keripik']);
    }

    public function test_update()
    {
        $produk = $this->createDummyProduct();
        
        $updatedData = ['nama_produk' => 'Teh Kotak'];
        $success = $this->repository->update($produk, $updatedData);

        $this->assertTrue($success);
        $this->assertDatabaseHas('produk', ['nama_produk' => 'Teh Kotak']);
    }

    // --- INI YANG AKAN MENGHIJAUKAN UNCOVERED CODE REPO ---
    public function test_delete()
    {
        $produk = $this->createDummyProduct();
        
        // Panggil method delete di repository
        $this->repository->delete($produk);

        // Pastikan hilang dari DB
        $this->assertDatabaseMissing('produk', ['id_produk' => $produk->id_produk]);
    }
}