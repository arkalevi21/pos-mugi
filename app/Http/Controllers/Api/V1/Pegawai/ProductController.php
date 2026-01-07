<?php

namespace App\Http\Controllers\Api\V1\Pegawai;

use App\Http\Controllers\Controller;
use App\Services\Pegawai\ProductService;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    protected $productService;

    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
    }

    // Mengambil semua data produk (Pengganti Index View)
    public function index()
    {
        $products = $this->productService->getAllProducts();

        return response()->json([
            'status' => 'success',
            'data' => $products
        ]);
    }

    // Detail satu produk (untuk edit form di frontend)
    public function show($id)
    {
        $product = $this->productService->getProductById($id);

        if (!$product) {
            return response()->json(['status' => 'error', 'message' => 'Produk tidak ditemukan'], 404);
        }

        return response()->json(['status' => 'success', 'data' => $product]);
    }

    public function store(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'nama_produk' => 'required|string|max:100',
                'id_kategori' => 'required|exists:kategori,id_kategori',
                'harga' => 'required|numeric|min:0',
            ]);

            $product = $this->productService->createProduct($validatedData);

            return response()->json([
                'status' => 'success',
                'message' => 'Produk berhasil ditambahkan',
                'data' => $product
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 400);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $validatedData = $request->validate([
                'nama_produk' => 'required|string|max:100',
                'id_kategori' => 'required|exists:kategori,id_kategori',
                'harga' => 'required|numeric|min:0',
            ]);

            $updated = $this->productService->updateProduct($id, $validatedData);

            if (!$updated) {
                return response()->json(['status' => 'error', 'message' => 'Produk tidak ditemukan'], 404);
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Produk berhasil diperbarui'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 400);
        }
    }

    public function destroy($id)
    {
        try {
            $this->productService->deleteProduct($id);
            return response()->json(['status' => 'success', 'message' => 'Produk berhasil dihapus']);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }
}