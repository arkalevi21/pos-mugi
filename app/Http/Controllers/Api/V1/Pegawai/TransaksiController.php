<?php

namespace App\Http\Controllers\Api\V1\Pegawai;

use App\Http\Controllers\Controller;
use App\Services\Pegawai\TransaksiService;
use Illuminate\Http\Request;

class TransaksiController extends Controller
{
    protected $transaksiService;

    public function __construct(TransaksiService $transaksiService)
    {
        $this->transaksiService = $transaksiService;
    }

    /**
     * Checkout Transaksi
     * Payload JSON yg diharapkan:
     * {
     * "nama_pembeli": "Budi",
     * "metode_pembayaran": "tunai",
     * "uang_diterima": 50000,
     * "total_harga": 45000,
     * "items": [
     * {"id_produk": 1, "qty": 2},
     * {"id_produk": 5, "qty": 1}
     * ]
     * }
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama_pembeli' => 'required|string|max:100',
            'metode_pembayaran' => 'required|in:tunai,qris',
            'uang_diterima' => $request->metode_pembayaran === 'tunai' ? 'required|numeric' : 'nullable',
            'total_harga' => 'required|numeric|min:0',
            'items' => 'required|array|min:1', 
            'items.*.id_produk' => 'required|exists:produk,id_produk',
            'items.*.qty' => 'required|integer|min:1',
        ]);

        try {
            // Karena Service kamu logicnya memisahkan $data dan $cartItems
            // Kita ambil cart items dari request body
            $cartItems = $request->input('items');

            $result = $this->transaksiService->createTransaction(
                $request->except('items'),
                $cartItems
            );

            return response()->json([
                'status' => 'success',
                'message' => 'Transaksi Berhasil',
                'data' => [
                    'transaksi' => $result['transaksi'],
                    'snap_token' => $result['snap_token'] ?? null // Jika QRIS
                ]
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal memproses transaksi: ' . $e->getMessage()
            ], 500);
        }
    }

    
    public function callback(Request $request)
    {
        $this->transaksiService->handleMidtransCallback($request->all());
        return response()->json(['status' => 'ok']);
    }

    public function show($id)
    {
        $transaksi = $this->transaksiService->getTransactionDetail($id);

        if (!$transaksi) {
            return response()->json(['status' => 'error', 'message' => 'Transaksi tidak ditemukan'], 404);
        }

        return response()->json(['status' => 'success', 'data' => $transaksi]);
    }
}