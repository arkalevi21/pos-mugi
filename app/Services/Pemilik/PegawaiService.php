<?php

namespace App\Services\Pemilik;

use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Hash;
use DomainException;

class PegawaiService
{
    protected $userRepo;

    public function __construct(UserRepository $userRepo)
    {
        $this->userRepo = $userRepo;
    }

    public function getAllPegawai()
    {
        return $this->userRepo->getPegawai();
    }

    public function storePegawai(array $data)
    {
        $data['password'] = Hash::make($data['password']);
        return $this->userRepo->create($data);
    }

    public function updatePegawai($id, array $data)
    {
        if (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }
        return $this->userRepo->update($id, $data);
    }

    public function deletePegawai($id, $currentUserId)
    {
        if ($currentUserId == $id) {
            throw new DomainException('Tidak dapat menghapus akun sendiri');
        }

        if ($this->userRepo->hasTransactions($id)) {
            throw new DomainException('Pegawai tidak dapat dihapus karena memiliki riwayat transaksi');
        }

        return $this->userRepo->delete($id);
    }
}