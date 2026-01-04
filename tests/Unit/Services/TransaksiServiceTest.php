<?php

namespace Tests\Unit\Services;

use Tests\TestCase;
use App\Services\Pegawai\TransaksiService;
use App\Repositories\Pegawai\TransaksiRepository;
use App\Repositories\Pegawai\ProductRepository;
use App\Models\Pegawai\Transaksi;
use App\Models\Pegawai\Produk; // <--- SUDAH DIPERBAIKI (Produk)
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Mockery;

class TransaksiServiceTest extends TestCase
{
    protected $transaksiRepository;
    protected $productRepository;
    protected $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->transaksiRepository = Mockery::mock(TransaksiRepository::class);
        $this->productRepository = Mockery::mock(ProductRepository::class);

        $this->service = new TransaksiService(
            $this->transaksiRepository,
            $this->productRepository
        );
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    // --- 1. TEST GET CART DETAILS ---
    public function test_get_cart_details()
    {
        $sessionCart = [
            1 => ['qty' => 2],
        ];

        // Gunakan Model Produk (bukan Product)
        $mockProduct = new Produk();
        $mockProduct->id_produk = 1;
        $mockProduct->nama_produk = 'Kopi';
        $mockProduct->harga = 10000;

        // Mock relation kategori
        $mockProduct->setRelation('kategori', (object)['nama_kategori' => 'Minuman']);

        $this->productRepository
            ->shouldReceive('findById')
            ->with(1)
            ->andReturn($mockProduct);

        $result = $this->service->getCartDetails($sessionCart);

        $this->assertEquals(20000, $result['total']);
        $this->assertCount(1, $result['items']);
        $this->assertEquals('Kopi', $result['items'][1]['nama_product']);
    }

    // --- 2. TEST CREATE TRANSACTION (TUNAI) ---
    public function test_create_transaction_tunai()
    {
        Auth::shouldReceive('id')->andReturn(1);

        DB::shouldReceive('transaction')->andReturnUsing(function ($callback) {
            return $callback();
        });

        $data = [
            'nama_pembeli' => 'Budi',
            'metode_pembayaran' => 'tunai',
            'uang_diterima' => 50000,
            'total_harga' => 20000
        ];

        $sessionCart = [1 => ['qty' => 2]];

        $mockProduct = new Produk(); // Perbaiki nama model
        $mockProduct->id_produk = 1;
        $mockProduct->harga = 10000;

        $this->productRepository->shouldReceive('findById')->with(1)->andReturn($mockProduct);

        // Mock Transaksi
        $mockTransaksi = new Transaksi();
        // PERBAIKAN ERROR 0 vs TRX-001:
        // Kita gunakan ID Integer (1) agar tidak terkena auto-cast Laravel menjadi 0
        $mockTransaksi->id_transaksi = 1;
        $mockTransaksi->total_harga = 20000;

        $this->transaksiRepository
            ->shouldReceive('create')
            ->with(Mockery::on(function ($arg) {
                // Pastikan status success & kembalian benar
                return $arg['status'] === 'success' && $arg['uang_kembalian'] === 30000;
            }))
            ->andReturn($mockTransaksi);

        $this->transaksiRepository->shouldReceive('createDetail')->once();

        $result = $this->service->createTransaction($data, $sessionCart);

        // Assert hasilnya angka 1, bukan string
        $this->assertEquals(1, $result['transaksi']->id_transaksi);
        $this->assertNull($result['snap_token']);
    }

