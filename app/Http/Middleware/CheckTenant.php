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
     * Admin dapat bypass untuk platform-level access.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Admin boleh akses global route tanpa tenant context (platform-level).
        if (Auth::check() && Auth::user()?->isAdmin()) {
            return $next($request);
        }

        if (tenancy()->tenant === null) {
            abort(403, 'Tenant context tidak ditemukan.');
        }

        return $next($request);
    }
}
