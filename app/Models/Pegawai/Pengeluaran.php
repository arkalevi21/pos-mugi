<?php

namespace App\Models\Pegawai;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pengeluaran extends Model
{
    use HasFactory;
    
    protected $table = 'pengeluaran';
    protected $primaryKey = 'id_pengeluaran';
    
    protected $fillable = [
        'nama_pengeluaran',
        'nominal',
        'tanggal',
        'keterangan'
    ];
    
    protected $casts = [
        'tanggal' => 'date',
        'nominal' => 'integer'
    ];
}