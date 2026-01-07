<?php

namespace App\Repositories;

use App\Models\User;

class UserRepository
{
    public function getPegawai()
    {
        return User::whereIn('role', ['admin', 'kasir'])->get();
    }

    public function find($id)
    {
        return User::findOrFail($id);
    }

    public function create(array $data)
    {
        return User::create($data);
    }

    public function update($id, array $data)
    {
        $user = $this->find($id);
        $user->update($data);
        return $user;
    }

    public function delete($id)
    {
        return User::destroy($id);
    }

    public function hasTransactions($id)
    {
        return $this->find($id)->transaksi()->exists();
    }
}