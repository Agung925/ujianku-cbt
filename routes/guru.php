<?php

use App\Http\Controllers\Guru\SiswaManagementController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Guru Routes
|--------------------------------------------------------------------------
| Prefix: /guru
| Middleware: auth, role:guru
*/

Route::get('/dashboard', fn() => view('guru.dashboard'))->name('guru.dashboard');

// ===== SISWA MANAGEMENT (Wali Kelas) =====
Route::prefix('siswa')->name('guru.siswa.')->group(function () {
    Route::get('/', [SiswaManagementController::class, 'index'])->name('index');
    Route::get('/create', [SiswaManagementController::class, 'create'])->name('create');
    Route::post('/', [SiswaManagementController::class, 'store'])->name('store');
    Route::post('/{siswa}/upload-photo', [SiswaManagementController::class, 'uploadStudentPhoto'])->name('upload-photo');
});

// ===== SOAL =====
Route::prefix('soal')->name('guru.soal.')->group(function () {
    Route::get('/', fn() => view('guru.soal.index'))->name('index');
});

// ===== UJIAN =====
Route::prefix('ujian')->name('guru.ujian.')->group(function () {
    Route::get('/', fn() => view('guru.ujian.index'))->name('index');
});

// ===== HASIL =====
Route::prefix('hasil')->name('guru.hasil.')->group(function () {
    Route::get('/', fn() => view('guru.hasil.index'))->name('index');
});
