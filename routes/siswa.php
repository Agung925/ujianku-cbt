<?php

use App\Http\Controllers\Siswa\ExamController;
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
    Route::get('/', [ExamController::class, 'index'])->name('index');
    Route::get('/{id}/start', [ExamController::class, 'startExam'])->name('start');
    Route::post('/{ujianId}/finish', [ExamController::class, 'finishExam'])->name('finish');
});

Route::prefix('hasil')->name('siswa.hasil.')->group(function () {
    Route::get('/', fn() => view('siswa.hasil.index'))->name('index');
});
