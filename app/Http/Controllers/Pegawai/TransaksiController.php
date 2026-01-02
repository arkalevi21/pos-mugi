<?php

namespace App\Http\Controllers\Pegawai;

use App\Http\Controllers\Controller;
use App\Models\Pegawai\Produk;
use App\Models\Pegawai\Transaksi;
use App\Models\Pegawai\DetailTransaksi;
use App\Models\Pegawai\Kategori;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
                'uang_diterima' => $request->metode_pembayaran === 'tunai' ? 'required|numeric|min:' . $request->total_harga : 'nullable',
                'total_harga' => 'required|numeric|min:0'
            ]);
            
            // Validasi uang diterima untuk tunai
            if ($request->metode_pembayaran === 'tunai') {
                if ($request->uang_diterima < $request->total_harga) {
                    return redirect()->back()
                        ->with('error', 'Uang diterima kurang dari total harga')
                        ->withInput();
                }
            }
            
            // Buat transaksi
            $transaksi = Transaksi::create([
                'id_user' => auth()->id(),
                'nama_pembeli' => $request->nama_pembeli,
                'metode_pembayaran' => $request->metode_pembayaran,
                'uang_diterima' => $request->uang_diterima,
                'uang_kembalian' => $request->uang_diterima ? $request->uang_diterima - $request->total_harga : null,
                'total_harga' => $request->total_harga,
                'tanggal' => now()
            ]);
            
            // Simpan detail transaksi dari session cart
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
            
            DB::commit();
            
            // Clear cart session
            session()->forget('cart');
            
            // Redirect ke halaman struk
            return redirect()->route('transaksi.print', $transaksi->id_transaksi)
                ->with('success', 'Transaksi berhasil disimpan');
            
        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()->back()
                ->with('error', 'Gagal menyimpan transaksi: ' . $e->getMessage())
                ->withInput();
        }
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