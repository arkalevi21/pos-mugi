<?php

namespace App\Http\Controllers\Pegawai;

use App\Http\Controllers\Controller;
use App\Models\Pegawai\Produk;
use App\Models\Pegawai\Kategori;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $produk = Produk::with('kategori')->get();
        $kategori = Kategori::all();
        
        $editMode = false;
        $produkEdit = null;
        
        // Cek jika ada parameter edit
        if ($request->has('edit')) {
            $produkEdit = Produk::find($request->edit);
            if ($produkEdit) {
                $editMode = true;
            }
        }
        
        return view('pegawai.produk.index', compact('produk', 'kategori', 'editMode', 'produkEdit'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'nama_produk' => 'required|string|max:100',
                'id_kategori' => 'required|exists:kategori,id_kategori',
                'harga' => 'required|numeric|min:0',
                'gambar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
            ]);
            
            $data = [
                'nama_produk' => $request->nama_produk,
                'id_kategori' => $request->id_kategori,
                'harga' => $request->harga,
                'stok' => 0,
                'is_active' => true
            ];
            
            // Handle gambar upload
            if ($request->hasFile('gambar')) {
                $path = $request->file('gambar')->store('products', 'public');
                $data['gambar'] = basename($path);
            }
            
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
                'gambar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
            ]);
            
            $produk = Produk::findOrFail($id);
            
            $data = [
                'nama_produk' => $request->nama_produk,
                'id_kategori' => $request->id_kategori,
                'harga' => $request->harga
            ];
            
            // Handle gambar upload jika ada file baru
            if ($request->hasFile('gambar')) {
                // Hapus gambar lama jika ada
                if ($produk->gambar && Storage::disk('public')->exists('products/' . $produk->gambar)) {
                    Storage::disk('public')->delete('products/' . $produk->gambar);
                }
                
                $path = $request->file('gambar')->store('products', 'public');
                $data['gambar'] = basename($path);
            }
            
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
            
            // Hapus gambar jika ada
            if ($produk->gambar && Storage::disk('public')->exists('products/' . $produk->gambar)) {
                Storage::disk('public')->delete('products/' . $produk->gambar);
            }
            
            $produk->delete();
            
            return redirect()->route('produk.index')
                ->with('success', 'Produk berhasil dihapus');
            
        } catch (\Exception $e) {
            return redirect()->route('produk.index')
                ->with('error', 'Gagal menghapus produk: ' . $e->getMessage());
        }
    }
}