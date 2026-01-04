<?php

namespace App\Services\Pegawai;

use App\Repositories\Pegawai\PengeluaranRepository;
use App\Models\Pegawai\Pengeluaran;
use Illuminate\Database\Eloquent\Collection;
use Exception;

class PengeluaranService
{
    protected $pengeluaranRepository;

    public function __construct(PengeluaranRepository $pengeluaranRepository)
    {
        $this->pengeluaranRepository = $pengeluaranRepository;
    }

    /**
     * Mengambil list pengeluaran berdasarkan filter request
     */
    public function getPengeluaranList(array $filters): Collection
    {
        return $this->pengeluaranRepository->getFiltered($filters);
    }

    /**
     * Menghitung total nominal dari collection yang ada
     */
    public function calculateTotal(Collection $pengeluaran): float
    {
        return $pengeluaran->sum('nominal');
    }

    public function getPengeluaranById($id): ?Pengeluaran
    {
        return $this->pengeluaranRepository->findById($id);
    }

    public function createPengeluaran(array $data): Pengeluaran
    {
        return $this->pengeluaranRepository->create($data);
    }

    public function updatePengeluaran($id, array $data): bool
    {
        return $this->pengeluaranRepository->update($id, $data);
    }

    public function deletePengeluaran($id): void
    {
        $this->pengeluaranRepository->delete($id);
    }
}
