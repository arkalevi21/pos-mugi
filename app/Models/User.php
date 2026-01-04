<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'users';
    protected $primaryKey = 'id_user'; // Sesuai dengan migration
    
    protected $fillable = [
        'nama_user',
        'username',
        'password',
        'role'
    ];
    
    protected $hidden = [
        'password',
        'remember_token',
    ];
    
    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }
    
    // ========== CUSTOM METHODS ==========
    
    public function isAdmin()
    {
        return $this->role === 'admin';
    }
    
    public function isKasir()
    {
        return $this->role === 'kasir';
    }
    
    // ========== RELATIONSHIPS ==========
    
    public function transaksi()
    {
        return $this->hasMany(\App\Models\Pegawai\Transaksi::class, 'id_user', 'id_user');
    }
}
