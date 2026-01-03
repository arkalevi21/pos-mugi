<?php

namespace App\Repositories\Pegawai;

use App\Models\Pegawai\Pengeluaran;
use Illuminate\Database\Eloquent\Collection;

class PengeluaranRepository
{
    /**
     * Mengambil data pengeluaran dengan opsi filter tanggal
     */
    public function getFiltered(array $filters): Collection
    {
        $query = Pengeluaran::orderBy('tanggal', 'desc');

        
        if (isset($filters['tanggal']) && !empty($filters['tanggal'])) {
            $query->whereDate('tanggal', $filters['tanggal']);
        }

        return $query->get();
    }

    public function findById($id): ?Pengeluaran
    {
        return Pengeluaran::find($id);
    }

    public function create(array $data): Pengeluaran
    {
        return Pengeluaran::create($data);
    }

    public function update($id, array $data): bool
    {
        $pengeluaran = Pengeluaran::find($id);

        if ($pengeluaran) {
            return $pengeluaran->update($data);
        }

        return false;
    }

    public function delete($id): void
    {
        Pengeluaran::destroy($id);
    }
}