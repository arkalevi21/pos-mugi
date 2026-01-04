<?php

namespace App\Http\Controllers\Pegawai;

use App\Http\Controllers\Controller;
use App\Services\Pegawai\TransaksiService;
use App\Services\Pegawai\ProductService;
use App\Services\Pegawai\KategoriService;
use Illuminate\Http\Request;

class TransaksiController extends Controller
{
    protected $transaksiService;
    protected $productService;
    protected $kategoriService;

    public function __construct(
        TransaksiService $transaksiService,
        ProductService $productService,
        KategoriService $kategoriService
    ) {
        $this->transaksiService = $transaksiService;
        $this->productService = $productService;
        $this->kategoriService = $kategoriService;
    }

    public function create(Request $request)
    {
        
        $product = $this->productService->getAllProducts();

        $categoryFilter = $request->get('kategori', 'all');
        if ($categoryFilter !== 'all') {
            $product = $product->where('id_kategori', $categoryFilter);
        }

        $kategori = $this->kategoriService->getAllCategories();


        $cartData = $this->transaksiService->getCartDetails(session('cart', []));

        $cartItems = $cartData['items'];
        $cartTotal = $cartData['total'];

        return view('pegawai.transaksi.create', compact('product', 'kategori', 'cartItems', 'cartTotal', 'categoryFilter'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_pembeli' => 'required|string|max:100',
            'metode_pembayaran' => 'required|in:tunai,qris',
            'uang_diterima' => $request->metode_pembayaran === 'tunai' ? 'required|numeric|min:' . $request->total_harga : 'nullable',
            'total_harga' => 'required|numeric|min:0'
        ]);

        try {

            $result = $this->transaksiService->createTransaction(
                $request->all(),
                session('cart', [])
            );

            $transaksi = $result['transaksi'];


            session()->forget('cart');


            if ($request->metode_pembayaran === 'qris' && isset($result['snap_token'])) {
                return view('pegawai.transaksi.pay_qris', [
                    'transaksi' => $transaksi,
                    'snapToken' => $result['snap_token']
                ]);
            }


            return redirect()->route('transaksi.print', $transaksi->id_transaksi)
                ->with('success', 'Transaksi Tunai Berhasil');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal memproses transaksi: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function callback(Request $request)
    {

        $this->transaksiService->handleMidtransCallback($request->all());
        return response()->json(['status' => 'ok']);
    }

    public function finishQris($id)
    {
        return redirect()->route('transaksi.print', $id)
            ->with('success', 'Pembayaran QRIS Berhasil');
    }

    public function printStruk($id)
    {

        $transaksi = $this->transaksiService->getTransactionDetail($id);
        return view('pegawai.transaksi.struk', compact('transaksi'));
    }



    public function addToCart(Request $request)
    {

        $request->validate([
            'id_produk' => 'required',
            'qty' => 'required|integer|min:1'
        ]);

        $cart = session()->get('cart', []);

        $cartKey = $request->id_produk;

        if (isset($cart[$cartKey])) {
            $cart[$cartKey]['qty'] += $request->qty;
        } else {
            $cart[$cartKey] = ['qty' => $request->qty];
        }

        session()->put('cart', $cart);

        return redirect()->route('transaksi.create')
            ->with('success', 'product ditambahkan ke keranjang');
    }

    public function removeFromCart($id_product)
    {
        $cart = session()->get('cart', []);
        if (isset($cart[$id_product])) {
            unset($cart[$id_product]);
            session()->put('cart', $cart);
        }
        return redirect()->route('transaksi.create');
    }

    public function updateCart(Request $request, $id_product)
    {
        $cart = session()->get('cart', []);
        if (isset($cart[$id_product])) {
            if ($request->qty <= 0) {
                unset($cart[$id_product]);
            } else {
                $cart[$id_product]['qty'] = $request->qty;
            }
            session()->put('cart', $cart);
        }
        return redirect()->route('transaksi.create');
    }

    public function clearCart()
    {
        session()->forget('cart');
        return redirect()->route('transaksi.create')->with('success', 'Keranjang dikosongkan');
    }
}
