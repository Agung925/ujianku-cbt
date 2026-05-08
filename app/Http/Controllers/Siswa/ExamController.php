<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Models\JawabanSiswa;
use App\Models\Soal;
use App\Models\Ujian;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ExamController extends Controller
{
    /**
     * Daftar ujian yang tersedia untuk siswa.
     */
    public function index(): View
    {
        $tenantId = tenancy()->tenant?->id;
        $user = Auth::user();

        $now = Carbon::now();

        // Get available exams: is_active=true, tgl_mulai <= now <= tgl_selesai
        $availableExams = Ujian::where('tenant_id', $tenantId)
            ->where('is_active', true)
            ->where('tgl_mulai', '<=', $now)
            ->where('tgl_selesai', '>=', $now)
            ->with(['kategoriUjian'])
            ->orderBy('tgl_mulai')
            ->get();

        // For each exam, check if siswa already took it
        $examStatuses = [];
        foreach ($availableExams as $exam) {
            $submission = JawabanSiswa::where('ujian_id', $exam->id)
                ->where('siswa_id', $user->siswa->id ?? null)
                ->first();

            $examStatuses[$exam->id] = [
                'started' => $submission ? true : false,
                'submitted' => $submission && $submission->is_submitted ? true : false,
            ];
        }

        return view('siswa.ujian.index', compact('availableExams', 'examStatuses'));
    }

    /**
     * Start exam - show exam interface.
     */
    public function startExam(int $id): View|RedirectResponse
    {
        $tenantId = tenancy()->tenant?->id;
        $user = Auth::user();
        $siswaId = $user->siswa?->id;

        if (!$tenantId || !$siswaId) {
            return redirect()->route('siswa.ujian.index')->with('error', 'Context tidak valid.');
        }

        $ujian = Ujian::where('id', $id)
            ->where('tenant_id', $tenantId)
            ->where('is_active', true)
            ->first();

        if (!$ujian) {
            return redirect()->route('siswa.ujian.index')->with('error', 'Ujian tidak ditemukan.');
        }

        $now = Carbon::now();
        if ($now < $ujian->tgl_mulai || $now > $ujian->tgl_selesai) {
            return redirect()->route('siswa.ujian.index')
                ->with('error', 'Ujian tidak tersedia pada waktu ini.');
        }

        // Check if siswa already finished this exam
        $existingSubmission = JawabanSiswa::where('ujian_id', $ujian->id)
            ->where('siswa_id', $siswaId)
            ->where('is_submitted', true)
            ->exists();

        if ($existingSubmission) {
            return redirect()->route('siswa.ujian.index')
                ->with('error', 'Anda sudah menyelesaikan ujian ini.');
        }

        // Load exam with questions
        $ujian->load(['soal' => fn($q) => $q->orderByPivot('urutan')]);

        // Get or create answer records for each question
        foreach ($ujian->soal as $soal) {
            $answer = JawabanSiswa::firstOrCreate(
                [
                    'tenant_id' => $tenantId,
                    'ujian_id' => $ujian->id,
                    'siswa_id' => $siswaId,
                    'soal_id' => $soal->id,
                ],
                [
                    'jawaban' => null,
                    'waktu_mulai' => Carbon::now(),
                    'is_submitted' => false,
                ]
            );
        }

        return view('siswa.exam.take', compact('ujian'));
    }

    /**
     * Get specific question via AJAX.
     */
    public function getQuestion(int $ujianId, int $index): JsonResponse
    {
        $tenantId = tenancy()->tenant?->id;
        $user = Auth::user();
        $siswaId = $user->siswa?->id;

        $ujian = Ujian::where('id', $ujianId)
            ->where('tenant_id', $tenantId)
            ->first();

        if (!$ujian) {
            return response()->json(['error' => 'Ujian tidak ditemukan'], 404);
        }

        $ujian->load(['soal' => fn($q) => $q->orderByPivot('urutan')]);

        if ($index < 0 || $index >= $ujian->soal->count()) {
            return response()->json(['error' => 'Index soal tidak valid'], 422);
        }

        $soal = $ujian->soal[$index];

        // Get siswa's current answer for this question
        $answer = JawabanSiswa::where('ujian_id', $ujianId)
            ->where('siswa_id', $siswaId)
            ->where('soal_id', $soal->id)
            ->first();

        return response()->json([
            'index' => $index,
            'total' => $ujian->soal->count(),
            'soal' => [
                'id' => $soal->id,
                'pertanyaan' => $soal->pertanyaan,
                'tipe_soal' => $soal->tipe_soal,
                'opsi' => [
                    'a' => $soal->opsi_a,
                    'b' => $soal->opsi_b,
                    'c' => $soal->opsi_c,
                    'd' => $soal->opsi_d,
                ],
                'bobot' => $soal->bobot,
            ],
            'jawaban_siswa' => $answer?->jawaban,
        ]);
    }

    /**
     * Submit answer via AJAX.
     */
    public function submitAnswer(Request $request, int $ujianId): JsonResponse
    {
        $tenantId = tenancy()->tenant?->id;
        $user = Auth::user();
        $siswaId = $user->siswa?->id;

        $validated = $request->validate([
            'soal_id' => ['required', 'integer', 'exists:soals,id'],
            'jawaban' => ['nullable', 'string', 'max:500'],
        ]);

        $ujian = Ujian::where('id', $ujianId)
            ->where('tenant_id', $tenantId)
            ->first();

        if (!$ujian) {
            return response()->json(['error' => 'Ujian tidak ditemukan'], 404);
        }

        // Update or create answer
        $answer = JawabanSiswa::updateOrCreate(
            [
                'tenant_id' => $tenantId,
                'ujian_id' => $ujianId,
                'siswa_id' => $siswaId,
                'soal_id' => $validated['soal_id'],
            ],
            [
                'jawaban' => $validated['jawaban'],
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Jawaban disimpan.',
            'soal_id' => $answer->soal_id,
        ]);
    }

    /**
     * Get time remaining via AJAX.
     */
    public function getTimeRemaining(int $ujianId): JsonResponse
    {
        $tenantId = tenancy()->tenant?->id;
        $user = Auth::user();
        $siswaId = $user->siswa?->id;

        $ujian = Ujian::where('id', $ujianId)
            ->where('tenant_id', $tenantId)
            ->first();

        if (!$ujian) {
            return response()->json(['error' => 'Ujian tidak ditemukan'], 404);
        }

        // Check if exam time has expired
        $now = Carbon::now();
        if ($now > $ujian->tgl_selesai) {
            return response()->json([
                'expired' => true,
                'message' => 'Waktu ujian telah berakhir.',
            ]);
        }

        $secondsRemaining = $now->diffInSeconds($ujian->tgl_selesai, false);

        // Also check individual exam session time
        $firstAnswer = JawabanSiswa::where('ujian_id', $ujianId)
            ->where('siswa_id', $siswaId)
            ->orderBy('waktu_mulai')
            ->first();

        $sessionStartTime = $firstAnswer?->waktu_mulai ?? $now;
        $sessionSecondsUsed = $sessionStartTime->diffInSeconds($now);
        $sessionSecondsRemaining = ($ujian->waktu_durasi * 60) - $sessionSecondsUsed;

        // Use whichever is shorter
        $timeRemaining = min($secondsRemaining, max(0, $sessionSecondsRemaining));

        return response()->json([
            'expired' => $timeRemaining <= 0,
            'seconds_remaining' => $timeRemaining,
            'minutes_remaining' => intdiv($timeRemaining, 60),
            'seconds_in_minute' => $timeRemaining % 60,
        ]);
    }

    /**
     * Finish exam - submit all answers and mark as completed.
     */
    public function finishExam(Request $request, int $ujianId): JsonResponse
    {
        $tenantId = tenancy()->tenant?->id;
        $user = Auth::user();
        $siswaId = $user->siswa?->id;

        if (!$tenantId || !$siswaId) {
            return response()->json(['error' => 'Context tidak valid'], 422);
        }

        $ujian = Ujian::where('id', $ujianId)
            ->where('tenant_id', $tenantId)
            ->first();

        if (!$ujian) {
            return response()->json(['error' => 'Ujian tidak ditemukan'], 404);
        }

        // Mark all answers as submitted
        $submitted = JawabanSiswa::where('ujian_id', $ujianId)
            ->where('siswa_id', $siswaId)
            ->update([
                'is_submitted' => true,
                'waktu_selesai' => Carbon::now(),
            ]);

        return response()->json([
            'success' => true,
            'message' => 'Ujian selesai. Jawaban Anda telah disimpan.',
            'redirect' => route('siswa.ujian.index'),
        ]);
    }
}
