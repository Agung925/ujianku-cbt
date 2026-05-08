<?php

use App\Http\Controllers\SuperAdmin\DashboardController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Super Admin Routes
|--------------------------------------------------------------------------
| Prefix: /super-admin
| Middleware: auth, role:super_admin
*/

Route::get('/dashboard', [DashboardController::class, 'index'])->name('superadmin.dashboard');

Route::prefix('sekolah')->name('superadmin.sekolah.')->group(function () {
    Route::get('/', fn() => response('Sekolah Index'))->name('index');
});
