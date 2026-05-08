<?php

use App\Http\Controllers\Admin\GuruController;
use App\Http\Controllers\Admin\SiswaController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Admin Sekolah Routes
|--------------------------------------------------------------------------
| Prefix: /admin
| Middleware: auth, role:admin
*/

Route::get('/dashboard', fn() => view('admin.dashboard'))->name('admin.dashboard');

// ===== GURU MANAGEMENT =====
Route::prefix('guru')->name('admin.guru.')->group(function () {
    Route::get('/', [GuruController::class, 'index'])->name('index');
    Route::get('/create', [GuruController::class, 'create'])->name('create');
    Route::post('/', [GuruController::class, 'store'])->name('store');
    Route::get('/{guru}', [GuruController::class, 'show'])->name('show');
    Route::get('/{guru}/edit', [GuruController::class, 'edit'])->name('edit');
    Route::put('/{guru}', [GuruController::class, 'update'])->name('update');
    Route::delete('/{guru}', [GuruController::class, 'destroy'])->name('destroy');
    Route::post('/{guru}/upload-photo', [GuruController::class, 'uploadPhoto'])->name('upload-photo');
});

// ===== SISWA MANAGEMENT =====
Route::prefix('siswa')->name('admin.siswa.')->group(function () {
    Route::get('/', [SiswaController::class, 'index'])->name('index');
    Route::get('/create', [SiswaController::class, 'create'])->name('create');
    Route::post('/', [SiswaController::class, 'store'])->name('store');
    Route::get('/{siswa}', [SiswaController::class, 'show'])->name('show');
    Route::get('/{siswa}/edit', [SiswaController::class, 'edit'])->name('edit');
    Route::put('/{siswa}', [SiswaController::class, 'update'])->name('update');
    Route::delete('/{siswa}', [SiswaController::class, 'destroy'])->name('destroy');
    Route::post('/{siswa}/upload-photo', [SiswaController::class, 'uploadPhoto'])->name('upload-photo');
    Route::post('/{siswa}/activate', [SiswaController::class, 'activate'])->name('activate');
    Route::post('/{siswa}/deactivate', [SiswaController::class, 'deactivate'])->name('deactivate');
    Route::post('/{siswa}/reset-password', [SiswaController::class, 'resetPassword'])->name('reset-password');
});

// ===== LAPORAN =====
Route::prefix('laporan')->name('admin.laporan.')->group(function () {
    Route::get('/', fn() => response('Laporan Index'))->name('index');
});
