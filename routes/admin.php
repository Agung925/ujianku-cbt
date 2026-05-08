<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Admin Sekolah Routes
|--------------------------------------------------------------------------
| Prefix: /admin
| Middleware: auth, role:admin
*/

Route::get('/dashboard', fn() => view('admin.dashboard'))->name('admin.dashboard');

Route::prefix('guru')->name('admin.guru.')->group(function () {
    Route::get('/', fn() => response('Guru Index'))->name('index');
});

Route::prefix('siswa')->name('admin.siswa.')->group(function () {
    Route::get('/', fn() => response('Siswa Index'))->name('index');
});

Route::prefix('laporan')->name('admin.laporan.')->group(function () {
    Route::get('/', fn() => response('Laporan Index'))->name('index');
});
