<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\PegawaiController;
use App\Http\Controllers\Admin\MappingUserController;

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

    Route::prefix('admin')->name('admin.')->group(function () {

        Route::prefix('mapping-user')->name('mapping-user.')->group(function () {
            Route::get('/', [MappingUserController::class, 'index'])->name('index');
            Route::get('/users', [MappingUserController::class, 'users'])->name('users');
            Route::get('/{user}/menus', [MappingUserController::class, 'menus'])->name('menus');
            Route::post('/{user}/save', [MappingUserController::class, 'save'])->name('save');
        });

        Route::get('/mapping-group', function () {
            return view('admin.mapping-group.index');
        })->name('mapping-group');

        Route::get('/menu', function () {
            return view('admin.menu.index');
        })->name('menu.index');

         
        Route::prefix('pegawai')->name('pegawai.')->group(function () {
            Route::get('/', [PegawaiController::class, 'index'])->name('index');
            Route::get('/data', [PegawaiController::class, 'data'])->name('data');
            Route::get('/{pegawai}', [PegawaiController::class, 'show'])->name('show');
            Route::post('/', [PegawaiController::class, 'store'])->name('store');
            Route::put('/{pegawai}', [PegawaiController::class, 'update'])->name('update');
            Route::delete('/{pegawai}', [PegawaiController::class, 'destroy'])->name('destroy');
            Route::patch('/{pegawai}/toggle-active', [PegawaiController::class, 'toggleActive'])->name('toggle-active');
        });

    });

});

require __DIR__.'/auth.php';