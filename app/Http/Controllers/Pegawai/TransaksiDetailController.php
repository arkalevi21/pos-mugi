<?php

namespace App\Http\Controllers\Pegawai;

use App\Http\Controllers\Controller;
use App\Models\Pegawai\Transaksi;
use Illuminate\Http\Request;

class TransaksiDetailController extends Controller
{
    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $transaksi = Transaksi::with(['user', 'detailTransaksi.produk.kategori'])
            ->findOrFail($id);
            
        return view('pegawai.transaksi.detail', compact('transaksi'));
    }
}