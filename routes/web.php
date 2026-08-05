<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Sementara masih pakai closure + return view(...) untuk route yang
| controllernya BELUM dibuat. Kalau controllernya sudah ada, tinggal
| ganti baris closure jadi [XxxController::class, 'index'] dsb.
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {

    // ==================== PROFILE (bawaan breeze/jetstream) ====================
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // ==================== PELANGGAN ====================
    Route::prefix('pelanggan')->name('pelanggan.')->group(function () {
        Route::get('/', function () {
            return view('pelanggan.index');
        })->name('index');
    });

    // ==================== PRODUK ====================
    Route::prefix('produk')->name('produk.')->group(function () {
        Route::get('/', function () {
            return view('produk.index');
        })->name('index');
    });

    // ==================== KATEGORI ====================
    Route::prefix('kategori')->name('kategori.')->group(function () {
        Route::get('/', function () {
            return view('kategori.index');
        })->name('index');
    });

    // ==================== TRANSAKSI ====================
    Route::prefix('transaksi')->name('transaksi.')->group(function () {
        Route::get('/kasir', function () {
            return view('transaksi.kasir');
        })->name('kasir');

        Route::get('/', function () {
            return view('transaksi.index');
        })->name('index');
    });

    // ==================== RETUR ====================
    Route::prefix('retur')->name('retur.')->group(function () {
        Route::get('/', function () {
            return view('retur.index');
        })->name('index');
    });

    // ==================== STOK ====================
    Route::prefix('stok')->name('stok.')->group(function () {
        Route::get('/', function () {
            return view('stok.index');
        })->name('index');
    });

    // ==================== STOK ADJUSTMENT ====================
    Route::prefix('stok-adjustment')->name('stok-adjustment.')->group(function () {
        Route::get('/', function () {
            return view('stok-adjustment.index');
        })->name('index');
    });

    // ==================== SUPPLIER ====================
    Route::prefix('supplier')->name('supplier.')->group(function () {
        Route::get('/', function () {
            return view('supplier.index');
        })->name('index');
    });

    // ==================== LAPORAN ====================
    Route::prefix('laporan')->name('laporan.')->group(function () {
        Route::get('/penjualan', function () {
            return view('laporan.penjualan');
        })->name('penjualan');

        Route::get('/stok', function () {
            return view('laporan.stok');
        })->name('stok');

        Route::get('/laba-rugi', function () {
            return view('laporan.laba-rugi');
        })->name('laba-rugi');
    });

    // ==================== ADMIN ====================
    // Catatan: nama route di sini SENGAJA tanpa akhiran ".index"
    // karena kolom `route` di tabel menu kamu isinya persis
    // "admin.mapping-user", "admin.mapping-group", dst (tanpa .index).
    Route::prefix('admin')->name('admin.')->group(function () {

        Route::get('/mapping-user', function () {
            return view('admin.mapping-user.index');
        })->name('mapping-user');

        Route::get('/mapping-group', function () {
            return view('admin.mapping-group.index');
        })->name('mapping-group');

        Route::get('/menu', function () {
            return view('admin.menu.index');
        })->name('menu.index');

        Route::get('/pegawai', function () {
            return view('admin.pegawai.index');
        })->name('pegawai.index');

    });

});

require __DIR__.'/auth.php';