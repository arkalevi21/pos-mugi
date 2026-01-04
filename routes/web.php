<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Pegawai\KategoriController;
use App\Http\Controllers\Pegawai\ProductController;
use App\Http\Controllers\Pegawai\OperasionalController;
use App\Http\Controllers\Pegawai\TransaksiController;
use App\Http\Controllers\Pemilik\AddPegawaiController;
use App\Http\Controllers\Pemilik\LaporanController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// --- SONARQUBE FIX: Variabel untuk menghindari duplikasi string literal ---
$idPath = '/{id}';
$editPath = '/{id}/edit';

// ========== PUBLIC ROUTES ==========
Route::middleware(['guest'])->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

Route::post('/midtrans/callback', [TransaksiController::class, 'callback'])->name('midtrans.callback');

// ========== AUTHENTICATED ROUTES ==========
Route::middleware(['auth'])->group(function () use ($idPath, $editPath) { // Pass variable ke scope closure jika perlu (optional di level global, tapi aman)

    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // ========== AUTO REDIRECT BASED ON ROLE ==========
    Route::get('/', function () {
        $user = auth()->user();

        if ($user->role === 'admin') {
            return redirect()->route('pemilik.pegawai.index');
        }

        // Kasir langsung ke transaksi
        return redirect()->route('transaksi.create');
    })->name('home');

    // ========== PEGAWAI ROUTES (KASIR & ADMIN) ==========
    Route::middleware(['role:kasir,admin'])->group(function () use ($idPath, $editPath) {

        // KATEGORI
        Route::prefix('kategori')->name('kategori.')->group(function () use ($idPath, $editPath) {
            Route::get('/', [KategoriController::class, 'index'])->name('index');
            Route::post('/', [KategoriController::class, 'store'])->name('store');
            
            // Gunakan variabel
            Route::get($editPath, [KategoriController::class, 'edit'])->name('edit');
            Route::put($idPath, [KategoriController::class, 'update'])->name('update');
            Route::delete($idPath, [KategoriController::class, 'destroy'])->name('destroy');
        });

        // PRODUK
        Route::prefix('produk')->name('produk.')->group(function () use ($idPath, $editPath) {
            Route::get('/', [ProductController::class, 'index'])->name('index');
            Route::post('/', [ProductController::class, 'store'])->name('store');
            
            // Gunakan variabel
            Route::get($editPath, [ProductController::class, 'edit'])->name('edit');
            Route::put($idPath, [ProductController::class, 'update'])->name('update');
            Route::delete($idPath, [ProductController::class, 'destroy'])->name('destroy');
        });

        // PENGELUARAN
        Route::prefix('pengeluaran')->name('pengeluaran.')->group(function () use ($idPath, $editPath) {
            Route::get('/', [OperasionalController::class, 'index'])->name('index');
            Route::post('/', [OperasionalController::class, 'store'])->name('store');
            
            // Gunakan variabel
            Route::get($editPath, [OperasionalController::class, 'edit'])->name('edit');
            Route::put($idPath, [OperasionalController::class, 'update'])->name('update');
            Route::delete($idPath, [OperasionalController::class, 'destroy'])->name('destroy');
        });
    });

    // ========== KASIR ONLY ROUTES ==========
    Route::middleware(['role:kasir'])->group(function () use ($idPath) {
        // Note: String kompleks gabungan tidak selalu harus diubah, kecuali SonarQube komplain spesifik
        Route::get('/transaksi/{id}/finish-qris', [TransaksiController::class, 'finishQris'])->name('transaksi.finish_qris');

        // TRANSAKSI
        Route::prefix('transaksi')->name('transaksi.')->group(function () use ($idPath) {
            Route::get('/create', [TransaksiController::class, 'create'])->name('create');
            Route::post('/', [TransaksiController::class, 'store'])->name('store');
            // String concat agar tetap DRY
            Route::get($idPath . '/print', [TransaksiController::class, 'printStruk'])->name('print');
        });

        // CART ROUTES (Tanpa AJAX)
        Route::prefix('cart')->name('cart.')->group(function () use ($idPath) {
            Route::post('/add', [TransaksiController::class, 'addToCart'])->name('add');
            Route::put('/update' . $idPath, [TransaksiController::class, 'updateCart'])->name('update');
            Route::delete('/remove' . $idPath, [TransaksiController::class, 'removeFromCart'])->name('remove');
            Route::delete('/clear', [TransaksiController::class, 'clearCart'])->name('clear');
        });
    });

    // ========== ADMIN ONLY ROUTES (PEMILIK) ==========
    Route::middleware(['role:admin'])->prefix('pemilik')->name('pemilik.')->group(function () use ($idPath, $editPath) {
        // MANAJEMEN PEGAWAI
        Route::prefix('pegawai')->name('pegawai.')->group(function () use ($idPath, $editPath) {
            Route::get('/', [AddPegawaiController::class, 'index'])->name('index');
            Route::post('/', [AddPegawaiController::class, 'store'])->name('store');

            // Gunakan variabel
            Route::get($editPath, [AddPegawaiController::class, 'edit'])->name('edit');
            Route::put($idPath, [AddPegawaiController::class, 'update'])->name('update');
            Route::delete($idPath, [AddPegawaiController::class, 'destroy'])->name('destroy');
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
