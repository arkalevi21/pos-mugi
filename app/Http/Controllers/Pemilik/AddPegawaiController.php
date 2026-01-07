<?php

namespace App\Http\Controllers\Pemilik;

use App\Http\Controllers\Controller;
use App\Http\Requests\AddPegawaiRequest;
use App\Services\Pemilik\PegawaiService;
use Exception;

class AddPegawaiController extends Controller
{
    private const ROUTE_INDEX = 'pemilik.pegawai.index';
    private const ERROR_PREFIX = 'Gagal: ';

    protected $pegawaiService;

    public function __construct(PegawaiService $pegawaiService)
    {
        $this->pegawaiService = $pegawaiService;
    }

    public function index()
    {
        $pegawai = $this->pegawaiService->getAllPegawai();
        return view('pemilik.pegawai.index', compact('pegawai'));
    }

    public function store(AddPegawaiRequest $request)
    {
        try {
            $this->pegawaiService->storePegawai($request->validated());
            return redirect()->route(self::ROUTE_INDEX)->with('success', 'Pegawai berhasil ditambahkan');
        } catch (Exception $e) {
            return back()->withInput()->with('error', self::ERROR_PREFIX . $e->getMessage());
        }
    }

    public function update(AddPegawaiRequest $request, $id)
    {
        try {
            $this->pegawaiService->updatePegawai($id, $request->validated());
            return redirect()->route(self::ROUTE_INDEX)->with('success', 'Pegawai berhasil diperbarui');
        } catch (Exception $e) {
            return back()->withInput()->with('error', self::ERROR_PREFIX . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            $this->pegawaiService->deletePegawai($id, auth()->id());
            return redirect()->route(self::ROUTE_INDEX)->with('success', 'Pegawai berhasil dihapus');
        } catch (Exception $e) {
            return back()->with('error', self::ERROR_PREFIX . $e->getMessage());
        }
    }
}