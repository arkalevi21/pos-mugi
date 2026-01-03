<?php

namespace App\Repositories\Pegawai;

use App\Models\Pegawai\Transaksi;
use Illuminate\Database\Eloquent\Collection;

class RiwayatRepository
{
    /**
     * Mengambil data transaksi berdasarkan tanggal
     * Eager load: User dan Detail Produk
     */
    public function getByDate(string $date): Collection
    {
        return Transaksi::with(['user', 'detailTransaksi.produk'])
            ->whereDate('tanggal', $date)
            ->orderBy('tanggal', 'desc')
            ->get();
    }

    /**
     * Menghitung total omzet pada tanggal tertentu
     */
    public function getTotalRevenueByDate(string $date): float
    {
        return Transaksi::whereDate('tanggal', $date)->sum('total_harga');
    }

    /**
     * Mengambil detail satu transaksi
     * Eager load lengkap sampai ke kategori produk
     */
    public function findWithDetails($id): ?Transaksi
    {
        // Perhatikan nesting relation: detail -> produk -> kategori
        return Transaksi::with(['user', 'detailTransaksi.produk.kategori'])
            ->find($id);
    }
}