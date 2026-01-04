<?php

namespace Tests\Unit\Repositories;

use Tests\TestCase;
use App\Models\User;
use App\Models\Pegawai\Transaksi;
use App\Models\Pegawai\Kategori;
use App\Models\Pegawai\Produk;
use App\Models\Pegawai\DetailTransaksi;
use App\Repositories\Pegawai\TransaksiRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

class TransaksiRepositoryTest extends TestCase
{
    use RefreshDatabase;

    protected $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = new TransaksiRepository();
    }

    // --- Helper Data Dummy ---
    private function createCompleteTransaction()
    {
        $user = User::create([
            'nama_user' => 'Kasir',
            'username' => 'kasir',
            'password' => Hash::make('x'),
            'role' => 'kasir'
        ]);

        $kategori = Kategori::create(['nama_kategori' => 'Minuman']);

        $produk = Produk::create([
            'nama_produk' => 'Kopi',
            'id_kategori' => $kategori->id_kategori,
            'harga' => 5000,
            'stok' => 10,
            'is_active' => true
        ]);

        $trx = Transaksi::create([
            'id_user' => $user->id_user,
            'nama_pembeli' => 'Ali',
            'total_harga' => 10000,
            'tanggal' => now(),
            'status' => 'success',
            'metode_pembayaran' => 'tunai',
            'uang_diterima' => 10000,
            'uang_kembalian' => 0
        ]);

        DetailTransaksi::create([
            'id_transaksi' => $trx->id_transaksi,
            'id_produk' => $produk->id_produk,
            'qty' => 2,
            'harga_satuan' => 5000,
            'subtotal' => 10000
        ]);

        return $trx;
    }

    // --- Test Existing (Biarkan atau sesuaikan) ---
    public function test_get_by_date()
    {
        $this->createCompleteTransaction();
        $result = $this->repository->getByDate(now()->format('Y-m-d'));
        $this->assertNotEmpty($result);
    }

    public function test_get_total_revenue()
    {
        $this->createCompleteTransaction();
        $total = $this->repository->getTotalRevenueByDate(now()->format('Y-m-d'));
        $this->assertEquals(10000, $total);
    }

    public function test_find_by_id()
    {
        $trx = $this->createCompleteTransaction();
        $result = $this->repository->findById($trx->id_transaksi);
        $this->assertNotNull($result);
    }

    public function test_update()
    {
        $trx = $this->createCompleteTransaction();
        $this->repository->update($trx, ['status' => 'pending']);
        $this->assertDatabaseHas('transaksi', ['id_transaksi' => $trx->id_transaksi, 'status' => 'pending']);
    }

    // --- TARGET UTAMA: MENGHIJAUKAN findWithDetails ---
    public function test_find_with_details_loads_relations()
    {
        $trx = $this->createCompleteTransaction();

        // Panggil method repository
        $result = $this->repository->findWithDetails($trx->id_transaksi);

        // Assert Transaksi ditemukan
        $this->assertNotNull($result);
        $this->assertEquals($trx->id_transaksi, $result->id_transaksi);

        // Assert Relations Loaded (User & Detail)
        $this->assertNotNull($result->user);
        $this->assertNotEmpty($result->detailTransaksi);

        // Assert Nested Relations (Detail -> Produk -> Kategori)
        $detail = $result->detailTransaksi->first();
        $this->assertNotNull($detail->produk);
        $this->assertNotNull($detail->produk->kategori);

        // Cek datanya benar
        $this->assertEquals('Kopi', $detail->produk->nama_produk);
        $this->assertEquals('Minuman', $detail->produk->kategori->nama_kategori);
    }

    // --- TAMBAHAN UNTUK COVERAGE createDetail ---
    public function test_create_detail_directly()
    {
        // 1. Siapkan Parent Data (Transaksi & Produk)
        // Kita pakai helper createCompleteTransaction yang sudah ada di file ini
        $trx = $this->createCompleteTransaction();

        // Ambil ID produk yang sudah ada dari helper
        $existingProdukId = $trx->detailTransaksi->first()->id_produk;

        // 2. Data Detail Baru
        $dataDetail = [
            'id_transaksi' => $trx->id_transaksi,
            'id_produk' => $existingProdukId,
            'qty' => 10,
            'harga_satuan' => 500,
            'subtotal' => 5000
        ];

        // 3. Panggil Method Repository yang Uncovered
        $result = $this->repository->createDetail($dataDetail);

        // 4. Assert
        $this->assertInstanceOf(DetailTransaksi::class, $result);
        $this->assertDatabaseHas('detail_transaksi', [
            'id_transaksi' => $trx->id_transaksi,
            'qty' => 10,
            'subtotal' => 5000
        ]);
    }
}
