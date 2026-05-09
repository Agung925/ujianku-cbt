<?php

namespace App\Http\Middleware;

use Stancl\Tenancy\Database\Models\Tenant;
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
        if (Auth::check() && Auth::user()?->isAdmin()) {
            if (tenancy()->tenant === null) {
                $tenant = Tenant::first();

                if (! $tenant) {
                    $tenant = Tenant::create([
                        'id' => 'default-school',
                        'data' => [
                            'name' => config('app.name', 'UJIANKU-CBT') . ' Demo',
                        ],
                    ]);
                }

                tenancy()->initialize($tenant);
            }

            return $next($request);
        }

        if (tenancy()->tenant === null) {
            abort(403, 'Tenant context tidak ditemukan.');
        }

        return $next($request);
    }
}
