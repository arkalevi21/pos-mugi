<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\Pemilik\AddPegawaiController;
use App\Http\Controllers\Api\V1\Pemilik\RiwayatController;
use App\Http\Controllers\Api\V1\Pegawai\ProductController;
use App\Http\Controllers\Api\V1\Pegawai\KategoriController;
use App\Http\Controllers\Api\V1\Pegawai\OperasionalController;
use App\Http\Controllers\Api\V1\Pegawai\TransaksiController;

/*

| URL Structure: http://localhost/api/v1/{endpoint}
|
*/

Route::prefix('v1')->group(function () {

    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/transaksi/callback', [TransaksiController::class, 'callback']);
    
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/user', function (Request $request) {
            return $request->user();
        });

       
        Route::prefix('admin')->group(function () {
            Route::apiResource('pegawai', AddPegawaiController::class);
            Route::get('/laporan', [RiwayatController::class, 'index']);
        });


       
        Route::prefix('kasir')->group(function () {
            Route::apiResource('produk', ProductController::class);
            Route::apiResource('kategori', KategoriController::class);
            Route::apiResource('pengeluaran', OperasionalController::class);
            Route::post('/transaksi', [TransaksiController::class, 'store']); 
            Route::get('/transaksi/{id}', [TransaksiController::class, 'show']); 
        });

    });

});
