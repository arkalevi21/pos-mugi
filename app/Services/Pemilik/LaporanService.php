<?php

namespace App\Services\Pemilik;

use App\Repositories\Pegawai\TransaksiRepository;
use App\Repositories\Pegawai\PengeluaranRepository;

class LaporanService
{
    protected $transaksiRepo;
    protected $pengeluaranRepo;
    private const END_OF_DAY = ' 23:59:59';

    
    public function __construct(
        TransaksiRepository $transaksiRepo,
        PengeluaranRepository $pengeluaranRepo
    ) {
        $this->transaksiRepo = $transaksiRepo;
        $this->pengeluaranRepo = $pengeluaranRepo;
    }

    public function getLaporanData($startDate, $endDate)
    {
        $endDateTime = $endDate . self::END_OF_DAY;

        
        $transactions = $this->transaksiRepo->getByDateRange($startDate, $endDateTime);
        $pengeluaran = $this->pengeluaranRepo->getByDateRange($startDate, $endDate);
        $topProducts = $this->transaksiRepo->getTopProducts($startDate, $endDateTime);

        $totalPendapatan = $transactions->sum('total_harga');
        $totalPengeluaran = $pengeluaran->sum('nominal');

        return [
            'transactions' => $transactions,
            'pengeluaran' => $pengeluaran,
            'topProducts' => $topProducts,
            'totalTransaksi' => $transactions->count(),
            'totalPendapatan' => $totalPendapatan,
            'totalPengeluaran' => $totalPengeluaran,
            'labaBersih' => $totalPendapatan - $totalPengeluaran,
            'rataTransaksi' => $transactions->count() > 0 ? $totalPendapatan / $transactions->count() : 0,
            'tunai' => $transactions->where('metode_pembayaran', 'tunai')->count(),
            'qris' => $transactions->where('metode_pembayaran', 'qris')->count(),
        ];
    }
}