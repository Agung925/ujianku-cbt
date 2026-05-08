<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Route;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        then: function () {
            Route::middleware(['web', 'auth', 'checkRole:super_admin'])
                ->prefix('super-admin')
                ->group(base_path('routes/superadmin.php'));

            Route::middleware(['web', 'auth', 'checkTenant', 'checkRole:admin'])
                ->prefix('admin')
                ->group(base_path('routes/admin.php'));

            Route::middleware(['web', 'auth', 'checkTenant', 'checkRole:guru'])
                ->prefix('guru')
                ->group(base_path('routes/guru.php'));

            Route::middleware(['web', 'checkTenant', 'siswa.auth'])
                ->prefix('siswa')
                ->group(base_path('routes/siswa.php'));
        },
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'checkRole' => \App\Http\Middleware\CheckRole::class,
            'checkTenant' => \App\Http\Middleware\CheckTenant::class,
            'siswa.auth' => \App\Http\Middleware\IsSiswa::class,
            'isAdmin' => \App\Http\Middleware\IsAdmin::class,
            'isAdminOrSuperAdmin' => \App\Http\Middleware\IsAdminOrSuperAdmin::class,
            'verify.exam.session' => \App\Http\Middleware\VerifyExamSession::class,
            'role' => \Spatie\Permission\Middleware\RoleMiddleware::class,
            'permission' => \Spatie\Permission\Middleware\PermissionMiddleware::class,
            'role_or_permission' => \Spatie\Permission\Middleware\RoleOrPermissionMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
