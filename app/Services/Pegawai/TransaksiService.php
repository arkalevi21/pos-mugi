<?php

namespace App\Services\Pegawai;

use App\Repositories\Pegawai\TransaksiRepository;
use App\Repositories\Pegawai\ProductRepository;
use Illuminate\Support\Facades\DB;
use Midtrans\Snap;
use Midtrans\Config;
use Exception;

class TransaksiService
{
    protected $transaksiRepository;
    protected $productRepository;

    public function __construct(
        TransaksiRepository $transaksiRepository,
        ProductRepository $productRepository
    ) {
        $this->transaksiRepository = $transaksiRepository;
        $this->productRepository = $productRepository;
    }

    /**
     * Mengubah data session cart (id => qty) menjadi array object product lengkap
     * Digunakan untuk tampilan di halaman Create
     */
    public function getCartDetails(array $sessionCart): array
    {
        $cartItems = [];
        $cartTotal = 0;

        foreach ($sessionCart as $productId => $item) {
            $product = $this->productRepository->findById($productId);

            if ($product) {
                $subtotal = $product->harga * $item['qty'];
                $cartItems[$productId] = [
                    'id_product' => $product->id_produk,
                    'nama_product' => $product->nama_produk,
                    'nama_kategori' => $product->kategori->nama_kategori ?? '-',
                    'harga_satuan' => $product->harga,
                    'qty' => $item['qty'],
                    'subtotal' => $subtotal
                ];
                $cartTotal += $subtotal;
            }
        }

        return [
            'items' => $cartItems,
            'total' => $cartTotal
        ];
    }

    /**
     * Core Logic: Membuat Transaksi (Header + Detail)
     * Return berupa array [TransaksiObject, SnapToken(optional)]
     */
    public function createTransaction(array $data, array $sessionCart): array
    {
        return DB::transaction(function () use ($data, $sessionCart) {

            
            $status = 'pending';
            $uangKembalian = null;

            if ($data['metode_pembayaran'] === 'tunai') {
                $status = 'success';
                $uangKembalian = $data['uang_diterima'] - $data['total_harga'];
            }

            $transaksiData = [
                'id_user' => auth()->id(),
                'nama_pembeli' => $data['nama_pembeli'],
                'metode_pembayaran' => $data['metode_pembayaran'],
                'uang_diterima' => $data['uang_diterima'],
                'uang_kembalian' => $uangKembalian,
                'total_harga' => $data['total_harga'],
                'tanggal' => now(),
                'status' => $status
            ];

            
            $transaksi = $this->transaksiRepository->create($transaksiData);

            
            foreach ($sessionCart as $productId => $item) {
                $product = $this->productRepository->findById($productId);
                if ($product) {
                    $this->transaksiRepository->createDetail([
                        'id_transaksi' => $transaksi->id_transaksi,
                        'id_produk' => $productId,
                        'qty' => $item['qty'],
                        'harga_satuan' => $product->harga,
                        'subtotal' => $product->harga * $item['qty']
                    ]);
                }
            }

            
            $snapToken = null;
            if ($data['metode_pembayaran'] === 'qris') {
                $snapToken = $this->generateSnapToken($transaksi);
                $this->transaksiRepository->update($transaksi, ['snap_token' => $snapToken]);
            }

            return [
                'transaksi' => $transaksi,
                'snap_token' => $snapToken
            ];
        });
    }

    /**
     * Logic Generate Token Midtrans
     */
    private function generateSnapToken($transaksi)
    {
        
        Config::$serverKey = config('midtrans.server_key');
        Config::$isProduction = config('midtrans.is_production');
        Config::$isSanitized = config('midtrans.is_sanitized');
        Config::$is3ds = config('midtrans.is_3ds');

        $params = [
            'transaction_details' => [
                'order_id' => $transaksi->id_transaksi . '-' . time(), 
                'gross_amount' => (int) $transaksi->total_harga,
            ],
            'customer_details' => [
                'first_name' => $transaksi->nama_pembeli,
                'email' => 'guest@example.com',
            ],
            'enabled_payments' => ['gopay', 'shopeepay', 'qris'],
        ];

        return Snap::getSnapToken($params);
    }

    /**
     * Logic Verifikasi Callback Midtrans
     */
    public function handleMidtransCallback(array $requestData)
    {
        $serverKey = config('midtrans.server_key');
        $signatureKey = $requestData['signature_key'];

        $hashed = hash("sha512", $requestData['order_id'] . $requestData['status_code'] . $requestData['gross_amount'] . $serverKey);

        if ($hashed == $signatureKey) {

            $orderIdParts = explode('-', $requestData['order_id']);
            $transaksiId = $orderIdParts[0];

            $transaksi = $this->transaksiRepository->findById($transaksiId);

            if ($transaksi) {
                $status = $requestData['transaction_status'];
                if (in_array($status, ['capture', 'settlement'])) {
                    $this->transaksiRepository->update($transaksi, ['status' => 'success']);
                } elseif (in_array($status, ['expire', 'cancel', 'deny'])) {
                    $this->transaksiRepository->update($transaksi, ['status' => 'failed']);
                }
            }
        }
    }

    public function getTransactionDetail($id)
    {
        return $this->transaksiRepository->findWithDetails($id);
    }
}