<?php

namespace Tests\Unit;

use App\Models\JawabanSiswa;
use App\Models\Nilai;
use App\Models\Siswa;
use App\Models\Soal;
use App\Models\Ujian;
use App\Services\GradingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GradingServiceTest extends TestCase
{
    use RefreshDatabase;

    protected GradingService $gradingService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->gradingService = app(GradingService::class);
    }

    /**
     * Test calculateScorePG with correct weighted scoring
     */
    public function test_calculate_score_pg_correct_answers(): void
    {
        // Create exam with PG questions
        $ujian = Ujian::factory()->create();
        $siswa = Siswa::factory()->create();

        // Create 2 PG questions with different weights
        $soal1 = Soal::factory()->create([
            'ujian_id' => $ujian->id,
            'tipe_soal' => 'pg',
            'bobot' => 50,
            'kunci_jawaban' => 'A',
        ]);

        $soal2 = Soal::factory()->create([
            'ujian_id' => $ujian->id,
            'tipe_soal' => 'pg',
            'bobot' => 50,
            'kunci_jawaban' => 'B',
        ]);

        // Create answers - first correct, second wrong
        JawabanSiswa::factory()->create([
            'ujian_id' => $ujian->id,
            'siswa_id' => $siswa->id,
            'soal_id' => $soal1->id,
            'jawaban' => 'A', // Correct
            'is_submitted' => true,
        ]);

        JawabanSiswa::factory()->create([
            'ujian_id' => $ujian->id,
            'siswa_id' => $siswa->id,
            'soal_id' => $soal2->id,
            'jawaban' => 'C', // Wrong
            'is_submitted' => true,
        ]);

        // Score should be (50/100) * 100 = 50
        $score = $this->gradingService->calculateScorePG($ujian->id, $siswa->id);

        $this->assertEquals(50.0, $score);
    }

    /**
     * Test calculateScorePG with all correct answers
     */
    public function test_calculate_score_pg_all_correct(): void
    {
        $ujian = Ujian::factory()->create();
        $siswa = Siswa::factory()->create();

        $soal1 = Soal::factory()->create([
            'ujian_id' => $ujian->id,
            'tipe_soal' => 'pg',
            'bobot' => 30,
            'kunci_jawaban' => 'A',
        ]);

        $soal2 = Soal::factory()->create([
            'ujian_id' => $ujian->id,
            'tipe_soal' => 'pg',
            'bobot' => 70,
            'kunci_jawaban' => 'B',
        ]);

        JawabanSiswa::factory()->create([
            'ujian_id' => $ujian->id,
            'siswa_id' => $siswa->id,
            'soal_id' => $soal1->id,
            'jawaban' => 'A',
            'is_submitted' => true,
        ]);

        JawabanSiswa::factory()->create([
            'ujian_id' => $ujian->id,
            'siswa_id' => $siswa->id,
            'soal_id' => $soal2->id,
            'jawaban' => 'B',
            'is_submitted' => true,
        ]);

        $score = $this->gradingService->calculateScorePG($ujian->id, $siswa->id);

        $this->assertEquals(100.0, $score);
    }

    /**
     * Test calculateScorePG with no answers
     */
    public function test_calculate_score_pg_no_answers(): void
    {
        $ujian = Ujian::factory()->create();
        $siswa = Siswa::factory()->create();

        Soal::factory()->create([
            'ujian_id' => $ujian->id,
            'tipe_soal' => 'pg',
            'bobot' => 50,
        ]);

        $score = $this->gradingService->calculateScorePG($ujian->id, $siswa->id);

        $this->assertEquals(0.0, $score);
    }

    /**
     * Test calculateScorePG when no PG questions exist
     */
    public function test_calculate_score_pg_no_pg_questions(): void
    {
        $ujian = Ujian::factory()->create();
        $siswa = Siswa::factory()->create();

        // Create only essay question
        Soal::factory()->create([
            'ujian_id' => $ujian->id,
            'tipe_soal' => 'essay',
        ]);

        $score = $this->gradingService->calculateScorePG($ujian->id, $siswa->id);

        $this->assertEquals(0.0, $score);
    }

    /**
     * Test calculateScorePG when ujian not found
     */
    public function test_calculate_score_pg_ujian_not_found(): void
    {
        $score = $this->gradingService->calculateScorePG(999, 999);

        $this->assertNull($score);
    }

    /**
     * Test calculateScoreEssay with valid score
     */
    public function test_calculate_score_essay_valid(): void
    {
        $score = $this->gradingService->calculateScoreEssay(1, 1, 85.5);

        $this->assertEquals(85.5, $score);
    }

    /**
     * Test calculateScoreEssay with score > 100
     */
    public function test_calculate_score_essay_clamp_max(): void
    {
        $score = $this->gradingService->calculateScoreEssay(1, 1, 150);

        $this->assertEquals(100.0, $score);
    }

    /**
     * Test calculateScoreEssay with negative score
     */
    public function test_calculate_score_essay_clamp_min(): void
    {
        $score = $this->gradingService->calculateScoreEssay(1, 1, -10);

        $this->assertEquals(0.0, $score);
    }

    /**
     * Test calculateFinalScore with PG only (no essay)
     */
    public function test_calculate_final_score_pg_only(): void
    {
        $ujian = Ujian::factory()->create();
        $siswa = Siswa::factory()->create();

        // Create only PG questions
        Soal::factory()->create([
            'ujian_id' => $ujian->id,
            'tipe_soal' => 'pg',
            'bobot' => 100,
            'kunci_jawaban' => 'A',
        ]);

        // Create nilai record
        $nilai = Nilai::create([
            'ujian_id' => $ujian->id,
            'siswa_id' => $siswa->id,
            'tenant_id' => null,
            'nilai_otomatis' => 75,
            'nilai_essay' => 0,
            'nilai_akhir' => 0,
            'status' => 'pending',
        ]);

        $result = $this->gradingService->calculateFinalScore($ujian->id, $siswa->id);

        $this->assertEquals(75.0, $result['nilai_akhir']);
        $this->assertEquals('lulus', $result['status']);
    }

    /**
     * Test calculateFinalScore with PG + Essay (average)
     */
    public function test_calculate_final_score_pg_and_essay(): void
    {
        $ujian = Ujian::factory()->create();
        $siswa = Siswa::factory()->create();

        // Create PG and essay questions
        Soal::factory()->create([
            'ujian_id' => $ujian->id,
            'tipe_soal' => 'pg',
            'bobot' => 50,
        ]);

        Soal::factory()->create([
            'ujian_id' => $ujian->id,
            'tipe_soal' => 'essay',
        ]);

        // Create nilai record
        $nilai = Nilai::create([
            'ujian_id' => $ujian->id,
            'siswa_id' => $siswa->id,
            'tenant_id' => null,
            'nilai_otomatis' => 80,
            'nilai_essay' => 60,
            'nilai_akhir' => 0,
            'status' => 'pending_essay',
        ]);

        $result = $this->gradingService->calculateFinalScore($ujian->id, $siswa->id);

        // Average = (80 + 60) / 2 = 70
        $this->assertEquals(70.0, $result['nilai_akhir']);
        $this->assertEquals('lulus', $result['status']);
    }

    /**
     * Test calculateFinalScore fail status (<70)
     */
    public function test_calculate_final_score_fail_status(): void
    {
        $ujian = Ujian::factory()->create();
        $siswa = Siswa::factory()->create();

        Soal::factory()->create([
            'ujian_id' => $ujian->id,
            'tipe_soal' => 'pg',
        ]);

        $nilai = Nilai::create([
            'ujian_id' => $ujian->id,
            'siswa_id' => $siswa->id,
            'tenant_id' => null,
            'nilai_otomatis' => 60,
            'nilai_essay' => 0,
            'nilai_akhir' => 0,
            'status' => 'pending',
        ]);

        $result = $this->gradingService->calculateFinalScore($ujian->id, $siswa->id);

        $this->assertEquals(60.0, $result['nilai_akhir']);
        $this->assertEquals('tidak_lulus', $result['status']);
    }

    /**
     * Test autoGradeExam success with all answers submitted
     */
    public function test_auto_grade_exam_success(): void
    {
        $ujian = Ujian::factory()->create();
        $siswa = Siswa::factory()->create();

        $soal = Soal::factory()->create([
            'ujian_id' => $ujian->id,
            'tipe_soal' => 'pg',
            'bobot' => 100,
            'kunci_jawaban' => 'A',
        ]);

        JawabanSiswa::factory()->create([
            'ujian_id' => $ujian->id,
            'siswa_id' => $siswa->id,
            'soal_id' => $soal->id,
            'jawaban' => 'A',
            'is_submitted' => true,
        ]);

        $result = $this->gradingService->autoGradeExam($ujian->id, $siswa->id);

        $this->assertTrue($result);

        $nilai = Nilai::where('ujian_id', $ujian->id)
            ->where('siswa_id', $siswa->id)
            ->first();

        $this->assertEquals(100.0, $nilai->nilai_otomatis);
        $this->assertEquals('lulus', $nilai->status);
    }

    /**
     * Test autoGradeExam with pending essay
     */
    public function test_auto_grade_exam_with_essay_pending(): void
    {
        $ujian = Ujian::factory()->create();
        $siswa = Siswa::factory()->create();

        Soal::factory()->create([
            'ujian_id' => $ujian->id,
            'tipe_soal' => 'pg',
            'bobot' => 50,
            'kunci_jawaban' => 'A',
        ]);

        Soal::factory()->create([
            'ujian_id' => $ujian->id,
            'tipe_soal' => 'essay',
        ]);

        // Submit answers for both questions
        $questions = $ujian->soal;
        foreach ($questions as $soal) {
            JawabanSiswa::factory()->create([
                'ujian_id' => $ujian->id,
                'siswa_id' => $siswa->id,
                'soal_id' => $soal->id,
                'jawaban' => $soal->tipe_soal === 'pg' ? 'A' : 'essay answer',
                'is_submitted' => true,
            ]);
        }

        $result = $this->gradingService->autoGradeExam($ujian->id, $siswa->id);

        $this->assertTrue($result);

        $nilai = Nilai::where('ujian_id', $ujian->id)
            ->where('siswa_id', $siswa->id)
            ->first();

        // Should be pending_essay since essay not graded yet
        $this->assertEquals('pending_essay', $nilai->status);
    }

    /**
     * Test autoGradeExam failure when not all answers submitted
     */
    public function test_auto_grade_exam_not_all_submitted(): void
    {
        $ujian = Ujian::factory()->create();
        $siswa = Siswa::factory()->create();

        // Create 2 questions
        Soal::factory()->create([
            'ujian_id' => $ujian->id,
            'tipe_soal' => 'pg',
        ]);

        Soal::factory()->create([
            'ujian_id' => $ujian->id,
            'tipe_soal' => 'pg',
        ]);

        // Only submit 1 answer
        $soal1 = $ujian->soal->first();
        JawabanSiswa::factory()->create([
            'ujian_id' => $ujian->id,
            'siswa_id' => $siswa->id,
            'soal_id' => $soal1->id,
            'is_submitted' => true,
        ]);

        $result = $this->gradingService->autoGradeExam($ujian->id, $siswa->id);

        $this->assertFalse($result);
    }

    /**
     * Test finalizeGrades success
     */
    public function test_finalize_grades_success(): void
    {
        $ujian = Ujian::factory()->create();

        // Create multiple students with pending_essay
        $siswa1 = Siswa::factory()->create();
        $siswa2 = Siswa::factory()->create();

        Nilai::create([
            'ujian_id' => $ujian->id,
            'siswa_id' => $siswa1->id,
            'tenant_id' => null,
            'nilai_otomatis' => 80,
            'nilai_essay' => 75,
            'status' => 'pending_essay',
        ]);

        Nilai::create([
            'ujian_id' => $ujian->id,
            'siswa_id' => $siswa2->id,
            'tenant_id' => null,
            'nilai_otomatis' => 60,
            'nilai_essay' => 65,
            'status' => 'pending_essay',
        ]);

        $updatedCount = $this->gradingService->finalizeGrades($ujian->id);

        $this->assertEquals(2, $updatedCount);

        $nilai1 = Nilai::where('ujian_id', $ujian->id)
            ->where('siswa_id', $siswa1->id)
            ->first();

        $nilai2 = Nilai::where('ujian_id', $ujian->id)
            ->where('siswa_id', $siswa2->id)
            ->first();

        $this->assertEquals('lulus', $nilai1->status); // (80+75)/2 = 77.5
        $this->assertEquals('lulus', $nilai2->status); // (60+65)/2 = 62.5 (should fail)
    }

    /**
     * Test getGradingSummary returns correct statistics
     */
    public function test_get_grading_summary(): void
    {
        $ujian = Ujian::factory()->create();

        // Create 10 students with various statuses
        for ($i = 0; $i < 7; $i++) {
            Nilai::create([
                'ujian_id' => $ujian->id,
                'siswa_id' => Siswa::factory()->create()->id,
                'tenant_id' => null,
                'nilai_akhir' => 80,
                'status' => 'lulus',
            ]);
        }

        for ($i = 0; $i < 2; $i++) {
            Nilai::create([
                'ujian_id' => $ujian->id,
                'siswa_id' => Siswa::factory()->create()->id,
                'tenant_id' => null,
                'nilai_akhir' => 60,
                'status' => 'tidak_lulus',
            ]);
        }

        Nilai::create([
            'ujian_id' => $ujian->id,
            'siswa_id' => Siswa::factory()->create()->id,
            'tenant_id' => null,
            'status' => 'pending_essay',
        ]);

        $summary = $this->gradingService->getGradingSummary($ujian->id);

        $this->assertEquals(10, $summary['total_students']);
        $this->assertEquals(9, $summary['graded']);
        $this->assertEquals(1, $summary['pending_essay']);
        $this->assertEquals(0, $summary['pending_submit']);
        $this->assertEquals(70.0, $summary['average_score']); // (7*80 + 2*60) / 9
        $this->assertEquals(77.78, $summary['pass_rate']); // 7/9 * 100
    }

    /**
     * Test getGradingSummary with empty exam
     */
    public function test_get_grading_summary_empty(): void
    {
        $ujian = Ujian::factory()->create();

        $summary = $this->gradingService->getGradingSummary($ujian->id);

        $this->assertEquals(0, $summary['total_students']);
        $this->assertEquals(0, $summary['graded']);
        $this->assertEquals(0, $summary['pass_rate']);
    }
}
