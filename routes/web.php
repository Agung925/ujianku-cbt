<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// ===== HOME / REDIRECT LOGIC =====
Route::get('/', function () {
    // Jika sudah login, redirect ke dashboard sesuai role
    if (Auth::check()) {
        $user = Auth::user();
        
        if ($user->hasRole('super_admin')) {
            return redirect()->route('superadmin.dashboard');
        } elseif ($user->hasRole('admin')) {
            return redirect()->route('admin.dashboard');
        } elseif ($user->hasRole('guru')) {
            return redirect()->route('guru.dashboard');
        } elseif ($user->hasRole('siswa')) {
            return redirect()->route('siswa.dashboard');
        }
    }
    
    // Belum login, redirect ke halaman login
    return redirect()->route('login');
})->name('home');

// ===== DYNAMIC DASHBOARD REDIRECT =====
Route::get('/dashboard', function () {
    $user = Auth::user();
    
    if ($user->hasRole('super_admin')) {
        return redirect()->route('superadmin.dashboard');
    } elseif ($user->hasRole('admin')) {
        return redirect()->route('admin.dashboard');
    } elseif ($user->hasRole('guru')) {
        return redirect()->route('guru.dashboard');
    } elseif ($user->hasRole('siswa')) {
        return redirect()->route('siswa.dashboard');
    }
    
    // Fallback
    return redirect('/');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
