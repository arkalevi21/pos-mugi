<?php

namespace App\Http\Controllers\Pegawai;

use App\Http\Controllers\Controller;
use App\Services\Pegawai\KategoriService;
use Illuminate\Http\Request;

class KategoriController extends Controller
{
    protected $kategoriService;

    
    public function __construct(KategoriService $kategoriService)
    {
        $this->kategoriService = $kategoriService;
    }

    public function index(Request $request)
    {
        
        $kategori = $this->kategoriService->getAllCategories();

        $editMode = false;
        $kategoriEdit = null;

        
        if ($request->has('edit')) {
            $kategoriEdit = $this->kategoriService->getCategoryById($request->edit);
            if ($kategoriEdit) {
                $editMode = true;
            }
        }

        return view('pegawai.kategori.index', compact('kategori', 'editMode', 'kategoriEdit'));
    }

    public function store(Request $request)
    {
        
        $validated = $request->validate([
            'nama_kategori' => 'required|string|max:100|unique:kategori,nama_kategori'
        ]);

        try {
            
            $this->kategoriService->createCategory($validated);

            return redirect()->route('kategori.index')
                ->with('success', 'Kategori berhasil ditambahkan');

        } catch (\Exception $e) {
            return redirect()->route('kategori.index')
                ->with('error', 'Gagal menambahkan kategori: ' . $e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'nama_kategori' => 'required|string|max:100|unique:kategori,nama_kategori,' . $id . ',id_kategori'
        ]);

        try {
            $this->kategoriService->updateCategory($id, $validated);

            return redirect()->route('kategori.index')
                ->with('success', 'Kategori berhasil diperbarui');

        } catch (\Exception $e) {
            return redirect()->route('kategori.index')
                ->with('error', 'Gagal memperbarui kategori: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            
            $this->kategoriService->deleteCategory($id);

            return redirect()->route('kategori.index')
                ->with('success', 'Kategori berhasil dihapus');

        } catch (\Exception $e) {
            
            return redirect()->route('kategori.index')
                ->with('error', $e->getMessage());
        }
    }
}