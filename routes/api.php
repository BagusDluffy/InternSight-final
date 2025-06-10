<?php

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\UserController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\GuruController;
use App\Http\Controllers\API\MagangController;
use App\Http\Controllers\API\LaporanController;

// Grup route yang memerlukan autentikasi
Route::middleware('auth:sanctum')->group(function () {
    // Route untuk User
    Route::get('/users', [UserController::class, 'index']);
    Route::get('/validate-token', [AuthController::class, 'validateToken']);

    // Route untuk Guru
    Route::prefix('guru')->group(function () {
        Route::get('/', [GuruController::class, 'index']);
        Route::get('/{id}', [GuruController::class, 'show']);
        Route::post('/', [GuruController::class, 'store']);
        Route::put('/{id}', [GuruController::class, 'update']);
        Route::delete('/{id}', [GuruController::class, 'destroy']);
    });

    // Route untuk Magang
    Route::prefix('magang')->group(function () {
        Route::get('/', [MagangController::class, 'index']); // Daftar semua magang
        Route::get('/guru/{guruId}', [MagangController::class, 'getMagangByGuru']); // Magang berdasarkan guru
        Route::get('/students/{dudika_id}', [MagangController::class, 'getStudentsByDudika']); // Siswa berdasarkan dudika
    });

    Route::post('/laporan', [LaporanController::class, 'store']);
    Route::get('/laporan/magang/{magangId}', [LaporanController::class, 'getLaporanByMagangId']);
    Route::get('/laporan/guru/{guruId}', [LaporanController::class, 'getLaporanByGuru']);
    Route::get('/dudika', [LaporanController::class, 'getDudika']);
    Route::get('/magang', [LaporanController::class, 'getMagangId']);
    Route::get('/laporan', [LaporanController::class, 'index']);
});

// Route login tidak memerlukan autentikasi
Route::post('/login', [AuthController::class, 'login']);