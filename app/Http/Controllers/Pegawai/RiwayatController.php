<?php

namespace App\Http\Controllers\Pegawai;

use App\Http\Controllers\Controller;
use App\Models\Pegawai\Transaksi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RiwayatController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $tanggal = $request->get('tanggal', date('Y-m-d'));
        
        $riwayat = Transaksi::with(['user', 'detailTransaksi.produk'])
            ->whereDate('tanggal', $tanggal)
            ->orderBy('tanggal', 'desc')
            ->get();
            
        $totalHariIni = Transaksi::whereDate('tanggal', $tanggal)->sum('total_harga');
        
        return view('pegawai.riwayat.index', compact('riwayat', 'tanggal', 'totalHariIni'));
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $transaksi = Transaksi::with(['user', 'detailTransaksi.produk.kategori'])
            ->findOrFail($id);
            
        return view('pegawai.riwayat.show', compact('transaksi'));
    }
}