<?php

namespace App\Http\Controllers\Api\V1\Pemilik;

use App\Http\Controllers\Controller;
use App\Services\Pemilik\LaporanService;
use Illuminate\Http\Request;
use Carbon\Carbon;

class LaporanController extends Controller
{
    protected $laporanService;

    public function __construct(LaporanService $laporanService)
    {
        // $this->middleware('auth:sanctum'); // Gunakan ini jika sudah pakai token
        $this->laporanService = $laporanService;
    }

    public function index(Request $request)
    {
        $startDate = $request->get('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', Carbon::now()->format('Y-m-d'));

        $data = $this->laporanService->getLaporanData($startDate, $endDate);

        // Langsung return data kalkulasi dalam bentuk JSON
        return response()->json([
            'status' => 'success',
            'meta' => [
                'start_date' => $startDate,
                'end_date' => $endDate
            ],
            'data' => $data
        ]);
    }
}