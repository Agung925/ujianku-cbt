<?php

namespace App\Http\Middleware;

use Closure;
use Carbon\Carbon;
use App\Models\JawabanSiswa;
use App\Models\Ujian;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class VerifyExamSession
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Only check for exam routes
        if (!$request->route()?->hasParameter('ujianId')) {
            return $next($request);
        }

        $user = Auth::user();
        if (!$user || !$user->hasRole('siswa') || !$user->siswa) {
            return response()->json(['error' => 'Tidak terautentikasi'], 401);
        }

        $tenantId = tenancy()->tenant?->id;
        $ujianId = $request->route('ujianId');

        // Verify exam exists and is active
        $ujian = Ujian::where('id', $ujianId)
            ->where('tenant_id', $tenantId)
            ->where('is_active', true)
            ->first();

        if (!$ujian) {
            return response()->json(['error' => 'Ujian tidak ditemukan atau tidak aktif'], 404);
        }

        // Check if exam time is valid
        $now = Carbon::now();
        if ($now < $ujian->tgl_mulai || $now > $ujian->tgl_selesai) {
            return response()->json(['error' => 'Ujian tidak tersedia pada waktu ini'], 422);
        }

        // Check if siswa already submitted this exam
        $submitted = JawabanSiswa::where('ujian_id', $ujianId)
            ->where('siswa_id', $user->siswa->id)
            ->where('is_submitted', true)
            ->exists();

        if ($submitted && $request->route()->getName() !== 'siswa.exam.view-results') {
            return response()->json(['error' => 'Anda sudah menyelesaikan ujian ini'], 422);
        }

        // Check for multiple window/tab sessions
        $sessionId = session('exam_session_id_' . $ujianId);
        if (!$sessionId) {
            session(['exam_session_id_' . $ujianId => uniqid('exam_', true)]);
        }

        return $next($request);
    }
}
