<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run()
    {
        // Cek apakah admin sudah ada
        if (!User::where('username', 'admin')->exists()) {
            User::create([
                'nama_user' => 'Admin Sistem',
                'username' => 'admin',
                'password' => Hash::make('password123'),
                'role' => 'admin',
            ]);
            echo "✅ Admin user created successfully!\n";
            echo "👤 Username: admin\n";
            echo "🔑 Password: password123\n";
        } else {
            echo "ℹ️ Admin user already exists!\n";
        }

        // Buat user kasir contoh
        if (!User::where('username', 'kasir')->exists()) {
            User::create([
                'nama_user' => 'Kasir Toko',
                'username' => 'kasir',
                'password' => Hash::make('password123'),
                'role' => 'kasir',
            ]);
            echo "\n✅ Kasir user created successfully!\n";
            echo "👤 Username: kasir\n";
            echo "🔑 Password: password123\n";
        } else {
            echo "\nℹ️ Kasir user already exists!\n";
        }

        // Buat user kasir 2 (opsional)
        if (!User::where('username', 'kasir2')->exists()) {
            User::create([
                'nama_user' => 'Kasir 2',
                'username' => 'kasir2',
                'password' => Hash::make('password123'),
                'role' => 'kasir',
            ]);
            echo "\n✅ Kasir 2 user created successfully!\n";
        }
    }
}
