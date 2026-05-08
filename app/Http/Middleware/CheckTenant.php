<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Stancl\Tenancy\Facades\Tenancy;
use Symfony\Component\HttpFoundation\Response;

class CheckTenant
{
    /**
     * Pastikan route tenant hanya diakses saat tenant context aktif.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Super admin boleh akses global route tanpa tenant context.
        if (Auth::check() && Auth::user()?->isSuperAdmin()) {
            return $next($request);
        }

        if (Tenancy::getTenant() === null) {
            abort(403, 'Tenant context tidak ditemukan.');
        }

        return $next($request);
    }
}
