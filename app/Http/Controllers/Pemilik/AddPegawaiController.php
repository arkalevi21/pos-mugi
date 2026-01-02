<?php

namespace App\Http\Controllers\Pemilik;

use App\Http\Controllers\Controller;
use App\Http\Requests\AddPegawaiRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AddPegawaiController extends Controller
{
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
            
            // Redirect dengan pesan sukses
            return redirect()->route('pemilik.pegawai.index')
                ->with('success', 'Pegawai berhasil ditambahkan');

        } catch (\Exception $e) {
            // Redirect kembali dengan input lama dan pesan error
            return back()->withInput()->with('error', 'Gagal: ' . $e->getMessage());
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
            
            return redirect()->route('pemilik.pegawai.index')
                ->with('success', 'Pegawai berhasil diperbarui');

        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Gagal: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            if (auth()->id() == $id) {
                return back()->with('error', 'Tidak dapat menghapus akun sendiri');
            }
            
            $pegawai = User::findOrFail($id);
            
            if ($pegawai->transaksi()->count() > 0) {
                return back()->with('error', 'Pegawai tidak dapat dihapus karena memiliki riwayat transaksi');
            }
            
            $pegawai->delete();
            
            return redirect()->route('pemilik.pegawai.index')
                ->with('success', 'Pegawai berhasil dihapus');

        } catch (\Exception $e) {
            return back()->with('error', 'Gagal: ' . $e->getMessage());
        }
    }
    
    // Method edit() TIDAK DIPERLUKAN LAGI jika tidak pakai AJAX
    // Kita akan kirim data lewat atribut HTML di View
}