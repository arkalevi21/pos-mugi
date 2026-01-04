<?php

namespace Tests\Unit\Services;

use Tests\TestCase;
use App\Services\Pegawai\KategoriService;
use App\Repositories\Pegawai\KategoriRepository;
use App\Models\Pegawai\Kategori;
use Mockery;
use Illuminate\Database\Eloquent\Collection;

class KategoriServiceTest extends TestCase
{
    protected $kategoriRepository;
    protected $service;

    protected function setUp(): void
    {
        parent::setUp();

        // Mock Repository
        $this->kategoriRepository = Mockery::mock(KategoriRepository::class);

        // Inject Mock ke Service
        $this->service = new KategoriService($this->kategoriRepository);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    // --- TEST GET ALL ---
    public function test_get_all_categories()
    {
        $collection = new Collection();
        $this->kategoriRepository->shouldReceive('getAllWithProductCount')
            ->once()
            ->andReturn($collection);

        $result = $this->service->getAllCategories();
        $this->assertInstanceOf(Collection::class, $result);
    }

    // --- TEST GET BY ID ---
    public function test_get_category_by_id()
    {
        $mockKategori = new Kategori();
        $this->kategoriRepository->shouldReceive('readById')
            ->with(1)
            ->once()
            ->andReturn($mockKategori);

        $result = $this->service->getCategoryById(1);
        $this->assertInstanceOf(Kategori::class, $result);
    }

    // --- TEST CREATE ---
    public function test_create_category()
    {
        $data = ['nama_kategori' => 'Makanan'];
        $mockKategori = new Kategori();

        $this->kategoriRepository->shouldReceive('create')
            ->with($data)
            ->once()
            ->andReturn($mockKategori);

        $result = $this->service->createCategory($data);
        $this->assertInstanceOf(Kategori::class, $result);
    }

    // --- TEST UPDATE ---
    public function test_update_category()
    {
        $data = ['nama_kategori' => 'Baru'];
        $this->kategoriRepository->shouldReceive('update')
            ->with(1, $data)
            ->once()
            ->andReturn(true);

        $result = $this->service->updateCategory(1, $data);
        $this->assertTrue($result);
    }

    // --- TEST DELETE SUCCESS ---
    public function test_delete_category_success()
    {
        // Mock hasProducts mengembalikan FALSE (Aman dihapus)
        $this->kategoriRepository->shouldReceive('hasProducts')
            ->with(1)
            ->once()
            ->andReturn(false);

        $this->kategoriRepository->shouldReceive('destroy')
            ->with(1)
            ->once();

        $this->service->deleteCategory(1);
        
        // Assert tidak ada error
        $this->assertTrue(true);
    }

    // --- TEST DELETE FAILED (EXCEPTION) ---
    // INI YANG AKAN MENGHIJAUKAN UNCOVERED CODE KAMU
    public function test_delete_category_throws_exception_when_has_products()
    {
        $this->expectException(\DomainException::class);
        $this->expectExceptionMessage('Kategori tidak dapat dihapus karena masih digunakan oleh produk.');

        // Mock hasProducts mengembalikan TRUE (Sedang dipakai produk)
        $this->kategoriRepository->shouldReceive('hasProducts')
            ->with(1)
            ->once()
            ->andReturn(true);

        // destroy() TIDAK BOLEH dipanggil
        $this->kategoriRepository->shouldReceive('destroy')->never();

        $this->service->deleteCategory(1);
    }
}