<?php

use App\Http\Controllers\Guru\SiswaManagementController;
use App\Http\Controllers\Guru\KategoriUjianController;
use App\Http\Controllers\Guru\SoalController;
use App\Http\Controllers\Guru\SoalImportController;
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

// ===== KATEGORI UJIAN (READ ONLY) =====
Route::prefix('kategori-ujian')->name('guru.kategori-ujian.')->group(function () {
    Route::get('/', [KategoriUjianController::class, 'index'])->name('index');
    Route::get('/{kategoriUjian}', [KategoriUjianController::class, 'show'])->name('show');
});

// ===== SOAL =====
Route::prefix('soal')->name('guru.soal.')->group(function () {
    Route::get('/', [SoalController::class, 'index'])->name('index');
    Route::get('/create', [SoalController::class, 'create'])->name('create');
    Route::post('/', [SoalController::class, 'store'])->name('store');
    Route::get('/{soal}/edit', [SoalController::class, 'edit'])->name('edit');
    Route::patch('/{soal}', [SoalController::class, 'update'])->name('update');
    Route::delete('/{soal}', [SoalController::class, 'destroy'])->name('destroy');
    Route::post('/{soal}/duplicate', [SoalController::class, 'duplicate'])->name('duplicate');
    
    // ===== SOAL IMPORT =====
    Route::get('/import', [SoalImportController::class, 'showForm'])->name('import.form');
    Route::post('/import', [SoalImportController::class, 'import'])->name('import');
    Route::get('/template', [SoalImportController::class, 'downloadTemplate'])->name('template');
});

// ===== UJIAN =====
Route::prefix('ujian')->name('guru.ujian.')->group(function () {
    Route::get('/', fn() => view('guru.ujian.index'))->name('index');
});

// ===== HASIL =====
Route::prefix('hasil')->name('guru.hasil.')->group(function () {
    Route::get('/', fn() => view('guru.hasil.index'))->name('index');
});
