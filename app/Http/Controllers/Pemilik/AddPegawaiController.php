<?php

namespace App\Http\Controllers\Pemilik;

use App\Http\Controllers\Controller;
use App\Http\Requests\AddPegawaiRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AddPegawaiController extends Controller
{
    private const ERROR_PREFIX = 'Gagal: ';
    private const ROUTE_INDEX = 'pemilik.pegawai.index';

    public function index()
    {
        $pegawai = User::whereIn('role', ['admin', 'kasir'])->get();
        return view('pemilik.pegawai.index', compact('pegawai'));
    }

    public function store(AddPegawaiRequest $request)
    {
        try {
            User::create([
                'nama_user' => $request->nama_user,
                'username'  => $request->username,
                'password'  => Hash::make($request->password),
                'role'      => $request->role
            ]);

            return redirect()->route(self::ROUTE_INDEX)
                ->with('success', 'Pegawai berhasil ditambahkan');
        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', self::ERROR_PREFIX . $e->getMessage());
        }
    }

    public function update(AddPegawaiRequest $request, $id)
    {
        try {
            $pegawai = User::findOrFail($id);

            $data = [
                'nama_user' => $request->nama_user,
                'username'  => $request->username,
                'role'      => $request->role
            ];

            if ($request->filled('password')) {
                $data['password'] = Hash::make($request->password);
            }

            $pegawai->update($data);

            return redirect()->route(self::ROUTE_INDEX)
                ->with('success', 'Pegawai berhasil diperbarui');
        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', self::ERROR_PREFIX . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            // 1. Cek User Sendiri
            if (auth()->id() == $id) {
                throw new \DomainException('Tidak dapat menghapus akun sendiri');
            }
            
            $pegawai = User::findOrFail($id);
            
            // 2. Cek Riwayat Transaksi
            if ($pegawai->transaksi()->exists()) {
                throw new \DomainException('Pegawai tidak dapat dihapus karena memiliki riwayat transaksi');
            }
            
            $pegawai->delete();
            
            return redirect()->route(self::ROUTE_INDEX)
                ->with('success', 'Pegawai berhasil dihapus');

        } catch (\Exception $e) {
            return back()->with('error', self::ERROR_PREFIX . $e->getMessage());
        }
    }
}
