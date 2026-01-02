<?php

namespace App\Models\Pegawai;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaksi extends Model
{
    use HasFactory;
    
    protected $table = 'transaksi';
    protected $primaryKey = 'id_transaksi';
    
    protected $fillable = [
        'id_user',
        'nama_pembeli',
        'metode_pembayaran',
        'uang_diterima',
        'uang_kembalian',
        'total_harga',
        'tanggal'
    ];
    
    protected $casts = [
        'tanggal' => 'datetime',
        'uang_diterima' => 'integer',
        'uang_kembalian' => 'integer',
        'total_harga' => 'integer'
    ];
    
    public function user()
    {
        return $this->belongsTo(\App\Models\User::class, 'id_user', 'id_user');
    }
    
    public function detailTransaksi()
    {
        return $this->hasMany(DetailTransaksi::class, 'id_transaksi', 'id_transaksi');
    }
}