<?php

namespace App\Http\Controllers\Pegawai;

use App\Http\Controllers\Controller;
use App\Services\Pegawai\PengeluaranService;
use Illuminate\Http\Request;

class OperasionalController extends Controller
{
    protected $pengeluaranService;

    // Inject Service
    public function __construct(PengeluaranService $pengeluaranService)
    {
        $this->pengeluaranService = $pengeluaranService;
    }

    public function index(Request $request)
    {
        
        $pengeluaran = $this->pengeluaranService->getPengeluaranList($request->all());

        
        $totalPengeluaran = $this->pengeluaranService->calculateTotal($pengeluaran);

        
        $editMode = false;
        $pengeluaranEdit = null;

        if ($request->has('edit')) {
            $pengeluaranEdit = $this->pengeluaranService->getPengeluaranById($request->edit);
            if ($pengeluaranEdit) {
                $editMode = true;
            }
        }

        return view('pegawai.pengeluaran.index', compact('pengeluaran', 'totalPengeluaran', 'editMode', 'pengeluaranEdit'));
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
            $this->pengeluaranService->createPengeluaran($validated);

            return redirect()->route('pengeluaran.index')
                ->with('success', 'Pengeluaran berhasil dicatat');

        } catch (\Exception $e) {
            return redirect()->route('pengeluaran.index')
                ->with('error', 'Gagal mencatat pengeluaran: ' . $e->getMessage());
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

            return redirect()->route('pengeluaran.index')
                ->with('success', 'Pengeluaran berhasil diperbarui');

        } catch (\Exception $e) {
            return redirect()->route('pengeluaran.index')
                ->with('error', 'Gagal memperbarui pengeluaran: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            $this->pengeluaranService->deletePengeluaran($id);

            return redirect()->route('pengeluaran.index')
                ->with('success', 'Pengeluaran berhasil dihapus');

        } catch (\Exception $e) {
            return redirect()->route('pengeluaran.index')
                ->with('error', 'Gagal menghapus pengeluaran: ' . $e->getMessage());
        }
    }
}
