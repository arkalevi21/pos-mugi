<?php

namespace App\Http\Controllers\Api\V1\Pemilik;

use App\Http\Controllers\Controller;
use App\Http\Requests\AddPegawaiRequest; // Pastikan request ini return JSON validation error
use App\Services\Pemilik\PegawaiService;
use Illuminate\Http\Request;
use Exception;

class AddPegawaiController extends Controller
{
    protected $pegawaiService;

    public function __construct(PegawaiService $pegawaiService)
    {
        $this->pegawaiService = $pegawaiService;
    }

    public function index()
    {
        $pegawai = $this->pegawaiService->getAllPegawai();
        return response()->json(['status' => 'success', 'data' => $pegawai]);
    }

    public function store(AddPegawaiRequest $request)
    {
        try {
            $pegawai = $this->pegawaiService->storePegawai($request->validated());
            return response()->json([
                'status' => 'success',
                'message' => 'Pegawai berhasil ditambahkan',
                'data' => $pegawai
            ], 201);
        } catch (Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 400);
        }
    }

    public function update(AddPegawaiRequest $request, $id)
    {
        try {
            $this->pegawaiService->updatePegawai($id, $request->validated());
            return response()->json(['status' => 'success', 'message' => 'Pegawai berhasil diperbarui']);
        } catch (Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 400);
        }
    }

    public function destroy($id)
    {
        try {
            $this->pegawaiService->deletePegawai($id, auth()->id());
            return response()->json(['status' => 'success', 'message' => 'Pegawai berhasil dihapus']);
        } catch (Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 400);
        }
    }
}