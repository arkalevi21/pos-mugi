<?php

namespace App\Http\Controllers\Pegawai;

use App\Http\Controllers\Controller;
use App\Models\Pegawai\Kategori; 
use App\Services\Pegawai\ProductService; 
use Illuminate\Http\Request;

class ProductController extends Controller
{
    protected $productService;

    /**
     * Inject ProductService ke dalam Controller
     */
    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
    }

    /**
     * Menampilkan daftar produk
     */
    public function index(Request $request)
    {
        
        $product = $this->productService->getAllProducts();

        
        $kategori = Kategori::all();

        
        $editMode = false;
        $produkEdit = null;

        if ($request->has('edit')) {
            
            $produkEdit = $this->productService->getProductById($request->edit);
            if ($produkEdit) {
                $editMode = true;
            }
        }

        return view('pegawai.produk.index', compact('product', 'kategori', 'editMode', 'produkEdit'));
    }

    /**
     * Menyimpan produk baru
     */
    public function store(Request $request)
    {
        try {
            // 1. Validasi Input (Tugas HTTP Layer)
            $validatedData = $request->validate([
                'nama_produk' => 'required|string|max:100',
                'id_kategori' => 'required|exists:kategori,id_kategori',
                'harga' => 'required|numeric|min:0',
            ]);

          
            $this->productService->createProduct($validatedData);

            return redirect()->route('produk.index')
                ->with('success', 'Produk berhasil ditambahkan');

        } catch (\Exception $e) {
            return redirect()->route('produk.index')
                ->with('error', 'Gagal menambahkan produk: ' . $e->getMessage());
        }
    }

    /**
     * Memperbarui produk
     */
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
                throw new \Exception("Produk tidak ditemukan.");
            }

            return redirect()->route('produk.index')
                ->with('success', 'Produk berhasil diperbarui');

        } catch (\Exception $e) {
            return redirect()->route('produk.index')
                ->with('error', 'Gagal memperbarui produk: ' . $e->getMessage());
        }
    }

    /**
     * Menghapus produk
     */
    public function destroy($id)
    {
        try {
            
            $this->productService->deleteProduct($id);

            return redirect()->route('produk.index')
                ->with('success', 'Produk berhasil dihapus');

        } catch (\Exception $e) {
            return redirect()->route('produk.index')
                ->with('error', 'Gagal menghapus produk: ' . $e->getMessage());
        }
    }
}