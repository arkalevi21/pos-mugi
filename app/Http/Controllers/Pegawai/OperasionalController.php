<?php

namespace App\Http\Controllers\Pegawai;

use App\Http\Controllers\Controller;
use App\Models\Pegawai\Pengeluaran;
use Illuminate\Http\Request;

class OperasionalController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Pengeluaran::orderBy('tanggal', 'desc');
        
        // Filter berdasarkan tanggal jika ada
        if ($request->has('tanggal')) {
            $query->whereDate('tanggal', $request->tanggal);
        }
        
        $pengeluaran = $query->get();
        
        // Hitung total pengeluaran
        $totalPengeluaran = $pengeluaran->sum('nominal');
        
        $editMode = false;
        $pengeluaranEdit = null;
        
        // Cek jika ada parameter edit
        if ($request->has('edit')) {
            $pengeluaranEdit = Pengeluaran::find($request->edit);
            if ($pengeluaranEdit) {
                $editMode = true;
            }
        }
        
        return view('pegawai.pengeluaran.index', compact('pengeluaran', 'totalPengeluaran', 'editMode', 'pengeluaranEdit'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'nama_pengeluaran' => 'required|string|max:150',
                'nominal' => 'required|numeric|min:0',
                'tanggal' => 'required|date',
                'keterangan' => 'nullable|string'
            ]);
            
            Pengeluaran::create([
                'nama_pengeluaran' => $request->nama_pengeluaran,
                'nominal' => $request->nominal,
                'tanggal' => $request->tanggal,
                'keterangan' => $request->keterangan
            ]);
            
            return redirect()->route('pengeluaran.index')
                ->with('success', 'Pengeluaran berhasil dicatat');
            
        } catch (\Exception $e) {
            return redirect()->route('pengeluaran.index')
                ->with('error', 'Gagal mencatat pengeluaran: ' . $e->getMessage());
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        try {
            $request->validate([
                'nama_pengeluaran' => 'required|string|max:150',
                'nominal' => 'required|numeric|min:0',
                'tanggal' => 'required|date',
                'keterangan' => 'nullable|string'
            ]);
            
            $pengeluaran = Pengeluaran::findOrFail($id);
            $pengeluaran->update([
                'nama_pengeluaran' => $request->nama_pengeluaran,
                'nominal' => $request->nominal,
                'tanggal' => $request->tanggal,
                'keterangan' => $request->keterangan
            ]);
            
            return redirect()->route('pengeluaran.index')
                ->with('success', 'Pengeluaran berhasil diperbarui');
            
        } catch (\Exception $e) {
            return redirect()->route('pengeluaran.index')
                ->with('error', 'Gagal memperbarui pengeluaran: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $pengeluaran = Pengeluaran::findOrFail($id);
            $pengeluaran->delete();
            
            return redirect()->route('pengeluaran.index')
                ->with('success', 'Pengeluaran berhasil dihapus');
            
        } catch (\Exception $e) {
            return redirect()->route('pengeluaran.index')
                ->with('error', 'Gagal menghapus pengeluaran: ' . $e->getMessage());
        }
    }
}