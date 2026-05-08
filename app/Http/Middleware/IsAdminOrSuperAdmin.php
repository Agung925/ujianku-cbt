<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class IsAdminOrSuperAdmin
{
    /**
     * Middleware untuk memverifikasi user memiliki role admin.
     * 
     * Deprecated: super_admin role merged into admin (2026-05-09)
     * This middleware now just checks for admin role.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        if (! $user || ! $user->isAdmin()) {
            abort(403, 'Akses khusus admin.');
        }

        return $next($request);
    }
}
