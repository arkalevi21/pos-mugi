<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Pegawai\KategoriController;
use App\Http\Controllers\Pegawai\ProductController;
use App\Http\Controllers\Pegawai\OperasionalController;
use App\Http\Controllers\Pegawai\RiwayatController;
use App\Http\Controllers\Pegawai\TransaksiController;
use App\Http\Controllers\Pegawai\TransaksiDetailController;
use App\Http\Controllers\Pemilik\AddPegawaiController;
use App\Http\Controllers\Pemilik\LaporanController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// ========== PUBLIC ROUTES ==========
Route::middleware(['guest'])->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

// ========== AUTHENTICATED ROUTES ==========
Route::middleware(['auth'])->group(function () {

    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // ========== AUTO REDIRECT BASED ON ROLE ==========
    Route::get('/', function () {
        $user = auth()->user();

        if ($user->role === 'admin') {
            return redirect()->route('pemilik.pegawai.index');
        }

        // Kasir langsung ke transaksi
        return redirect()->route('transaksi.create');
    })->name('home'); // Tambahkan nama route 'home'

    // ========== PEGAWAI ROUTES (KASIR & ADMIN) ==========
    Route::middleware(['role:kasir,admin'])->group(function () {

        // KATEGORI
        Route::prefix('kategori')->name('kategori.')->group(function () {
            Route::get('/', [KategoriController::class, 'index'])->name('index');
            Route::post('/', [KategoriController::class, 'store'])->name('store');
            Route::get('/{id}/edit', [KategoriController::class, 'edit'])->name('edit');
            Route::put('/{id}', [KategoriController::class, 'update'])->name('update');
            Route::delete('/{id}', [KategoriController::class, 'destroy'])->name('destroy');
        });

        // PRODUK
        Route::prefix('produk')->name('produk.')->group(function () {
            Route::get('/', [ProductController::class, 'index'])->name('index');
            Route::post('/', [ProductController::class, 'store'])->name('store');
            Route::get('/{id}/edit', [ProductController::class, 'edit'])->name('edit');
            Route::put('/{id}', [ProductController::class, 'update'])->name('update');
            Route::delete('/{id}', [ProductController::class, 'destroy'])->name('destroy');
        });

        // PENGELUARAN
        Route::prefix('pengeluaran')->name('pengeluaran.')->group(function () {
            Route::get('/', [OperasionalController::class, 'index'])->name('index');
            Route::post('/', [OperasionalController::class, 'store'])->name('store');
            Route::get('/{id}/edit', [OperasionalController::class, 'edit'])->name('edit');
            Route::put('/{id}', [OperasionalController::class, 'update'])->name('update');
            Route::delete('/{id}', [OperasionalController::class, 'destroy'])->name('destroy');
        });

        // RIWAYAT TRANSAKSI
        Route::prefix('riwayat')->name('riwayat.')->group(function () {
            Route::get('/', [RiwayatController::class, 'index'])->name('index');
            Route::get('/{id}', [RiwayatController::class, 'show'])->name('show');
        });

        // DETAIL TRANSAKSI
        Route::get('/transaksi-detail/{id}', [TransaksiDetailController::class, 'show'])
            ->name('transaksi.detail');
    });

    // ========== KASIR ONLY ROUTES ==========
    Route::middleware(['role:kasir'])->group(function () {
        // TRANSAKSI
        Route::prefix('transaksi')->name('transaksi.')->group(function () {
            Route::get('/create', [TransaksiController::class, 'create'])->name('create');
            Route::post('/', [TransaksiController::class, 'store'])->name('store');
            Route::get('/{id}/print', [TransaksiController::class, 'printStruk'])->name('print');
        });

        // CART ROUTES (Tanpa AJAX)
        Route::prefix('cart')->name('cart.')->group(function () {
            Route::post('/add', [TransaksiController::class, 'addToCart'])->name('add');
            Route::put('/update/{id}', [TransaksiController::class, 'updateCart'])->name('update');
            Route::delete('/remove/{id}', [TransaksiController::class, 'removeFromCart'])->name('remove');
            Route::delete('/clear', [TransaksiController::class, 'clearCart'])->name('clear');
        });
    });

    // ========== ADMIN ONLY ROUTES (PEMILIK) ==========
    Route::middleware(['role:admin'])->prefix('pemilik')->name('pemilik.')->group(function () {
        // MANAJEMEN PEGAWAI
        Route::prefix('pegawai')->name('pegawai.')->group(function () {
            Route::get('/', [AddPegawaiController::class, 'index'])->name('index');
            Route::post('/', [AddPegawaiController::class, 'store'])->name('store');
            Route::get('/{id}/edit', [AddPegawaiController::class, 'edit'])->name('edit');
            Route::put('/{id}', [AddPegawaiController::class, 'update'])->name('update');
            Route::delete('/{id}', [AddPegawaiController::class, 'destroy'])->name('destroy');
        });

        // LAPORAN
        Route::prefix('laporan')->name('laporan.')->group(function () {
            Route::get('/', [LaporanController::class, 'index'])->name('index');
            Route::get('/print', [LaporanController::class, 'print'])->name('print');
            Route::get('/export', [LaporanController::class, 'export'])->name('export');
        });
    });
});

// ========== ERROR PAGES ==========
Route::fallback(function () {
    return view('errors.404');
});
