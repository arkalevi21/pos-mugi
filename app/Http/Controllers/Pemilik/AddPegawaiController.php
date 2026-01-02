<?php

namespace App\Http\Controllers\Pemilik;

use App\Http\Controllers\Controller;
use App\Http\Requests\AddPegawaiRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AddPegawaiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $pegawai = User::whereIn('role', ['admin', 'kasir'])->get();
        return view('pemilik.pegawai.index', compact('pegawai'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(AddPegawaiRequest $request)
    {
        try {
            $pegawai = User::create([
                'nama_user' => $request->nama_user,
                'username' => $request->username,
                'password' => Hash::make($request->password),
                'role' => $request->role
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Pegawai berhasil ditambahkan',
                'data' => $pegawai
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menambahkan pegawai: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(AddPegawaiRequest $request, $id)
    {
        try {
            $pegawai = User::findOrFail($id);
            
            $data = [
                'nama_user' => $request->nama_user,
                'username' => $request->username,
                'role' => $request->role
            ];
            
            // Update password jika diisi
            if ($request->filled('password')) {
                $data['password'] = Hash::make($request->password);
            }
            
            $pegawai->update($data);
            
            return response()->json([
                'success' => true,
                'message' => 'Pegawai berhasil diperbarui',
                'data' => $pegawai
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui pegawai: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            // Cek apakah user yang login menghapus dirinya sendiri
            if (auth()->id() == $id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak dapat menghapus akun sendiri'
                ], 400);
            }
            
            $pegawai = User::findOrFail($id);
            
            // Cek apakah pegawai memiliki transaksi
            if ($pegawai->transaksi()->count() > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Pegawai tidak dapat dihapus karena memiliki riwayat transaksi'
                ], 400);
            }
            
            $pegawai->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'Pegawai berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus pegawai: ' . $e->getMessage()
            ], 500);
        }
    }

    // Tambahkan method edit()
    public function edit($id)
    {
        try {
            $pegawai = User::findOrFail($id);
            
            return response()->json([
                'success' => true,
                'data' => $pegawai
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Pegawai tidak ditemukan'
            ], 404);
        }
    }
}