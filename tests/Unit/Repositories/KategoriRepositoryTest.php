<?php

namespace Tests\Unit\Repositories;

use Tests\TestCase;
use App\Models\Pegawai\Kategori;
use App\Repositories\Pegawai\KategoriRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;

class KategoriRepositoryTest extends TestCase
{
    use RefreshDatabase;

    protected $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = new KategoriRepository();
    }

    // --- COVERAGE: readAll (return Kategori::all()) ---
    public function test_read_all()
    {
        // 1. Buat Dummy Data
        Kategori::create(['nama_kategori' => 'Makanan']);
        Kategori::create(['nama_kategori' => 'Minuman']);

        // 2. Panggil Method readAll
        $result = $this->repository->readAll();

        // 3. Assert
        $this->assertCount(2, $result);
        $this->assertEquals('Makanan', $result->first()->nama_kategori);
    }

    // --- COVERAGE: update (return false) ---
    public function test_update_returns_false_if_not_found()
    {
        // 1. Coba update ID yang ngawur (99999)
        $result = $this->repository->update(99999, ['nama_kategori' => 'Ghaib']);

        // 2. Assert harus false (masuk ke blok return false)
        $this->assertFalse($result);
    }

    // --- Opsional: Test Update Berhasil (Biar lengkap sekalian) ---
    public function test_update_success()
    {
        $kategori = Kategori::create(['nama_kategori' => 'Lama']);
        
        $result = $this->repository->update($kategori->id_kategori, ['nama_kategori' => 'Baru']);

        $this->assertTrue($result);
        $this->assertDatabaseHas('kategori', ['nama_kategori' => 'Baru']);
    }
}