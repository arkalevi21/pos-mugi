<?php

namespace App\Repositories\Pegawai;

use App\Models\Pegawai\Produk;
use Illuminate\Database\Eloquent\Collection;

class ProductRepository
{
    /**
     * Mengambil semua produk beserta relasi kategorinya
     */
    public function getAllWithCategory(): Collection
    {
        return Produk::with('kategori')->get();
    }

    public function findById($id): ?Produk
    {
        return Produk::find($id);
    }

    public function create(array $data): Produk
    {
        return Produk::create($data);
    }

    public function update(Produk $produk, array $data): bool
    {
        return $produk->update($data);
    }

    public function delete(Produk $produk): void
    {
        $produk->delete();
    }
}
