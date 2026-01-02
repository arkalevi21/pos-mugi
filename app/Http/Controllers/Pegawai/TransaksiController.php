<?php

namespace App\Http\Controllers\Pegawai;

use App\Http\Controllers\Controller;
use App\Models\Pegawai\Produk;
use App\Models\Pegawai\Transaksi;
use App\Models\Pegawai\DetailTransaksi;
use App\Models\Pegawai\Kategori;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Midtrans\Snap;
use Midtrans\Config;

class TransaksiController extends Controller
{
    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $produk = Produk::with('kategori')->get();
        $kategori = Kategori::all();

        // Get cart from session
        $cart = session('cart', []);
        $cartItems = [];
        $cartTotal = 0;

        foreach ($cart as $productId => $item) {
            $produkItem = Produk::with('kategori')->find($productId);
            if ($produkItem) {
                $cartItems[$productId] = [
                    'id_produk' => $produkItem->id_produk,
                    'nama_produk' => $produkItem->nama_produk,
                    'nama_kategori' => $produkItem->kategori->nama_kategori,
                    'harga_satuan' => $produkItem->harga,
                    'qty' => $item['qty'],
                    'subtotal' => $produkItem->harga * $item['qty']
                ];
                $cartTotal += $produkItem->harga * $item['qty'];
            }
        }

        // Filter produk berdasarkan kategori
        $categoryFilter = $request->get('kategori', 'all');
        if ($categoryFilter !== 'all') {
            $produk = $produk->where('id_kategori', $categoryFilter);
        }

