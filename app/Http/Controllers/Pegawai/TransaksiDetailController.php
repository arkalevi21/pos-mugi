<?php

namespace App\Http\Controllers\Pegawai;

use App\Http\Controllers\Controller;
use App\Services\Pegawai\TransaksiService;

class TransaksiDetailController extends Controller
{
    protected $transaksiService;

    public function __construct(TransaksiService $transaksiService)
    {
        $this->transaksiService = $transaksiService;
    }

    public function show($id)
    {
        
        $transaksi = $this->transaksiService->getTransactionDetail($id);

        return view('pegawai.transaksi.detail', compact('transaksi'));
    }
}