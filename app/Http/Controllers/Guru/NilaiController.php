<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\JawabanSiswa;
use App\Models\Nilai;
use App\Models\Siswa;
use App\Models\Soal;
use App\Models\Ujian;
use App\Services\GradingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class NilaiController extends Controller
{
    protected GradingService $gradingService;

    public function __construct(GradingService $gradingService)
    {
        $this->gradingService = $gradingService;
    }

    /**
     * List all exams with grading status.
     */
    public function index(): View
    {
        $user = Auth::user();
        $tenantId = tenancy()->tenant?->id;

        $ujians = Ujian::where('tenant_id', $tenantId)
            ->where('guru_id', $user->guru->id)
            ->with(['kategoriUjian', 'soal'])
            ->orderByDesc('tgl_selesai')
            ->paginate(15);

        // Get grading summary for each exam
        $summaries = [];
        foreach ($ujians as $ujian) {
            $summaries[$ujian->id] = $this->gradingService->getGradingSummary($ujian->id);
        }

        return view('guru.nilai.index', compact('ujians', 'summaries'));
    }

    /**
     * Show exam with all student answers for grading.
     */
    public function gradeExam(int $ujianId): View|RedirectResponse
    {
        $user = Auth::user();
        $tenantId = tenancy()->tenant?->id;

        // Verify ownership
        $ujian = Ujian::where('id', $ujianId)
            ->where('tenant_id', $tenantId)
            ->where('guru_id', $user->guru->id)
            ->with(['soal', 'kategoriUjian'])
            ->first();

        if (!$ujian) {
            return redirect()->route('guru.nilai.index')
                ->with('error', 'Ujian tidak ditemukan.');
        }

        // Get all student answers for this exam
        $nilaiRecords = Nilai::where('ujian_id', $ujianId)
            ->with(['siswa'])
            ->orderBy('siswa_id')
            ->get();

        // Group answers by student
        $studentAnswers = [];
        foreach ($nilaiRecords as $nilai) {
            $answers = JawabanSiswa::where('ujian_id', $ujianId)
                ->where('siswa_id', $nilai->siswa_id)
                ->with('soal')
                ->orderByRaw('CAST(soal_id AS INTEGER)')
                ->get();

            $studentAnswers[$nilai->siswa_id] = [
                'siswa' => $nilai->siswa,
                'nilai' => $nilai,
                'answers' => $answers,
            ];
        }

        $summary = $this->gradingService->getGradingSummary($ujianId);

        return view('guru.nilai.grade-exam', compact('ujian', 'studentAnswers', 'summary'));
    }

    /**
     * Show specific question with all student answers for that question.
     */
    public function gradeQuestion(int $ujianId, int $soalId): View|RedirectResponse
    {
        $user = Auth::user();
        $tenantId = tenancy()->tenant?->id;

        // Verify ownership
        $ujian = Ujian::where('id', $ujianId)
            ->where('tenant_id', $tenantId)
            ->where('guru_id', $user->guru->id)
            ->first();

        if (!$ujian) {
            return redirect()->route('guru.nilai.index')
                ->with('error', 'Ujian tidak ditemukan.');
        }

        // Get the question
        $soal = Soal::find($soalId);
        if (!$soal) {
            return redirect()->route('guru.nilai.grade-exam', $ujianId)
                ->with('error', 'Soal tidak ditemukan.');
        }

        // Check if soal belongs to this exam
        $belongsToExam = $ujian->soal()->where('soal_id', $soalId)->exists();
        if (!$belongsToExam) {
            return redirect()->route('guru.nilai.grade-exam', $ujianId)
                ->with('error', 'Soal tidak ada dalam ujian ini.');
        }

        // Get all student answers for this question
        $studentAnswers = JawabanSiswa::where('ujian_id', $ujianId)
            ->where('soal_id', $soalId)
            ->with('siswa')
            ->orderBy('siswa_id')
            ->get();

        return view('guru.nilai.grade-question', compact('ujian', 'soal', 'studentAnswers'));
    }

    /**
     * Submit grade for a student answer (AJAX or Form).
     */
    public function submitGrade(Request $request, int $ujianId): JsonResponse|RedirectResponse
    {
        $user = Auth::user();
        $tenantId = tenancy()->tenant?->id;

        // Verify ownership
        $ujian = Ujian::where('id', $ujianId)
            ->where('tenant_id', $tenantId)
            ->where('guru_id', $user->guru->id)
            ->first();

        if (!$ujian) {
            return $this->respondError('Ujian tidak ditemukan.', $request);
        }

        $validated = $request->validate([
            'siswa_id' => ['required', 'integer'],
            'soal_id' => ['required', 'integer'],
            'nilai' => ['required', 'numeric', 'min:0', 'max:100'],
        ]);

        try {
            // Get Nilai record
            $nilai = Nilai::where('ujian_id', $ujianId)
                ->where('siswa_id', $validated['siswa_id'])
                ->first();

            if (!$nilai) {
                return $this->respondError('Nilai siswa tidak ditemukan.', $request);
            }

            // Get soal to check type
            $soal = Soal::find($validated['soal_id']);
            if (!$soal) {
                return $this->respondError('Soal tidak ditemukan.', $request);
            }

            // Ensure soal belongs to ujian
            if (!$ujian->soal()->where('soal_id', $validated['soal_id'])->exists()) {
                return $this->respondError('Soal tidak ada dalam ujian ini.', $request);
            }

            // Save essay grade
            if ($soal->tipe_soal === 'essay') {
                $nilai->nilai_essay = $this->gradingService->calculateScoreEssay(
                    $ujianId,
                    $validated['siswa_id'],
                    (float) $validated['nilai']
                );
                $nilai->status = 'nilai_essay_diinput';
                $nilai->save();

                return $this->respondSuccess('Nilai essay berhasil disimpan.', $request);
            }

            return $this->respondError('Tipe soal tidak didukung untuk grading manual.', $request);
        } catch (\Exception $e) {
            \Log::error('[NilaiController] Error submitting grade', [
                'ujian_id' => $ujianId,
                'error' => $e->getMessage(),
            ]);
            return $this->respondError('Gagal menyimpan nilai: ' . $e->getMessage(), $request);
        }
    }

    /**
     * Publish/finalize grades for an exam.
     */
    public function publishGrades(int $ujianId): RedirectResponse
    {
        $user = Auth::user();
        $tenantId = tenancy()->tenant?->id;

        // Verify ownership
        $ujian = Ujian::where('id', $ujianId)
            ->where('tenant_id', $tenantId)
            ->where('guru_id', $user->guru->id)
            ->first();

        if (!$ujian) {
            return redirect()->route('guru.nilai.index')
                ->with('error', 'Ujian tidak ditemukan.');
        }

        try {
            // Finalize all grades
            $updatedCount = $this->gradingService->finalizeGrades($ujianId);

            return redirect()->route('guru.nilai.grade-exam', $ujianId)
                ->with('success', "$updatedCount nilai siswa berhasil difinalisasi dan dipublikasikan.");
        } catch (\Exception $e) {
            \Log::error('[NilaiController] Error publishing grades', [
                'ujian_id' => $ujianId,
                'error' => $e->getMessage(),
            ]);
            return redirect()->route('guru.nilai.grade-exam', $ujianId)
                ->with('error', 'Gagal mempublikasikan nilai: ' . $e->getMessage());
        }
    }

    /**
     * Respond with success (JSON or redirect).
     */
    private function respondSuccess(string $message, Request $request)
    {
        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => $message]);
        }
        return redirect()->back()->with('success', $message);
    }

    /**
     * Respond with error (JSON or redirect).
     */
    private function respondError(string $message, Request $request)
    {
        if ($request->expectsJson()) {
            return response()->json(['success' => false, 'message' => $message], 422);
        }
        return redirect()->back()->with('error', $message);
    }
}
