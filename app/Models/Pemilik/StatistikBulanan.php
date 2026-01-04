<?php

namespace App\Models\Pemilik;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class StatistikBulanan extends Model
{
    use HasFactory;
    
    // Model ini tidak punya tabel khusus, untuk query statistik saja
    
    public static function getPendapatanBulanan($tahun = null)
    {
        $tahun = $tahun ?? date('Y');
        
        return DB::table('transaksi')
            ->select(
                DB::raw('MONTH(tanggal) as bulan'),
                DB::raw('YEAR(tanggal) as tahun'),
                DB::raw('COUNT(*) as total_transaksi'),
                DB::raw('SUM(total_harga) as total_pendapatan')
            )
            ->whereYear('tanggal', $tahun)
            ->groupBy(DB::raw('MONTH(tanggal), YEAR(tanggal)'))
            ->orderBy('tahun', 'desc')
            ->orderBy('bulan', 'desc')
            ->get();
    }
}
