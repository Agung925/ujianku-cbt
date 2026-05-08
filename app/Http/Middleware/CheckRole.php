<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Middleware role-based auth dengan parameter comma-separated.
     */
    public function handle(Request $request, Closure $next, string $roles): Response
    {
        $user = Auth::user();

        if (! $user) {
            abort(401, 'Unauthenticated.');
        }

        $roleList = collect(explode(',', $roles))
            ->map(fn (string $role) => trim($role))
            ->filter();

        if ($roleList->isEmpty()) {
            abort(403, 'Anda tidak memiliki role yang diizinkan.');
        }

        $allowed = false;

        foreach ($roleList as $role) {
            if (method_exists($user, 'hasRole') && $user->hasRole($role)) {
                $allowed = true;
                break;
            }
        }

        if (! $allowed) {
            abort(403, 'Anda tidak memiliki role yang diizinkan.');
        }

        return $next($request);
    }
}