    // --- 3. TEST CREATE TRANSACTION (QRIS) ---
    public function test_create_transaction_qris()
    {
        $mockSnap = Mockery::mock('alias:Midtrans\Snap');
        $mockSnap->shouldReceive('getSnapToken')->andReturn('dummy-snap-token');

        Auth::shouldReceive('id')->andReturn(1);
        DB::shouldReceive('transaction')->andReturnUsing(function ($callback) {
            return $callback();
        });

        $data = [
            'nama_pembeli' => 'Siti',
            'metode_pembayaran' => 'qris',
            'uang_diterima' => 20000,
            'total_harga' => 20000
        ];
        $sessionCart = [1 => ['qty' => 2]];

        $mockProduct = new Produk(); // Perbaiki nama model
        $mockProduct->id_produk = 1;
        $mockProduct->harga = 10000;
        $this->productRepository->shouldReceive('findById')->andReturn($mockProduct);

        $mockTransaksi = new Transaksi();
        $mockTransaksi->id_transaksi = 2; // Gunakan Integer lagi
        $mockTransaksi->total_harga = 20000;
        $mockTransaksi->nama_pembeli = 'Siti';

        $this->transaksiRepository
            ->shouldReceive('create')
            ->with(Mockery::on(function ($arg) {
                return $arg['status'] === 'pending';
            }))
            ->andReturn($mockTransaksi);

        $this->transaksiRepository->shouldReceive('createDetail');

        $this->transaksiRepository
            ->shouldReceive('update')
            ->with($mockTransaksi, ['snap_token' => 'dummy-snap-token'])
            ->once();

        $result = $this->service->createTransaction($data, $sessionCart);

        $this->assertEquals('dummy-snap-token', $result['snap_token']);
    }

    // --- 4. TEST GET DETAIL ---
    public function test_get_transaction_detail()
    {
        $mockTransaksi = new Transaksi();
        $this->transaksiRepository
            ->shouldReceive('findWithDetails')
            ->with(123)
            ->andReturn($mockTransaksi);

        $result = $this->service->getTransactionDetail(123);

        $this->assertInstanceOf(Transaksi::class, $result);
    }

    // --- 5. TEST CALLBACK ---
    public function test_handle_midtrans_callback_success()
    {
        Config::set('midtrans.server_key', 'server-key-rahasia');

        $orderId = 'TRX123-123456';
        $statusCode = '200';
        $grossAmount = '10000.00';
        $serverKey = 'server-key-rahasia';
        $signatureKey = hash("sha512", $orderId . $statusCode . $grossAmount . $serverKey);

        $requestData = [
            'order_id' => $orderId,
            'status_code' => $statusCode,
            'gross_amount' => $grossAmount,
            'signature_key' => $signatureKey,
            'transaction_status' => 'settlement'
        ];

        $mockTransaksi = new Transaksi();
        $mockTransaksi->id_transaksi = 'TRX123'; // Di sini String tidak masalah karena tidak dicek equals strict int
        $mockTransaksi->total_harga = 10000;

        $this->transaksiRepository->shouldReceive('findById')->with('TRX123')->andReturn($mockTransaksi);
        $this->transaksiRepository->shouldReceive('update')->with(Mockery::type(Transaksi::class), ['status' => 'success'])->once();

        $this->service->handleMidtransCallback($requestData);
        $this->assertTrue(true);
    }

    public function test_handle_midtrans_callback_failed_or_expired()
    {
        // 1. Setup Server Key di Config
        $serverKey = 'mock-server-key';
        Config::set('midtrans.server_key', $serverKey);

        // --- PERBAIKAN LOGIC DATA ---
        $realId = 'TRX';              // ID Asli di Database
        $midtransOrderId = 'TRX-FAIL'; // ID Unik dari Midtrans (biasanya ID Asli + Timestamp/Random)

        $statusCode = '202';
        $grossAmount = '10000.00';

        // 2. Hitung Signature Key (Pakai Order ID Midtrans)
        $signatureKey = hash("sha512", $midtransOrderId . $statusCode . $grossAmount . $serverKey);

        $requestData = [
            'order_id' => $midtransOrderId, // Kirim ID yang ada embel-embelnya
            'transaction_status' => 'expire',
            'status_code' => $statusCode,
            'gross_amount' => $grossAmount,
            'signature_key' => $signatureKey
        ];

        // Mock Transaksi
        $mockTransaksi = new Transaksi();
        $mockTransaksi->id_transaksi = $realId; // ID Object harus ID Asli

        // Expectation
        // Service akan melakukan explode, jadi kita expect dia mencari $realId ('TRX')
        $this->transaksiRepository->shouldReceive('findById')
            ->with($realId) // <--- PERBAIKAN DI SINI (Jangan pakai $midtransOrderId)
            ->andReturn($mockTransaksi);

        // UPDATE HARUS TERPANGGIL
        $this->transaksiRepository->shouldReceive('update')
            ->with($mockTransaksi, ['status' => 'failed'])
            ->once();

        // Eksekusi
        $this->service->handleMidtransCallback($requestData);

        $this->assertTrue(true);
    }
}
