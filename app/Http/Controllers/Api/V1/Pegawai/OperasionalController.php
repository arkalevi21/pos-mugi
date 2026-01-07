<?php

namespace App\Http\Controllers\Api\V1\Pegawai;

use App\Http\Controllers\Controller;
use App\Services\Pegawai\PengeluaranService;
use Illuminate\Http\Request;

class OperasionalController extends Controller
{
    protected $pengeluaranService;

    public function __construct(PengeluaranService $pengeluaranService)
    {
        $this->pengeluaranService = $pengeluaranService;
    }

    public function index(Request $request)
    {
        $pengeluaran = $this->pengeluaranService->getPengeluaranList($request->all());
        $total = $this->pengeluaranService->calculateTotal($pengeluaran);

        return response()->json([
            'status' => 'success',
            'data' => [
                'list' => $pengeluaran,
                'total' => $total
            ]
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_pengeluaran' => 'required|string|max:150',
            'nominal' => 'required|numeric|min:0',
            'tanggal' => 'required|date',
            'keterangan' => 'nullable|string'
        ]);

        try {
            $data = $this->pengeluaranService->createPengeluaran($validated);
            return response()->json([
                'status' => 'success',
                'message' => 'Pengeluaran dicatat',
                'data' => $data
            ], 201);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 400);
        }
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'nama_pengeluaran' => 'required|string|max:150',
            'nominal' => 'required|numeric|min:0',
            'tanggal' => 'required|date',
            'keterangan' => 'nullable|string'
        ]);

        try {
            $this->pengeluaranService->updatePengeluaran($id, $validated);
            return response()->json(['status' => 'success', 'message' => 'Pengeluaran diperbarui']);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 400);
        }
    }

    public function destroy($id)
    {
        try {
            $this->pengeluaranService->deletePengeluaran($id);
            return response()->json(['status' => 'success', 'message' => 'Pengeluaran dihapus']);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 400);
        }
    }
}