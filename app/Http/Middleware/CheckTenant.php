<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckTenant
{
    /**
     * Pastikan route tenant hanya diakses saat tenant context aktif.
     * Super admin dan admin dapat bypass untuk development purposes.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Super admin boleh akses global route tanpa tenant context.
        if (Auth::check() && Auth::user()?->isSuperAdmin()) {
            return $next($request);
        }

        // Admin dapat access admin routes tanpa explicit tenant context
        // (untuk development/localhost testing purposes)
        if (Auth::check() && Auth::user()?->hasRole('admin')) {
            return $next($request);
        }

        if (tenancy()->tenant === null) {
            abort(403, 'Tenant context tidak ditemukan.');
        }

        return $next($request);
    }
}
