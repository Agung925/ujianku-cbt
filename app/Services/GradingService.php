<?php

namespace App\Services;

use App\Models\JawabanSiswa;
use App\Models\Nilai;
use App\Models\Soal;
use App\Models\Ujian;
use Illuminate\Support\Facades\DB;

class GradingService
{
    /**
     * Calculate automatic score for multiple choice questions.
     */
    public function calculateScorePG(int $ujianId, int $siswaId): ?float
    {
        try {
            $ujian = Ujian::find($ujianId);
            if (!$ujian) {
                throw new \Exception('Ujian tidak ditemukan.');
            }

            // Get all PG questions in this exam
            $pgQuestions = $ujian->soal()
                ->where('tipe_soal', 'pg')
                ->get();

            if ($pgQuestions->isEmpty()) {
                return 0;
            }

            $correctAnswers = 0;
            $totalWeight = 0;

            // Loop through each PG question
            foreach ($pgQuestions as $soal) {
                $siswaAnswer = JawabanSiswa::where('ujian_id', $ujianId)
                    ->where('siswa_id', $siswaId)
                    ->where('soal_id', $soal->id)
                    ->first();

                $totalWeight += $soal->bobot;

                // Check if answer is correct
                if ($siswaAnswer && $siswaAnswer->jawaban === $soal->kunci_jawaban) {
                    $correctAnswers += $soal->bobot;
                }
            }

            // Calculate score: (correct_weight / total_weight) * 100
            $score = ($totalWeight > 0) ? ($correctAnswers / $totalWeight) * 100 : 0;

            return round($score, 2);
        } catch (\Exception $e) {
            \Log::error('[GradingService] Error calculating PG score', [
                'ujian_id' => $ujianId,
                'siswa_id' => $siswaId,
                'error' => $e->getMessage(),
            ]);
            return null;
        }
    }

    /**
     * Save essay score (manual grading by guru).
     */
    public function calculateScoreEssay(int $ujianId, int $siswaId, float $nilaiEssay): float
    {
        // Validate score range
        $nilaiEssay = max(0, min(100, $nilaiEssay));
        return round($nilaiEssay, 2);
    }

    /**
     * Calculate final score and status.
     */
    public function calculateFinalScore(int $ujianId, int $siswaId): array
    {
        try {
            $nilai = Nilai::where('ujian_id', $ujianId)
                ->where('siswa_id', $siswaId)
                ->first();

            if (!$nilai) {
                throw new \Exception('Nilai tidak ditemukan.');
            }

            // Get nilai_otomatis and nilai_essay
            $nilaiOtomatis = $nilai->nilai_otomatis ?? 0;
            $nilaiEssay = $nilai->nilai_essay ?? 0;

            // Check if exam has essay questions
            $ujian = Ujian::find($ujianId);
            $hasEssay = $ujian->soal()
                ->where('tipe_soal', 'essay')
                ->exists();

            // Calculate final score
            if ($hasEssay && $nilaiEssay > 0) {
                // Average of PG and Essay
                $nilaiAkhir = ($nilaiOtomatis + $nilaiEssay) / 2;
            } else {
                // Only PG
                $nilaiAkhir = $nilaiOtomatis;
            }

            $nilaiAkhir = round($nilaiAkhir, 2);

            // Determine status (pass >= 70, fail < 70)
            $status = $nilaiAkhir >= 70 ? 'lulus' : 'tidak_lulus';

            return [
                'nilai_akhir' => $nilaiAkhir,
                'status' => $status,
            ];
        } catch (\Exception $e) {
            \Log::error('[GradingService] Error calculating final score', [
                'ujian_id' => $ujianId,
                'siswa_id' => $siswaId,
                'error' => $e->getMessage(),
            ]);
            return [
                'nilai_akhir' => 0,
                'status' => 'belum_dinilai',
            ];
        }
    }

