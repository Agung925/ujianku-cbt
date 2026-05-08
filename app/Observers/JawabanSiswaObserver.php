<?php

namespace App\Observers;

use App\Models\JawabanSiswa;
use App\Services\GradingService;

class JawabanSiswaObserver
{
    protected GradingService $gradingService;

    public function __construct(GradingService $gradingService)
    {
        $this->gradingService = $gradingService;
    }

    /**
     * Handle when JawabanSiswa is updated.
     */
    public function updated(JawabanSiswa $jawabanSiswa): void
    {
        // Check if is_submitted was just set to true
        if ($jawabanSiswa->wasChanged('is_submitted') && $jawabanSiswa->is_submitted) {
            // Trigger auto-grading
            $this->triggerAutoGrading($jawabanSiswa->ujian_id, $jawabanSiswa->siswa_id);
        }
    }

    /**
     * Handle when JawabanSiswa is created.
     */
    public function created(JawabanSiswa $jawabanSiswa): void
    {
        // Check if created with is_submitted = true
        if ($jawabanSiswa->is_submitted) {
            $this->triggerAutoGrading($jawabanSiswa->ujian_id, $jawabanSiswa->siswa_id);
        }
    }

    /**
     * Trigger auto-grading for the exam.
     */
    protected function triggerAutoGrading(int $ujianId, int $siswaId): void
    {
        try {
            // Use a queue job to avoid blocking the request
            // For now, we'll call it synchronously
            // In production, dispatch to queue: 
            // \App\Jobs\AutoGradeExamJob::dispatch($ujianId, $siswaId);

            $this->gradingService->autoGradeExam($ujianId, $siswaId);

            \Log::info('[JawabanSiswaObserver] Auto-grading triggered', [
                'ujian_id' => $ujianId,
                'siswa_id' => $siswaId,
            ]);
        } catch (\Exception $e) {
            \Log::error('[JawabanSiswaObserver] Error triggering auto-grading', [
                'ujian_id' => $ujianId,
                'siswa_id' => $siswaId,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
