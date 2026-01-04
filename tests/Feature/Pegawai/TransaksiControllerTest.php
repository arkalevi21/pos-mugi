<?php

namespace Tests\Feature\Pegawai;

use Tests\TestCase;
use App\Models\User;
use App\Models\Pegawai\Produk;
use App\Models\Pegawai\Kategori;
use App\Models\Pegawai\Transaksi;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Mockery;
use App\Services\Pegawai\TransaksiService;

class TransaksiControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $kasir;
    protected $produk;
    protected $kategori;

    protected function setUp(): void
    {
        parent::setUp();

        $this->kasir = User::create([
            'nama_user' => 'Kasir',
            'username' => 'kasir',
            'password' => Hash::make('password'),
            'role' => 'kasir'
        ]);

        $this->kategori = Kategori::create(['nama_kategori' => 'Minuman']);
        $this->produk = Produk::create([
            'nama_produk' => 'Kopi',
            'id_kategori' => $this->kategori->id_kategori,
            'harga' => 5000,
            'stok' => 10,
            'is_active' => true
        ]);
    }

    // --- COVER: Filter Kategori ($product->where(...)) ---
    public function test_create_page_with_category_filter()
    {
        // Buat kategori lain dan produk lain
        $katMakanan = Kategori::create(['nama_kategori' => 'Makanan']);
        Produk::create(['nama_produk' => 'Roti', 'id_kategori' => $katMakanan->id_kategori, 'harga' => 2000]);

        // Request dengan parameter ?kategori=ID
        $response = $this->actingAs($this->kasir)
            ->get(route('transaksi.create', ['kategori' => $this->kategori->id_kategori]));

        $response->assertStatus(200);

        // Assert view variable 'categoryFilter' terisi
        $response->assertViewHas('categoryFilter', $this->kategori->id_kategori);

        // Assert produk yang muncul hanya yang sesuai kategori
        $productsInView = $response->viewData('product');
        $this->assertTrue($productsInView->contains('id_produk', $this->produk->id_produk));
        $this->assertFalse($productsInView->contains('nama_produk', 'Roti'));
    }

    // --- COVER: Cart Logic (+= qty dan unset) ---
    public function test_cart_operations_complete_logic()
    {
        // 1. Add Baru (Masuk ke else)
        $this->actingAs($this->kasir)->post(route('cart.add'), ['id_produk' => $this->produk->id_produk, 'qty' => 1]);
        $this->assertEquals(1, session('cart')[$this->produk->id_produk]['qty']);

        // 2. Add Item Sama (Masuk ke if isset -> += qty) -- INI COVER '+='
        $this->actingAs($this->kasir)->post(route('cart.add'), ['id_produk' => $this->produk->id_produk, 'qty' => 2]);
        $this->assertEquals(3, session('cart')[$this->produk->id_produk]['qty']);

        // 3. Update jadi 0 (Masuk ke unset) -- INI COVER 'unset' di updateCart
        $this->actingAs($this->kasir)->put(route('cart.update', $this->produk->id_produk), ['qty' => 0]);
        $this->assertFalse(isset(session('cart')[$this->produk->id_produk]));

        // 4. Remove Cart (Masuk ke unset) -- INI COVER 'unset' di removeFromCart
        // Add dulu biar ada
        session(['cart' => [$this->produk->id_produk => ['qty' => 1]]]);
        $this->actingAs($this->kasir)->delete(route('cart.remove', $this->produk->id_produk));
        $this->assertFalse(isset(session('cart')[$this->produk->id_produk]));
    }

    // --- COVER: Store QRIS & View pay_qris ---
    public function test_store_transaction_qris_returns_view()
    {
        // 1. Matikan Exception Handling agar jika error, pesannya jelas
        $this->withoutExceptionHandling();

        session(['cart' => [$this->produk->id_produk => ['qty' => 1]]]);

        // --- PERBAIKAN DI SINI ---
        // Kita tidak pakai mass assignment di constructor untuk ID
        // Supaya tidak diblokir oleh $fillable
        $mockTrx = new Transaksi();
        $mockTrx->id_transaksi = 123; // Set ID manual
        $mockTrx->total_harga = 5000;
        $mockTrx->status = 'pending';

        // 2. Mock Service
        $mockService = Mockery::mock(TransaksiService::class);
        $mockService->shouldReceive('createTransaction')
            ->andReturn([
                'transaksi' => $mockTrx, // Gunakan objek yang ID-nya sudah diset
                'snap_token' => 'dummy-token-123'
            ]);

        $this->app->instance(TransaksiService::class, $mockService);

        $data = [
            'nama_pembeli' => 'Qris User',
            'metode_pembayaran' => 'qris',
            'uang_diterima' => null,
            'total_harga' => 5000
        ];

        // 3. Eksekusi
        $response = $this->actingAs($this->kasir)
            ->post(route('transaksi.store'), $data);

        // 4. Assert
        $response->assertStatus(200);
        $response->assertViewIs('pegawai.transaksi.pay_qris');
        $response->assertViewHas('snapToken', 'dummy-token-123');
    }

    // --- COVER: Callback Endpoint ---
    public function test_callback_endpoint()
    {
        // Mock Service biar gak error ke DB
        $mockService = Mockery::mock(TransaksiService::class);
        $mockService->shouldReceive('handleMidtransCallback')->once();
        $this->app->instance(TransaksiService::class, $mockService);

        $response = $this->postJson(route('midtrans.callback'), [
            'order_id' => '123',
            'transaction_status' => 'settlement'
        ]);

        $response->assertStatus(200);
        $response->assertJson(['status' => 'ok']);
    }

    // --- COVER: Finish QRIS ---
    public function test_finish_qris_redirects()
    {
        $id = 123;
        $response = $this->actingAs($this->kasir)
            ->get(route('transaksi.finish_qris', $id));

        // Assert redirect ke print
        $response->assertRedirect(route('transaksi.print', $id));
        $response->assertSessionHas('success', 'Pembayaran QRIS Berhasil');
    }

    /**
     * Test khusus untuk memastikan fitur Clear Cart berjalan
     * dan menutup coverage pada method clearCart()
     */
    public function test_clear_cart_removes_session()
    {
        // 1. Setup: Isi session cart secara manual dengan data dummy
        // Kita simpan ID produk dan Qty ke dalam session
        session(['cart' => [
            $this->produk->id_produk => ['qty' => 5]
        ]]);

        // Verifikasi awal: Pastikan session cart benar-benar ada isinya
        $this->assertTrue(session()->has('cart'));
        $this->assertNotEmpty(session('cart'));

        // 2. Action: Panggil route untuk clear cart
        // Route ini mengarah ke TransaksiController@clearCart
        $response = $this->actingAs($this->kasir)
            ->delete(route('cart.clear'));

        // 3. Assert
        // Cek redirect ke halaman create transaksi
        $response->assertRedirect(route('transaksi.create'));

        // Cek pesan sukses di session (sesuai controller: 'Keranjang dikosongkan')
        $response->assertSessionHas('success', 'Keranjang dikosongkan');

        // Cek session 'cart' SUDAH HILANG (Ini yang paling penting untuk coverage)
        $response->assertSessionMissing('cart');
    }
}
