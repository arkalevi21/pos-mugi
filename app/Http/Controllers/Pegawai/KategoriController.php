<?php

namespace App\Http\Controllers\Pegawai;

use App\Http\Controllers\Controller;
use App\Models\Pegawai\Kategori;
use Illuminate\Http\Request;

class KategoriController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $kategori = Kategori::withCount('produk')->get();
        
        $editMode = false;
        $kategoriEdit = null;
        
        // Cek jika ada parameter edit
        if ($request->has('edit')) {
            $kategoriEdit = Kategori::find($request->edit);
            if ($kategoriEdit) {
                $editMode = true;
            }
        }
        
        return view('pegawai.kategori.index', compact('kategori', 'editMode', 'kategoriEdit'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'nama_kategori' => 'required|string|max:100|unique:kategori,nama_kategori'
            ]);
            
            Kategori::create([
                'nama_kategori' => $request->nama_kategori
            ]);
            
            return redirect()->route('kategori.index')
                ->with('success', 'Kategori berhasil ditambahkan');
            
        } catch (\Exception $e) {
            return redirect()->route('kategori.index')
                ->with('error', 'Gagal menambahkan kategori: ' . $e->getMessage());
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        try {
            $request->validate([
                'nama_kategori' => 'required|string|max:100|unique:kategori,nama_kategori,' . $id . ',id_kategori'
            ]);
            
            $kategori = Kategori::findOrFail($id);
            $kategori->update([
                'nama_kategori' => $request->nama_kategori
            ]);
            
            return redirect()->route('kategori.index')
                ->with('success', 'Kategori berhasil diperbarui');
            
        } catch (\Exception $e) {
            return redirect()->route('kategori.index')
                ->with('error', 'Gagal memperbarui kategori: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $kategori = Kategori::findOrFail($id);
            
            // Cek apakah kategori digunakan oleh produk
            if ($kategori->produk()->count() > 0) {
                return redirect()->route('kategori.index')
                    ->with('error', 'Kategori tidak dapat dihapus karena masih digunakan oleh produk');
            }
            
            $kategori->delete();
            
            return redirect()->route('kategori.index')
                ->with('success', 'Kategori berhasil dihapus');
            
        } catch (\Exception $e) {
            return redirect()->route('kategori.index')
                ->with('error', 'Gagal menghapus kategori: ' . $e->getMessage());
        }
    }
}