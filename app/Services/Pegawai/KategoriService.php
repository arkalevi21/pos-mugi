<?php

namespace App\Services\Pegawai;

use App\Repositories\Pegawai\KategoriRepository;
use App\Models\Pegawai\Kategori;
use Exception;
use Illuminate\Database\Eloquent\Collection;

class KategoriService
{
    protected $kategoriRepository;

    public function __construct(KategoriRepository $kategoriRepository)
    {
        $this->kategoriRepository = $kategoriRepository;
    }

    public function getAllCategories(): Collection
    {
        return $this->kategoriRepository->getAllWithProductCount();
    }

    public function getCategoryById($id): ?Kategori
    {
        return $this->kategoriRepository->readById($id);
    }

    public function createCategory(array $data): Kategori
    {
        
        return $this->kategoriRepository->create($data);
    }

    public function updateCategory($id, array $data): bool
    {
        return $this->kategoriRepository->update($id, $data);
    }

    /**
     * Menghapus kategori dengan validasi bisnis logic
     * @throws Exception jika kategori masih digunakan
     */
    public function deleteCategory($id): void
    {
       
        if ($this->kategoriRepository->hasProducts($id)) {
            throw new Exception('Kategori tidak dapat dihapus karena masih digunakan oleh produk.');
        }

        $this->kategoriRepository->destroy($id);
    }
}