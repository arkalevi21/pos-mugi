<?php

namespace App\Services\Pegawai;

use App\Repositories\Pegawai\RiwayatRepository;
use App\Models\Pegawai\Transaksi;
use Illuminate\Database\Eloquent\Collection;

class RiwayatService
{
    protected $transaksiRepository;

    public function __construct(RiwayatRepository $transaksiRepository)
    {
        $this->transaksiRepository = $transaksiRepository;
    }

    /**
     * Mengambil riwayat, jika tanggal kosong otomatis pakai hari ini
     */
    public function getDailyHistory(?string $date): array
    {
        // Business Logic: Default ke hari ini jika null
        $targetDate = $date ?? date('Y-m-d');

        return [
            'date' => $targetDate,
            'data' => $this->transaksiRepository->getByDate($targetDate),
            'total' => $this->transaksiRepository->getTotalRevenueByDate($targetDate)
        ];
    }

    public function getTransactionDetail($id): Transaksi
    {
        $transaksi = $this->transaksiRepository->findWithDetails($id);

        if (!$transaksi) {
            abort(404, 'Transaksi tidak ditemukan');
        }

        return $transaksi;
    }
}