    /**
     * Auto-grade exam when all answers are submitted.
     */
    public function autoGradeExam(int $ujianId, int $siswaId): bool
    {
        try {
            DB::beginTransaction();

            // Check if all answers are submitted
            $ujian = Ujian::with('soal')->find($ujianId);
            if (!$ujian) {
                throw new \Exception('Ujian tidak ditemukan.');
            }

            $submittedAnswers = JawabanSiswa::where('ujian_id', $ujianId)
                ->where('siswa_id', $siswaId)
                ->where('is_submitted', true)
                ->count();

            $totalQuestions = $ujian->soal->count();

            if ($submittedAnswers !== $totalQuestions) {
                throw new \Exception('Tidak semua jawaban tersubmit.');
            }

            // Get or create Nilai record
            $nilai = Nilai::firstOrCreate(
                [
                    'ujian_id' => $ujianId,
                    'siswa_id' => $siswaId,
                ],
                [
                    'tenant_id' => tenancy()->tenant?->id,
                    'nilai_otomatis' => 0,
                    'nilai_essay' => 0,
                    'nilai_akhir' => 0,
                    'status' => 'pending',
                ]
            );

            // Calculate automatic score (PG questions)
            $scorePG = $this->calculateScorePG($ujianId, $siswaId);
            if ($scorePG !== null) {
                $nilai->nilai_otomatis = $scorePG;
            }

            // Check if there are essay questions
            $hasEssay = $ujian->soal()
                ->where('tipe_soal', 'essay')
                ->exists();

            if (!$hasEssay) {
                // No essay questions - calculate final score immediately
                $finalScore = $this->calculateFinalScore($ujianId, $siswaId);
                $nilai->nilai_akhir = $finalScore['nilai_akhir'];
                $nilai->status = $finalScore['status'];
            } else {
                // Has essay - set status to pending_essay for manual grading
                $nilai->status = 'pending_essay';
            }

            $nilai->save();

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('[GradingService] Error auto-grading exam', [
                'ujian_id' => $ujianId,
                'siswa_id' => $siswaId,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Finalize grades after manual essay grading.
     */
    public function finalizeGrades(int $ujianId): int
    {
        try {
            $updatedCount = 0;

            // Get all Nilai records for this exam with pending_essay status
            $nilaiRecords = Nilai::where('ujian_id', $ujianId)
                ->where('status', 'pending_essay')
                ->get();

            foreach ($nilaiRecords as $nilai) {
                // Only finalize if essay score is set
                if ($nilai->nilai_essay !== null && $nilai->nilai_essay > 0) {
                    $finalScore = $this->calculateFinalScore($ujianId, $nilai->siswa_id);
                    $nilai->nilai_akhir = $finalScore['nilai_akhir'];
                    $nilai->status = $finalScore['status'];
                    $nilai->save();
                    $updatedCount++;
                }
            }

            return $updatedCount;
        } catch (\Exception $e) {
            \Log::error('[GradingService] Error finalizing grades', [
                'ujian_id' => $ujianId,
                'error' => $e->getMessage(),
            ]);
            return 0;
        }
    }

    /**
     * Get grading summary for an exam.
     */
    public function getGradingSummary(int $ujianId): array
    {
        try {
            $totalStudents = Nilai::where('ujian_id', $ujianId)->count();
            $graded = Nilai::where('ujian_id', $ujianId)
                ->whereIn('status', ['lulus', 'tidak_lulus'])
                ->count();
            $pendingEssay = Nilai::where('ujian_id', $ujianId)
                ->where('status', 'pending_essay')
                ->count();
            $pendingSubmit = Nilai::where('ujian_id', $ujianId)
                ->where('status', 'pending')
                ->count();

            $avgScore = Nilai::where('ujian_id', $ujianId)
                ->whereNotNull('nilai_akhir')
                ->where('nilai_akhir', '>', 0)
                ->avg('nilai_akhir') ?? 0;

            $passCount = Nilai::where('ujian_id', $ujianId)
                ->where('status', 'lulus')
                ->count();

            return [
                'total_students' => $totalStudents,
                'graded' => $graded,
                'pending_essay' => $pendingEssay,
                'pending_submit' => $pendingSubmit,
                'average_score' => round($avgScore, 2),
                'pass_rate' => $totalStudents > 0 ? round(($passCount / $totalStudents) * 100, 2) : 0,
            ];
        } catch (\Exception $e) {
            \Log::error('[GradingService] Error getting grading summary', [
                'ujian_id' => $ujianId,
                'error' => $e->getMessage(),
            ]);
            return [];
        }
    }
}
