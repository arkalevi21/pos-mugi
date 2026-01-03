<?php

namespace App\Services\Pegawai;

use App\Repositories\Pegawai\ProductRepository;
use App\Models\Pegawai\Produk;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;

class ProductService
{
    protected $produkRepository;

    public function __construct(ProductRepository $produkRepository)
    {
        $this->produkRepository = $produkRepository;
    }

    public function getAllProducts(): Collection
    {
        return $this->produkRepository->getAllWithCategory();
    }

    public function getProductById($id): ?Produk
    {
        return $this->produkRepository->findById($id);
    }

    /**
     * Handle logika pembuatan produk + upload gambar
     */
    public function createProduct(array $data, ?UploadedFile $file = null): Produk
    {
        // Set default values
        $data['stok'] = 0;
        $data['is_active'] = true;

        

        return $this->produkRepository->create($data);
    }

    /**
     * Handle logika update produk + ganti gambar (hapus lama, simpan baru)
     */
    public function updateProduct($id, array $data, ?UploadedFile $file = null): bool
    {
        $produk = $this->produkRepository->findById($id);

        if (!$produk) {
            return false;
        }

        

        return $this->produkRepository->update($produk, $data);
    }

    /**
     * Handle hapus produk + hapus fisik gambar
     */
    public function deleteProduct($id): void
    {
        $produk = $this->produkRepository->findById($id);

        if ($produk) {
            

            $this->produkRepository->delete($produk);
        }
    }

    /**
     * Helper private untuk menghapus file
     */
    
}