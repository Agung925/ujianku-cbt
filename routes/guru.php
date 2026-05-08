<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Guru Routes
|--------------------------------------------------------------------------
| Prefix: /guru
| Middleware: auth, role:guru
*/

Route::get('/dashboard', fn() => view('guru.dashboard'))->name('guru.dashboard');

Route::prefix('soal')->name('guru.soal.')->group(function () {
    Route::get('/', fn() => response('Soal Index'))->name('index');
});

Route::prefix('ujian')->name('guru.ujian.')->group(function () {
    Route::get('/', fn() => response('Ujian Index'))->name('index');
});

Route::prefix('hasil')->name('guru.hasil.')->group(function () {
    Route::get('/', fn() => response('Hasil Index'))->name('index');
});