        return view('pegawai.transaksi.create', compact('produk', 'kategori', 'cartItems', 'cartTotal', 'categoryFilter'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        DB::beginTransaction();

        try {
            $request->validate([
                'nama_pembeli' => 'required|string|max:100',
                'metode_pembayaran' => 'required|in:tunai,qris',
                // Uang diterima hanya wajib jika tunai
                'uang_diterima' => $request->metode_pembayaran === 'tunai' ? 'required|numeric|min:' . $request->total_harga : 'nullable',
                'total_harga' => 'required|numeric|min:0'
            ]);

            // Logic status awal
            $status = 'pending';
            if ($request->metode_pembayaran === 'tunai') {
                $status = 'success'; // Tunai dianggap langsung lunas
            }

            // Hitung kembalian (hanya untuk tunai)
            $uang_kembalian = null;
            if ($request->metode_pembayaran === 'tunai') {
                $uang_kembalian = $request->uang_diterima - $request->total_harga;
            }

            // 1. Buat Transaksi di DB
            $transaksi = Transaksi::create([
                'id_user' => auth()->id(),
                'nama_pembeli' => $request->nama_pembeli,
                'metode_pembayaran' => $request->metode_pembayaran,
                'uang_diterima' => $request->uang_diterima, // Bisa null jika QRIS
                'uang_kembalian' => $uang_kembalian,
                'total_harga' => $request->total_harga,
                'tanggal' => now(),
                'status' => $status
            ]);

            // 2. Simpan Detail Transaksi
            $cart = session('cart', []);
            foreach ($cart as $productId => $item) {
                $produk = Produk::find($productId);
                if ($produk) {
                    DetailTransaksi::create([
                        'id_transaksi' => $transaksi->id_transaksi,
                        'id_produk' => $productId,
                        'qty' => $item['qty'],
                        'harga_satuan' => $produk->harga,
                        'subtotal' => $produk->harga * $item['qty']
                    ]);
                }
            }

            // 3. LOGIC KHUSUS MIDTRANS (QRIS)
            if ($request->metode_pembayaran === 'qris') {
                // Set konfigurasi Midtrans
                Config::$serverKey = config('midtrans.server_key');
                Config::$isProduction = config('midtrans.is_production');
                Config::$isSanitized = config('midtrans.is_sanitized');
                Config::$is3ds = config('midtrans.is_3ds');

                // Buat params untuk Snap
                $params = [
                    'transaction_details' => [
                        'order_id' => $transaksi->id_transaksi . '-' . time(), // Order ID harus unik, tambah timestamp biar aman
                        'gross_amount' => (int) $request->total_harga,
                    ],
                    'customer_details' => [
                        'first_name' => $request->nama_pembeli,
                        'email' => 'guest@example.com', // Optional, bisa diisi dummy
                    ],
                    // Opsional: Batasi hanya muncul QRIS (Gopay/ShopeePay termasuk QRIS di Midtrans Core API, tapi di Snap muncul semua e-wallet)
                    'enabled_payments' => ['gopay', 'shopeepay', 'qris'],
                ];

                $snapToken = Snap::getSnapToken($params);

                // Update snap_token ke DB
                $transaksi->update(['snap_token' => $snapToken]);

                DB::commit();

                // Redirect kembali ke halaman create tapi bawa variable snapToken
                // Kita tidak hapus session cart dulu sampai bayar sukses (opsional, tergantung flow)
                // Disini saya pilih: Cart dihapus, status pending, user disuruh bayar.
                session()->forget('cart');

                return view('pegawai.transaksi.pay_qris', compact('transaksi', 'snapToken'));
            }

            // Jika Tunai, Commit dan Print
            DB::commit();
            session()->forget('cart');

            return redirect()->route('transaksi.print', $transaksi->id_transaksi)
                ->with('success', 'Transaksi Tunai Berhasil');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Gagal: ' . $e->getMessage())
                ->withInput();
        }
    }

    // Method untuk menangani Callback dari Midtrans (Webhook)
    public function callback(Request $request)
    {
        $serverKey = config('midtrans.server_key');
        $hashed = hash("sha512", $request->order_id.$request->status_code.$request->gross_amount.$serverKey);
        
        if($hashed == $request->signature_key){
            // Valid signature
            // Ambil ID Transaksi asli (karena tadi kita tambah timestamp, misal: "15-1704202020")
            $orderIdParts = explode('-', $request->order_id);
            $transaksiId = $orderIdParts[0];

            $transaksi = Transaksi::find($transaksiId);
            
            if($transaksi) {
                if($request->transaction_status == 'capture' || $request->transaction_status == 'settlement'){
                    $transaksi->update(['status' => 'success']);
                } elseif($request->transaction_status == 'expire' || $request->transaction_status == 'cancel' || $request->transaction_status == 'deny'){
                    $transaksi->update(['status' => 'failed']);
                }
            }
        }
        return response()->json(['status' => 'ok']);
    }

    // Tambahkan method ini jika user selesai bayar dan ingin cek status manual/redirect success
    public function finishQris($id) 
    {
        $transaksi = Transaksi::findOrFail($id);
        // Cek lagi ke midtrans statusnya (optional) atau langsung redirect print
        return redirect()->route('transaksi.print', $transaksi->id_transaksi)
                ->with('success', 'Pembayaran QRIS Berhasil');
    }

    /**
     * Add product to cart (session) - FORM SUBMIT
     */
    public function addToCart(Request $request)
    {
        $request->validate([
            'id_produk' => 'required|exists:produk,id_produk',
            'qty' => 'required|integer|min:1'
        ]);

        $cart = session()->get('cart', []);
        $cartKey = $request->id_produk;

        if (isset($cart[$cartKey])) {
            $cart[$cartKey]['qty'] += $request->qty;
        } else {
            $cart[$cartKey] = [
                'qty' => $request->qty
            ];
        }

        session()->put('cart', $cart);

        return redirect()->route('transaksi.create')
            ->with('success', 'Produk ditambahkan ke keranjang');
    }

    /**
     * Remove product from cart - FORM SUBMIT
     */
    public function removeFromCart($id_produk)
    {
        $cart = session()->get('cart', []);

        if (isset($cart[$id_produk])) {
            unset($cart[$id_produk]);
            session()->put('cart', $cart);

            return redirect()->route('transaksi.create')
                ->with('success', 'Produk dihapus dari keranjang');
        }

        return redirect()->route('transaksi.create')
            ->with('error', 'Produk tidak ditemukan di keranjang');
    }

    /**
     * Update cart quantity - FORM SUBMIT
     */
    public function updateCart(Request $request, $id_produk)
    {
        $request->validate([
            'qty' => 'required|integer|min:1'
        ]);

        $cart = session()->get('cart', []);

        if (isset($cart[$id_produk])) {
            if ($request->qty <= 0) {
                unset($cart[$id_produk]);
            } else {
                $cart[$id_produk]['qty'] = $request->qty;
            }

            session()->put('cart', $cart);

            return redirect()->route('transaksi.create')
                ->with('success', 'Keranjang diperbarui');
        }

        return redirect()->route('transaksi.create')
            ->with('error', 'Produk tidak ditemukan di keranjang');
    }

    /**
     * Clear cart - FORM SUBMIT
     */
    public function clearCart()
    {
        session()->forget('cart');

        return redirect()->route('transaksi.create')
            ->with('success', 'Keranjang dikosongkan');
    }

    /**
     * Print receipt
     */
    public function printStruk($id)
    {
        $transaksi = Transaksi::with(['detailTransaksi.produk', 'user'])->findOrFail($id);

        return view('pegawai.transaksi.struk', compact('transaksi'));
    }
}
