<?php

namespace App\Http\Controllers\Pegawai;

use App\Http\Controllers\Controller;
use App\Services\Pegawai\RiwayatService;
use Illuminate\Http\Request;

class RiwayatController extends Controller
{
    protected $riwayatService;

    public function __construct(RiwayatService $riwayatService)
    {
        $this->riwayatService = $riwayatService;
    }

    /**
     * Menampilkan daftar riwayat transaksi harian.
     */
    public function index(Request $request)
    {
        
        $result = $this->riwayatService->getDailyHistory($request->get('tanggal'));

        
        $tanggal = $result['date'];
        $riwayat = $result['data'];
        $totalHariIni = $result['total'];

        return view('pegawai.riwayat.index', compact('riwayat', 'tanggal', 'totalHariIni'));
    }

    /**
     * Menampilkan detail struk/transaksi.
     */
    public function show($id)
    {
        
        $transaksi = $this->riwayatService->getTransactionDetail($id);

        return view('pegawai.riwayat.show', compact('transaksi'));
    }
}