<?php

namespace App\Http\Controllers\Api\V1\Pegawai;

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

    public function index()
    {
        $kategori = $this->kategoriService->getAllCategories();
        return response()->json(['status' => 'success', 'data' => $kategori]);
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'nama_kategori' => 'required|string|max:100|unique:kategori,nama_kategori'
            ]);

            $category = $this->kategoriService->createCategory($validated);

            return response()->json([
                'status' => 'success',
                'message' => 'Kategori berhasil ditambahkan',
                'data' => $category
            ], 201);

        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 400);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $validated = $request->validate([
                'nama_kategori' => 'required|string|max:100|unique:kategori,nama_kategori,' . $id . ',id_kategori'
            ]);

            $this->kategoriService->updateCategory($id, $validated);

            return response()->json(['status' => 'success', 'message' => 'Kategori berhasil diperbarui']);

        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 400);
        }
    }

    public function destroy($id)
    {
        try {
            $this->kategoriService->deleteCategory($id);
            return response()->json(['status' => 'success', 'message' => 'Kategori berhasil dihapus']);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 400);
        }
    }
}