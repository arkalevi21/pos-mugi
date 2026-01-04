<?php

namespace Tests\Feature\Pemilik;

use Tests\TestCase;
use App\Models\User;
use App\Models\Pegawai\Transaksi;
use App\Models\Pegawai\Pengeluaran;
use App\Models\Pegawai\Produk;
use App\Models\Pegawai\DetailTransaksi;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class LaporanControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->admin = User::create([
            'nama_user' => 'Admin Owner',
            'username' => 'admin',
            'password' => Hash::make('password'),
            'role' => 'admin'
        ]);
    }

    private function createTransaction($date, $method, $total, $qty, $productName)
    {
        $produk = Produk::create([
            'nama_produk' => $productName,
            'id_kategori' => 1,
            'harga' => $total / $qty,
            'stok' => 100
        ]);

        $trx = Transaksi::create([
            'id_user' => $this->admin->id_user, // Pastikan id_user sesuai PK model User kamu
            'nama_pembeli' => 'Guest',
            'metode_pembayaran' => $method,
            'uang_diterima' => $total,
            'uang_kembalian' => 0,
            'total_harga' => $total,
            'tanggal' => $date,
            'status' => 'success'
        ]);

        DetailTransaksi::create([
            'id_transaksi' => $trx->id_transaksi,
            'id_produk' => $produk->id_produk,
            'qty' => $qty,
            'harga_satuan' => $produk->harga,
            'subtotal' => $total
        ]);

        return $trx;
    }

    public function test_index_loads_with_calculations()
    {
        // Setup Kategori Dummy
        DB::table('kategori')->insert(['id_kategori' => 1, 'nama_kategori' => 'Umum']);

        // 1. Transaksi HARI INI
        $this->createTransaction(Carbon::now(), 'tunai', 100000, 2, 'Kopi');

        // 2. Transaksi BULAN LALU (Seharusnya TIDAK MUNCUL)
        $this->createTransaction(Carbon::now()->subMonth(), 'qris', 50000, 1, 'Teh');

        // 3. Pengeluaran HARI INI
        // --- PERBAIKAN DI SINI (Ganti judul_pengeluaran jadi nama_pengeluaran) ---
        Pengeluaran::create([
            'nama_pengeluaran' => 'Beli Es Batu', // <--- INI PERBAIKANNYA
            'nominal' => 20000,
            'tanggal' => Carbon::now(),
            'keterangan' => 'Operasional'
        ]);

        $response = $this->actingAs($this->admin)
                         ->get(route('pemilik.laporan.index'));

        $response->assertStatus(200);
        
        // Assert Data
        $response->assertViewHas('totalTransaksi', 1);
        $response->assertViewHas('totalPendapatan', 100000);
        $response->assertViewHas('totalPengeluaran', 20000);
        $response->assertViewHas('labaBersih', 80000); // 100k - 20k
        $response->assertViewHas('tunai', 1);
        $response->assertViewHas('qris', 0);
    }

    public function test_index_with_date_filter()
    {
        DB::table('kategori')->insert(['id_kategori' => 1, 'nama_kategori' => 'Umum']);

        $yesterday = Carbon::yesterday()->format('Y-m-d');
        $today = Carbon::today()->format('Y-m-d');

        $this->createTransaction($yesterday, 'tunai', 50000, 1, 'Roti');
        $this->createTransaction($today, 'qris', 50000, 1, 'Kue');

        $response = $this->actingAs($this->admin)
                         ->get(route('pemilik.laporan.index', [
                             'start_date' => $yesterday,
                             'end_date' => $today
                         ]));

        $response->assertStatus(200);
        $response->assertViewHas('totalTransaksi', 2);
        $response->assertViewHas('totalPendapatan', 100000);
    }

    public function test_print_view_loads()
    {
        DB::table('kategori')->insert(['id_kategori' => 1, 'nama_kategori' => 'Umum']);
        $this->createTransaction(Carbon::now(), 'tunai', 50000, 1, 'Item Print');

        $response = $this->actingAs($this->admin)
                         ->get(route('pemilik.laporan.print'));

        $response->assertStatus(200);
        $response->assertViewHas('totalPendapatan', 50000);
    }

    public function test_export_redirects_to_print()
    {
        $startDate = '2025-01-01';
        $endDate = '2025-01-31';

        $response = $this->actingAs($this->admin)
                         ->get(route('pemilik.laporan.export', [
                             'start_date' => $startDate,
                             'end_date' => $endDate
                         ]));

        $response->assertRedirect(route('pemilik.laporan.print', [
            'start_date' => $startDate,
            'end_date' => $endDate
        ]));
    }
}