<?php

use App\Http\Controllers\Admin\GuruController;
use App\Http\Controllers\Admin\SiswaController;
use App\Http\Controllers\Admin\KategoriUjianController;
use App\Http\Controllers\Admin\DashboardController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Admin Routes (Platform & Tenant Admin)
|--------------------------------------------------------------------------
| Prefix: /admin
| Middleware: auth, role:admin
| Note: Merged from superadmin role (2026-05-09)
*/

Route::get('/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');
Route::get('/dashboard/statistics', [DashboardController::class, 'statisticsPage'])->name('admin.dashboard.statistics');
Route::get('/dashboard/chart-data', [DashboardController::class, 'chartData'])->name('admin.dashboard.chart-data');
Route::get('/dashboard/export', [DashboardController::class, 'exportStatistics'])->name('admin.dashboard.export');

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

// ===== KATEGORI UJIAN =====
Route::prefix('kategori-ujian')->name('admin.kategori-ujian.')->group(function () {
    Route::get('/', [KategoriUjianController::class, 'index'])->name('index');
    Route::get('/create', [KategoriUjianController::class, 'create'])->name('create');
    Route::post('/', [KategoriUjianController::class, 'store'])->name('store');
    Route::get('/{kategoriUjian}', [KategoriUjianController::class, 'show'])->name('show');
    Route::get('/{kategoriUjian}/edit', [KategoriUjianController::class, 'edit'])->name('edit');
    Route::patch('/{kategoriUjian}', [KategoriUjianController::class, 'update'])->name('update');
    Route::delete('/{kategoriUjian}', [KategoriUjianController::class, 'destroy'])->name('destroy');
});

// ===== LAPORAN =====
Route::prefix('laporan')->name('admin.laporan.')->group(function () {
    Route::get('/', fn() => view('admin.laporan.index'))->name('index');
});

// ===== TENANT MANAGEMENT (Platform Admin) =====
Route::prefix('sekolah')->name('admin.sekolah.')->group(function () {
    Route::get('/', fn() => view('admin.sekolah.index'))->name('index');
});
