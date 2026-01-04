<?php

namespace Tests\Unit\Services;

use Tests\TestCase;
use App\Services\Pegawai\ProductService;
use App\Repositories\Pegawai\ProductRepository;
use App\Models\Pegawai\Produk;
use Mockery;
use Illuminate\Database\Eloquent\Collection;

class ProductServiceTest extends TestCase
{
    protected $productRepository;
    protected $service;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Mock Repository agar tidak perlu koneksi DB
        $this->productRepository = Mockery::mock(ProductRepository::class);
        
        // Inject Mock ke Service
        $this->service = new ProductService($this->productRepository);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_get_all_products()
    {
        $collection = new Collection();
        $this->productRepository->shouldReceive('getAllWithCategory')->once()->andReturn($collection);
        
        $result = $this->service->getAllProducts();
        $this->assertInstanceOf(Collection::class, $result);
    }

    public function test_get_product_by_id()
    {
        $mockProduct = new Produk();
        $this->productRepository->shouldReceive('findById')->with(1)->once()->andReturn($mockProduct);
        
        $result = $this->service->getProductById(1);
        $this->assertInstanceOf(Produk::class, $result);
    }

    // --- COVER CREATE (Memastikan logic default value stok & is_active jalan) ---
    public function test_create_product_sets_default_values()
    {
        $inputData = [
            'nama_produk' => 'Kopi',
            'harga' => 5000
        ];

        // Kita cek apakah data yang dikirim ke repository sudah ditambah stok=0 & is_active=true
        $this->productRepository
            ->shouldReceive('create')
            ->with(Mockery::on(function ($data) {
                return $data['stok'] === 0 && $data['is_active'] === true;
            }))
            ->once()
            ->andReturn(new Produk());

        $this->service->createProduct($inputData);
        $this->assertTrue(true); // Assert pass jika mock called
    }

    // --- COVER UPDATE SUCCESS ---
    public function test_update_product_success()
    {
        $mockProduct = new Produk();
        $data = ['nama_produk' => 'Baru'];

        $this->productRepository->shouldReceive('findById')->with(1)->andReturn($mockProduct);
        $this->productRepository->shouldReceive('update')->with($mockProduct, $data)->andReturn(true);

        $result = $this->service->updateProduct(1, $data);
        $this->assertTrue($result);
    }

    // --- COVER UPDATE FAILED (Cover if !$produk return false) ---
    public function test_update_product_returns_false_if_not_found()
    {
        // Mock repo findById mengembalikan NULL
        $this->productRepository->shouldReceive('findById')->with(999)->andReturn(null);
        
        // Repo update TIDAK BOLEH dipanggil
        $this->productRepository->shouldReceive('update')->never();

        $result = $this->service->updateProduct(999, []);
        
        // Harus return false
        $this->assertFalse($result);
    }

    // --- COVER DELETE SUCCESS ---
    public function test_delete_product_success()
    {
        $mockProduct = new Produk();
        
        $this->productRepository->shouldReceive('findById')->with(1)->andReturn($mockProduct);
        
        // Pastikan repo->delete dipanggil
        $this->productRepository->shouldReceive('delete')->with($mockProduct)->once();

        $this->service->deleteProduct(1);
        $this->assertTrue(true);
    }

    // --- COVER DELETE NOT FOUND (Cover if $produk block skipped) ---
    public function test_delete_product_does_nothing_if_not_found()
    {
        // Mock return null
        $this->productRepository->shouldReceive('findById')->with(999)->andReturn(null);
        
        // Repo delete TIDAK BOLEH dipanggil
        $this->productRepository->shouldReceive('delete')->never();

        $this->service->deleteProduct(999);
        $this->assertTrue(true);
    }
}