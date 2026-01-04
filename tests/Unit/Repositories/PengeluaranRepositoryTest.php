<?php

namespace Tests\Unit\Repositories;

use Tests\TestCase;
use App\Repositories\Pegawai\PengeluaranRepository;
use App\Models\Pegawai\Pengeluaran;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Carbon\Carbon;

class PengeluaranRepositoryTest extends TestCase
{
    use RefreshDatabase;

    protected $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = new PengeluaranRepository();
    }

    public function test_create_pengeluaran()
    {
        $data = [
            'nama_pengeluaran' => 'Beli ATK',
            'nominal' => 50000,
            'tanggal' => now()->format('Y-m-d'),
            'keterangan' => 'Pena dan Kertas'
        ];

        $result = $this->repository->create($data);

        $this->assertInstanceOf(Pengeluaran::class, $result);
        $this->assertDatabaseHas('pengeluaran', ['nama_pengeluaran' => 'Beli ATK']);
    }

    public function test_find_by_id()
    {
        $pengeluaran = Pengeluaran::create([
            'nama_pengeluaran' => 'Tes ID',
            'nominal' => 10000,
            'tanggal' => now()
        ]);

        $result = $this->repository->findById($pengeluaran->id_pengeluaran);
        $this->assertEquals($pengeluaran->id_pengeluaran, $result->id_pengeluaran);
    }

    public function test_get_filtered()
    {
        // Data Hari Ini
        Pengeluaran::create(['nama_pengeluaran' => 'Hari Ini', 'nominal' => 10, 'tanggal' => Carbon::today()]);
        // Data Kemarin
        Pengeluaran::create(['nama_pengeluaran' => 'Kemarin', 'nominal' => 10, 'tanggal' => Carbon::yesterday()]);

        // Filter Hari Ini
        $result = $this->repository->getFiltered(['tanggal' => Carbon::today()->format('Y-m-d')]);

        $this->assertCount(1, $result);
        $this->assertEquals('Hari Ini', $result->first()->nama_pengeluaran);
    }

    public function test_update_success()
    {
        $pengeluaran = Pengeluaran::create([
            'nama_pengeluaran' => 'Lama',
            'nominal' => 10000,
            'tanggal' => now()
        ]);

        $success = $this->repository->update($pengeluaran->id_pengeluaran, ['nama_pengeluaran' => 'Baru']);

        $this->assertTrue($success);
        $this->assertDatabaseHas('pengeluaran', ['nama_pengeluaran' => 'Baru']);
    }

    // --- COVER IF ELSE (Return False) ---
    public function test_update_returns_false_if_not_found()
    {
        // Update ID ngawur (999)
        $success = $this->repository->update(999, ['nama_pengeluaran' => 'Ghaib']);

        $this->assertFalse($success);
    }

    public function test_delete()
    {
        $pengeluaran = Pengeluaran::create([
            'nama_pengeluaran' => 'Hapus',
            'nominal' => 10000,
            'tanggal' => now()
        ]);

        $this->repository->delete($pengeluaran->id_pengeluaran);

        $this->assertDatabaseMissing('pengeluaran', ['id_pengeluaran' => $pengeluaran->id_pengeluaran]);
    }
}