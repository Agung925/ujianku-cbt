<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Siswa Routes
|--------------------------------------------------------------------------
| Prefix: /siswa
| Middleware: auth, role:siswa
*/

Route::get('/dashboard', fn() => view('siswa.dashboard'))->name('siswa.dashboard');

Route::prefix('ujian')->name('siswa.ujian.')->group(function () {
    Route::get('/', fn() => response('Ujian Siswa Index'))->name('index');
});

Route::prefix('hasil')->name('siswa.hasil.')->group(function () {
    Route::get('/', fn() => response('Hasil Siswa Index'))->name('index');
});
