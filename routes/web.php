<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\GuruController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\MuridController;
use App\Http\Controllers\JurusanController;
use App\Http\Controllers\DudikaController;
use App\Http\Controllers\MagangController;
use App\Http\Controllers\LaporanController;

Route::get('/', function () {
    return view('welcome');
})->name('welcome');

// Middleware Guest (Belum Login)
Route::middleware(['guest'])->group(function () {
    Route::get('/login', [HomeController::class, 'login'])->name('login');
    Route::post('/login', [HomeController::class, 'dologin'])->name('dologin');
    Route::get('/register', [HomeController::class, 'register'])->name('register');
    Route::post('/register', [HomeController::class, 'doregister'])->name('doregister');
});

// Middleware Auth (Sudah Login)
Route::middleware(['auth'])->group(function () {
    // Dashboard
    Route::get('home', [HomeController::class, 'index'])->name('home');

    // Profile Routes
    Route::prefix('profile')->group(function () {
        Route::get('/edit', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::put('/update', [ProfileController::class, 'update'])->name('profile.update');
    });

    // Guru Routes
    Route::prefix('guru')->group(function () {
        Route::post('/import', [GuruController::class, 'import'])->name('guru.import');
        Route::get('/download-template', [GuruController::class, 'downloadTemplate'])->name('guru.download-template');
        Route::delete('/multi-delete', [GuruController::class, 'multiDelete'])->name('guru.multi-delete');
        Route::delete('/delete-all', [GuruController::class, 'deleteAll'])->name('guru.delete-all');
    });
    Route::resource('guru', GuruController::class);

    // Dudika Routes
    Route::prefix('dudika')->group(function () {
        Route::post('/import', [DudikaController::class, 'import'])->name('dudika.import');
        Route::get('/download-template', [DudikaController::class, 'downloadTemplate'])->name('dudika.download-template');
        Route::delete('/multi-delete', [DudikaController::class, 'multiDelete'])->name('dudika.multi-delete');
        Route::delete('/delete-all', [DudikaController::class, 'deleteAll'])->name('dudika.delete-all');
    });
    Route::resource('dudika', DudikaController::class);

    // Murid Routes
    Route::prefix('murid')->group(function () {
        Route::post('/import', [MuridController::class, 'import'])->name('murid.import');
        Route::get('/download-template', [MuridController::class, 'downloadTemplate'])->name('murid.download-template');
        Route::delete('/multi-delete', [MuridController::class, 'multiDelete'])->name('murid.multi-delete');
        Route::delete('/delete-all', [MuridController::class, 'deleteAll'])->name('murid.delete-all');
    });
    Route::resource('murid', MuridController::class);

    // Jurusan Routes
    Route::prefix('jurusan')->group(function () {
        Route::post('/import', [JurusanController::class, 'import'])->name('jurusan.import');
        Route::get('/download-template', [JurusanController::class, 'downloadTemplate'])->name('jurusan.download-template');
        Route::delete('/multi-delete', [JurusanController::class, 'multiDelete'])->name('jurusan.multi-delete');
        Route::delete('/delete-all', [JurusanController::class, 'deleteAll'])->name('jurusan.delete-all');
    });
    Route::resource('jurusan', JurusanController::class);

    // Magang Routes
    Route::prefix('magang')->group(function () {
        Route::post('/import', [MagangController::class, 'import'])->name('magang.import');
        Route::get('/download-template', [MagangController::class, 'downloadTemplate'])->name('magang.download-template');
        Route::get('/export', [MagangController::class, 'export'])->name('magang.export');
        Route::delete('/multi-delete', [MagangController::class, 'multiDelete'])->name('magang.multi-delete');
        Route::delete('/delete-all', [MagangController::class, 'deleteAll'])->name('magang.delete-all');

        Route::delete('/{magang}', [MagangController::class, 'destroy'])->name('magang.destroy');
        Route::get('/{magang}/edit', [MagangController::class, 'edit'])->name('magang.edit');
        Route::post('/{id}/laporan', [MagangController::class, 'uploadLaporan']);
        Route::get('/{magang}/print-data', [MagangController::class, 'getPrintData'])->name('magang.print-data');
    });

    Route::get('/get-murid', [MagangController::class, 'getMurid'])->name('get.murid');

    Route::resource('magang', MagangController::class)->except(['show']);

    // Laporan Routes
    Route::post('/laporan', [LaporanController::class, 'store']);

    // Logout
    Route::get('/logout', [HomeController::class, 'logout'])->name('logout');
});

// Development/Testing Routes (Sebaiknya dihapus di production)
Route::get('/teskon', [HomeController::class, 'testcon']);
