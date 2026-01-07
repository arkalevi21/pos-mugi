<?php

namespace App\Http\Controllers\Pemilik;

use App\Http\Controllers\Controller;
use App\Services\Pemilik\LaporanService;
use Illuminate\Http\Request;
use Carbon\Carbon;

class LaporanController extends Controller
{
    protected $laporanService;

    public function __construct(LaporanService $laporanService)
    {
        $this->middleware('auth');
        $this->middleware('role:admin');
        $this->laporanService = $laporanService;
    }

    public function index(Request $request)
    {
        $startDate = $request->get('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', Carbon::now()->format('Y-m-d'));

       
        $data = $this->laporanService->getLaporanData($startDate, $endDate);

        return view('pemilik.laporan.index', array_merge($data, [
            'startDate' => $startDate,
            'endDate' => $endDate
        ]));
    }

    public function print(Request $request)
    {
        $startDate = $request->get('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', Carbon::now()->format('Y-m-d'));

        $data = $this->laporanService->getLaporanData($startDate, $endDate);

        return view('pemilik.laporan.print', array_merge($data, [
            'startDate' => $startDate,
            'endDate' => $endDate
        ]));
    }

    public function export(Request $request)
    {
        
        return redirect()->route('pemilik.laporan.print', [
            'start_date' => $request->get('start_date'),
            'end_date' => $request->get('end_date')
        ]);
    }
}