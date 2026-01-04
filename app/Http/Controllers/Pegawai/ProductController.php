<?php

namespace App\Http\Controllers\Pegawai;

use App\Http\Controllers\Controller;
use App\Models\Pegawai\Produk;
use App\Models\Pegawai\Kategori;
use Illuminate\Http\Request;
// use Illuminate\Support\Facades\Storage; // Hapus ini karena tidak pakai storage gambar lagi

class ProductController extends Controller
{
    // HAPUS baris 'private const PRODUCT_DIR' di sini. Kita tidak butuh lagi.

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $product = Produk::with('kategori')->get();
        $kategori = Kategori::all();
        
        $editMode = false;
        $produkEdit = null;
        
        if ($request->has('edit')) {
            $produkEdit = Produk::find($request->edit);
            if ($produkEdit) {
                $editMode = true;
            }
        }
        
        return view('pegawai.produk.index', compact('product', 'kategori', 'editMode', 'produkEdit'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            // Validasi tanpa gambar
            $request->validate([
                'nama_produk' => 'required|string|max:100',
                'id_kategori' => 'required|exists:kategori,id_kategori',
                'harga' => 'required|numeric|min:0',
            ]);
            
            $data = [
                'nama_produk' => $request->nama_produk,
                'id_kategori' => $request->id_kategori,
                'harga' => $request->harga,
                'stok' => 0,
                'is_active' => true
            ];
            
            // Logika upload gambar dihapus total
            
            Produk::create($data);
            
            return redirect()->route('produk.index')
                ->with('success', 'Produk berhasil ditambahkan');
            
        } catch (\Exception $e) {
            return redirect()->route('produk.index')
                ->with('error', 'Gagal menambahkan produk: ' . $e->getMessage());
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        try {
            $request->validate([
                'nama_produk' => 'required|string|max:100',
                'id_kategori' => 'required|exists:kategori,id_kategori',
                'harga' => 'required|numeric|min:0',
            ]);
            
            $produk = Produk::findOrFail($id);
            
            $data = [
                'nama_produk' => $request->nama_produk,
                'id_kategori' => $request->id_kategori,
                'harga' => $request->harga
            ];
            
            // Logika update gambar dihapus total
            
            $produk->update($data);
            
            return redirect()->route('produk.index')
                ->with('success', 'Produk berhasil diperbarui');
            
        } catch (\Exception $e) {
            return redirect()->route('produk.index')
                ->with('error', 'Gagal memperbarui produk: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $produk = Produk::findOrFail($id);
            
            // Logika hapus file gambar dihapus total
            
            $produk->delete();
            
            return redirect()->route('produk.index')
                ->with('success', 'Produk berhasil dihapus');
            
        } catch (\Exception $e) {
            return redirect()->route('produk.index')
                ->with('error', 'Gagal menghapus produk: ' . $e->getMessage());
        }
    }
}
