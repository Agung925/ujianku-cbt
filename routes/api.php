<?php

use App\Http\Controllers\Siswa\ExamController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
| Prefix: /api
| Middleware: api, auth:sanctum
*/

Route::middleware('auth:sanctum')->group(function () {
    // Exam API endpoints (protected by VerifyExamSession middleware)
    Route::prefix('ujian/{ujianId}')->middleware('verify.exam.session')->group(function () {
        Route::get('soal/{index}', [ExamController::class, 'getQuestion'])->name('api.exam.question');
        Route::post('answer', [ExamController::class, 'submitAnswer'])->name('api.exam.answer');
        Route::get('time-remaining', [ExamController::class, 'getTimeRemaining'])->name('api.exam.time-remaining');
        Route::post('finish', [ExamController::class, 'finishExam'])->name('api.exam.finish');
    });
});
