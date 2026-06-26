<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\GuruController;
use App\Http\Controllers\Admin\ImportController;
use App\Http\Controllers\Admin\KomitmenInfakController;
/*
|--------------------------------------------------------------------------
| Controller Admin
|--------------------------------------------------------------------------
*/

use App\Http\Controllers\Admin\RayonController as AdminRayonController;
use App\Http\Controllers\Admin\RombelController;
use App\Http\Controllers\Admin\SiswaController;
use App\Http\Controllers\Admin\TagihanInfakController;
use App\Http\Controllers\Admin\TahunAjaranController;
use App\Http\Controllers\Ortu\DashboardController as OrtuDashboardController;
use App\Http\Controllers\Ortu\KomitmenInfakController as OrtuKomitmenInfakController;
use App\Http\Controllers\Ortu\PembayaranController as OrtuPembayaranController;
use App\Http\Controllers\PembayaranController;
/*
|--------------------------------------------------------------------------
| Controller Role Lain
|--------------------------------------------------------------------------
*/

use App\Http\Controllers\Petugas\DashboardController as PetugasDashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Rayon\DashboardController as RayonDashboardController;
use App\Http\Controllers\Rayon\SiswaController as RayonSiswaController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Redirect Root
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return redirect('/dashboard');
});

/*
|--------------------------------------------------------------------------
| Redirect Sesuai Role
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->get('/dashboard', function () {

    return match (Auth::user()->role) {

        'admin' => redirect()->route('admin.dashboard'),

        'petugas_infak' => redirect()->route('petugas.dashboard'),

        'pembimbing_rayon' => redirect()->route('rayon.dashboard'),

        'orang_tua' => redirect()->route('ortu.dashboard'),

        default => abort(403),

    };

})->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

/*
|--------------------------------------------------------------------------
| ADMIN
|--------------------------------------------------------------------------
*/

Route::prefix('admin')
    ->middleware(['auth', 'role:admin'])
    ->name('admin.')
    ->group(function () {

        /*
        |--------------------------------------------------------------------------
        | Dashboard
        |--------------------------------------------------------------------------
        */

        Route::get(
            '/dashboard',
            [DashboardController::class, 'index']
        )->name('dashboard');

        /*
        |--------------------------------------------------------------------------
        | Master Data
        |--------------------------------------------------------------------------
        */

        Route::get('import/{master}/template', [ImportController::class, 'template'])
            ->name('import.template');

        Route::post('import/{master}', [ImportController::class, 'import'])
            ->name('import.store');

        Route::resource(
            'tahun-ajaran',
            TahunAjaranController::class
        )->except(['show']);

        Route::resource(
            'guru',
            GuruController::class
        )->except(['show']);

        Route::resource('rayon', AdminRayonController::class)->except(['show']);

        Route::resource('rombel', RombelController::class)->except(['show']);

        Route::resource('siswa', SiswaController::class)->except(['show']);

        Route::resource('komitmen-infak', KomitmenInfakController::class)
            ->except(['show'])
            ->parameters(['komitmen-infak' => 'komitmenInfak']);

        Route::post('tagihan/generate', [TagihanInfakController::class, 'generate'])
            ->name('tagihan.generate');

        Route::resource('tagihan', TagihanInfakController::class)->except(['show']);

        Route::resource('pembayaran', PembayaranController::class)
            ->only(['index', 'create', 'store']);

        Route::patch('pembayaran/{pembayaran}/verify', [PembayaranController::class, 'verify'])
            ->name('pembayaran.verify');

    });

/*
|--------------------------------------------------------------------------
| PETUGAS INFAK
|--------------------------------------------------------------------------
*/

Route::prefix('petugas')
    ->middleware(['auth', 'role:petugas_infak'])
    ->name('petugas.')
    ->group(function () {

        Route::get(
            '/dashboard',
            [PetugasDashboardController::class, 'index']
        )->name('dashboard');

        Route::resource('pembayaran', PembayaranController::class)
            ->only(['index', 'create', 'store']);

        Route::patch('pembayaran/{pembayaran}/verify', [PembayaranController::class, 'verify'])
            ->name('pembayaran.verify');

    });

/*
|--------------------------------------------------------------------------
| PEMBIMBING RAYON
|--------------------------------------------------------------------------
*/

Route::prefix('rayon')
    ->middleware(['auth', 'role:pembimbing_rayon'])
    ->name('rayon.')
    ->group(function () {

        Route::get(
            '/dashboard',
            [RayonDashboardController::class, 'index']
        )->name('dashboard');

        Route::get('/siswa', [RayonSiswaController::class, 'index'])
            ->name('siswa.index');

    });

/*
|--------------------------------------------------------------------------
| ORANG TUA
|--------------------------------------------------------------------------
*/

Route::prefix('ortu')
    ->middleware(['auth', 'role:orang_tua'])
    ->name('ortu.')
    ->group(function () {

        Route::get(
            '/dashboard',
            [OrtuDashboardController::class, 'index']
        )->name('dashboard');

        Route::get('/komitmen-infak', [OrtuKomitmenInfakController::class, 'index'])
            ->name('komitmen.index');

        Route::put('/komitmen-infak', [OrtuKomitmenInfakController::class, 'update'])
            ->name('komitmen.update');

        Route::resource('pembayaran', OrtuPembayaranController::class)
            ->only(['index', 'create', 'store']);

    });

/*
|--------------------------------------------------------------------------
| Authentication Routes
|--------------------------------------------------------------------------
*/

require __DIR__.'/auth.php';
