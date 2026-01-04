<?php

namespace App\Repositories\Pegawai;

use App\Models\Pegawai\Transaksi;
use App\Models\Pegawai\DetailTransaksi;
use Illuminate\Database\Eloquent\Collection;

class TransaksiRepository
{
    public function getByDate(string $date): Collection
    {
        return Transaksi::with(['user', 'detailTransaksi.produk'])
            ->whereDate('tanggal', $date)
            ->orderBy('tanggal', 'desc')
            ->get();
    }

    public function getTotalRevenueByDate(string $date): float
    {
        return Transaksi::whereDate('tanggal', $date)->sum('total_harga');
    }

    public function findWithDetails($id): ?Transaksi
    {
        return Transaksi::with(['user', 'detailTransaksi.produk.kategori'])
            ->find($id);
    }

    public function findById($id): ?Transaksi
    {
        return Transaksi::find($id);
    }


    public function create(array $data): Transaksi
    {
        return Transaksi::create($data);
    }

    public function createDetail(array $data): DetailTransaksi
    {
        return DetailTransaksi::create($data);
    }

    public function update(Transaksi $transaksi, array $data): bool
    {
        return $transaksi->update($data);
    }
}
