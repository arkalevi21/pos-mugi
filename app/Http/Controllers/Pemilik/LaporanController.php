<?php

namespace App\Http\Controllers\Pemilik;

use App\Http\Controllers\Controller;
use App\Models\Pegawai\Transaksi;
use App\Models\Pegawai\Pengeluaran;
use App\Models\Pegawai\DetailTransaksi;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class LaporanController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:admin');
    }
    
    public function index(Request $request)
    {
        // Default periode: bulan ini
        $startDate = $request->get('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', Carbon::now()->format('Y-m-d'));
        
        // Get transactions
        $transactions = Transaksi::with(['user', 'detailTransaksi.produk'])
            ->whereBetween('tanggal', [$startDate, $endDate . ' 23:59:59'])
            ->orderBy('tanggal', 'desc')
            ->get();
            
        // Get pengeluaran untuk periode yang sama
        $pengeluaran = Pengeluaran::whereBetween('tanggal', [$startDate, $endDate])
            ->orderBy('tanggal', 'desc')
            ->get();
            
        // Calculate statistics
        $totalTransaksi = $transactions->count();
        $totalPendapatan = $transactions->sum('total_harga');
        $totalPengeluaran = $pengeluaran->sum('nominal');
        $labaBersih = $totalPendapatan - $totalPengeluaran;
        $rataTransaksi = $totalTransaksi > 0 ? $totalPendapatan / $totalTransaksi : 0;
        
        // Payment methods
        $tunai = $transactions->where('metode_pembayaran', 'tunai')->count();
        $qris = $transactions->where('metode_pembayaran', 'qris')->count();
        
        // Top products
        $topProducts = DetailTransaksi::select('id_produk', DB::raw('SUM(qty) as total_qty'))
            ->whereHas('transaksi', function($query) use ($startDate, $endDate) {
                $query->whereBetween('tanggal', [$startDate, $endDate . ' 23:59:59']);
            })
            ->with('produk')
            ->groupBy('id_produk')
            ->orderBy('total_qty', 'desc')
            ->limit(5)
            ->get();
        
        return view('pemilik.laporan.index', compact(
            'transactions',
            'pengeluaran',
            'startDate',
            'endDate',
            'totalTransaksi',
            'totalPendapatan',
            'totalPengeluaran',
            'labaBersih',
            'rataTransaksi',
            'tunai',
            'qris',
            'topProducts'
        ));
    }
    
    public function print(Request $request)
    {
        $startDate = $request->get('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', Carbon::now()->format('Y-m-d'));
        
        $transactions = Transaksi::with(['user', 'detailTransaksi.produk'])
            ->whereBetween('tanggal', [$startDate, $endDate . ' 23:59:59'])
            ->orderBy('tanggal', 'desc')
            ->get();
            
        $pengeluaran = Pengeluaran::whereBetween('tanggal', [$startDate, $endDate])
            ->orderBy('tanggal', 'desc')
            ->get();
            
        $totalPendapatan = $transactions->sum('total_harga');
        $totalPengeluaran = $pengeluaran->sum('nominal');
        $labaBersih = $totalPendapatan - $totalPengeluaran;
        
        return view('pemilik.laporan.print', compact(
            'transactions',
            'pengeluaran',
            'startDate',
            'endDate',
            'totalPendapatan',
            'totalPengeluaran',
            'labaBersih'
        ));
    }
    
    public function export(Request $request)
    {
        // Untuk export Excel
        $startDate = $request->get('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', Carbon::now()->format('Y-m-d'));
        
        // Redirect ke print page dulu
        return redirect()->route('pemilik.laporan.print', [
            'start_date' => $startDate,
            'end_date' => $endDate
        ]);
    }
}