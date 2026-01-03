<?php

namespace App\Repositories\Pegawai;

use App\Models\Pegawai\Kategori;
use Illuminate\Database\Eloquent\Collection;

class KategoriRepository
{
    public function create(array $data): Kategori
    {
        return Kategori::create($data);
    }

    public function readById($id): ?Kategori
    {
        return Kategori::find($id);
    }

    /**
     * Mengambil data kategori beserta jumlah produknya
     * Menggantikan Kategori::withCount('produk')->get()
     */
    public function getAllWithProductCount(): Collection
    {
        return Kategori::withCount('produk')->get();
    }

    public function readAll(): Collection
    {
        return Kategori::all();
    }

    public function update($id, array $data): bool
    {
        $kategori = Kategori::find($id);

        if ($kategori) {
            return $kategori->update($data);
        }

        return false;
    }

    public function destroy($id): void
    {
        Kategori::destroy($id);
    }

    /**
     * Cek apakah kategori punya relasi produk
     * Berguna untuk logic validasi sebelum delete
     */
    public function hasProducts($id): bool
    {
        $kategori = Kategori::find($id);
        return $kategori && $kategori->produk()->count() > 0;
    }
}