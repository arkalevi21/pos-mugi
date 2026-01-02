<?php

namespace App\Http\Controllers\Pemilik;

use App\Http\Controllers\Controller;
use App\Models\Pegawai\Transaksi;
use App\Models\Pegawai\Pengeluaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LaporanController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:admin');
    }
    
    public function index()
    {
        return view('pemilik.laporan.index');
    }
    
    public function getData(Request $request)
    {
        $startDate = $request->get('start_date', date('Y-m-01'));
        $endDate = $request->get('end_date', date('Y-m-d'));
        
        // Get transactions
        $transactions = Transaksi::with(['user', 'detailTransaksi.produk'])
            ->whereBetween('tanggal', [$startDate, $endDate])
            ->orderBy('tanggal', 'desc')
            ->get();
            
        // Calculate statistics
        $totalTransaksi = $transactions->count();
        $totalPendapatan = $transactions->sum('total_harga');
        $rataTransaksi = $totalTransaksi > 0 ? $totalPendapatan / $totalTransaksi : 0;
        
        $tunai = $transactions->where('metode_pembayaran', 'tunai')->count();
        $qris = $transactions->where('metode_pembayaran', 'qris')->count();
        
        return response()->json([
            'success' => true,
            'stats' => [
                'total_transaksi' => $totalTransaksi,
                'total_pendapatan' => $totalPendapatan,
                'rata_transaksi' => $rataTransaksi,
                'tunai' => $tunai,
                'qris' => $qris
            ],
            'transactions' => $transactions
        ]);
    }
    
    public function print(Request $request)
    {
        $startDate = $request->get('start_date', date('Y-m-01'));
        $endDate = $request->get('end_date', date('Y-m-d'));
        
        $transactions = Transaksi::with(['user', 'detailTransaksi.produk'])
            ->whereBetween('tanggal', [$startDate, $endDate])
            ->orderBy('tanggal', 'desc')
            ->get();
            
        $totalPendapatan = $transactions->sum('total_harga');
        
        return view('pemilik.laporan.print', compact('transactions', 'startDate', 'endDate', 'totalPendapatan'));
    }
    
    public function export(Request $request)
    {
        // Untuk export Excel nanti
        return response()->json([
            'success' => true,
            'message' => 'Export feature coming soon'
        ]);
    }
}