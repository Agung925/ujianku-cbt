<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class IsAdminOrSuperAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        if (! $user || ! ($user->isAdmin() || $user->isSuperAdmin())) {
            abort(403, 'Akses khusus admin atau super admin.');
        }

        return $next($request);
    }
}
