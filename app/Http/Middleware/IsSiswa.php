<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Carbon;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IsSiswa
{
    /**
     * Pastikan session siswa tersedia sebelum akses route siswa.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->session()->has('siswa_auth.id')) {
            return redirect()->route('siswa.login');
        }

        $loggedInAt = $request->session()->get('siswa_auth.logged_in_at');

        if ($loggedInAt && Carbon::parse($loggedInAt)->addHours(2)->isPast()) {
            $request->session()->forget('siswa_auth');

            return redirect()->route('siswa.login')->withErrors([
                'nis' => 'Sesi siswa telah berakhir. Silakan login ulang.',
            ]);
        }

        return $next($request);
    }
}
