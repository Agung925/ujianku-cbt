<?php

namespace App\Services;

use App\Models\Ujian;
use App\Models\Soal;
use App\Models\Nilai;
use App\Models\Siswa;
use App\Models\Guru;
use App\Models\JawabanSiswa;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class StatisticsService
{
    /**
     * Get admin dashboard statistics for current tenant
     *
     * @return array
     */
    public function getAdminDashboardStats(): array
    {
        try {
            $tenantId = tenancy()->tenant?->id;
            
            return [
                'total_guru' => Guru::whereTenantId($tenantId)->where('is_active', true)->count(),
                'total_siswa' => Siswa::whereTenantId($tenantId)->where('is_active', true)->count(),
                'total_exam' => Ujian::whereTenantId($tenantId)->count(),
                'total_questions' => Soal::whereTenantId($tenantId)->count(),
                'upcoming_exams' => $this->getUpcomingExams(),
                'recent_activities' => $this->getRecentActivities(),
                'average_score' => $this->getAverageScore(),
                'pass_rate' => $this->getPassRate(),
            ];
        } catch (\Exception $e) {
            \Log::error('[StatisticsService] Error getting admin stats', [
                'error' => $e->getMessage(),
            ]);
            return [];
        }
    }

    /**
     * Get guru dashboard statistics
     *
     * @param int $guruId
     * @return array
     */
    public function getGuruDashboardStats(int $guruId): array
    {
        try {
            $tenantId = tenancy()->tenant?->id;
            
            return [
                'total_soal' => Soal::whereTenantId($tenantId)
                    ->where('guru_id', $guruId)
                    ->count(),
                'total_exam' => Ujian::whereTenantId($tenantId)
                    ->where('guru_id', $guruId)
                    ->count(),
                'total_siswa' => $this->getGuruStudentCount($guruId),
                'upcoming_exams' => $this->getGuruUpcomingExams($guruId),
                'past_exams' => $this->getGuruPastExams($guruId),
                'average_student_score' => $this->getGuruAverageScore($guruId),
                'completion_rate' => $this->getGuruCompletionRate($guruId),
            ];
        } catch (\Exception $e) {
            \Log::error('[StatisticsService] Error getting guru stats', [
                'error' => $e->getMessage(),
                'guru_id' => $guruId,
            ]);
            return [];
        }
    }

    /**
     * Get siswa dashboard statistics
     *
     * @param int $siswaId
     * @return array
     */
    public function getSiswaDashboardStats(int $siswaId): array
    {
        try {
            $tenantId = tenancy()->tenant?->id;
            
            return [
                'active_exam' => $this->getSiswaActiveExam($siswaId),
                'upcoming_exams' => $this->getSiswaUpcomingExams($siswaId),
                'exam_history' => $this->getSiswaExamHistory($siswaId),
                'total_exams_taken' => $this->getSiswaTotalExamsTaken($siswaId),
                'average_score' => $this->getSiswaAverageScore($siswaId),
                'last_exam_score' => $this->getSiswaLastExamScore($siswaId),
            ];
        } catch (\Exception $e) {
            \Log::error('[StatisticsService] Error getting siswa stats', [
                'error' => $e->getMessage(),
                'siswa_id' => $siswaId,
            ]);
            return [];
        }
    }

    /**
     * Get exam scores for chart display
     *
     * @param int $ujianId
     * @param int|null $bulan
     * @param int|null $tahun
     * @return array
     */
    public function getExamScores(int $ujianId, ?int $bulan = null, ?int $tahun = null): array
    {
        try {
            $tenantId = tenancy()->tenant?->id;
            
            $query = Nilai::whereTenantId($tenantId)
                ->where('ujian_id', $ujianId)
                ->whereNotNull('nilai_akhir');
            
            if ($bulan) {
                $query->whereMonth('created_at', $bulan);
            }
            
            if ($tahun) {
                $query->whereYear('created_at', $tahun);
            }
            
            $scores = $query->pluck('nilai_akhir')->toArray();
            
            return [
                'scores' => $scores,
                'count' => count($scores),
                'average' => count($scores) > 0 ? round(array_sum($scores) / count($scores), 2) : 0,
                'min' => count($scores) > 0 ? min($scores) : 0,
                'max' => count($scores) > 0 ? max($scores) : 0,
                'distribution' => $this->getScoreDistribution($scores),
            ];
        } catch (\Exception $e) {
            \Log::error('[StatisticsService] Error getting exam scores', [
                'error' => $e->getMessage(),
                'ujian_id' => $ujianId,
            ]);
            return ['scores' => [], 'count' => 0, 'average' => 0, 'min' => 0, 'max' => 0, 'distribution' => []];
        }
    }

    /**
     * Get monthly trend data for line chart
     *
     * @param int $bulanMulai
     * @param int $bulanAkhir
     * @param int $tahun
     * @return array
     */
    public function getMonthlyTrend(int $bulanMulai, int $bulanAkhir, int $tahun): array
    {
        try {
            $tenantId = tenancy()->tenant?->id;
            
            $data = [];
            
            for ($bulan = $bulanMulai; $bulan <= $bulanAkhir; $bulan++) {
                $monthLabel = Carbon::createFromDate($tahun, $bulan, 1)->format('M Y');
                
                $avgScore = Nilai::whereTenantId($tenantId)
                    ->whereMonth('created_at', $bulan)
                    ->whereYear('created_at', $tahun)
                    ->whereNotNull('nilai_akhir')
                    ->avg('nilai_akhir');
                
                $data[] = [
                    'month' => $monthLabel,
                    'average_score' => $avgScore ? round($avgScore, 2) : 0,
                    'bulan' => $bulan,
                ];
            }
            
            return $data;
        } catch (\Exception $e) {
            \Log::error('[StatisticsService] Error getting monthly trend', [
                'error' => $e->getMessage(),
            ]);
            return [];
        }
    }

    /**
     * Get student performance data
     *
     * @param int $siswaId
     * @return array
     */
    public function getStudentPerformance(int $siswaId): array
    {
        try {
            $tenantId = tenancy()->tenant?->id;
            
            $nilai = Nilai::whereTenantId($tenantId)
                ->where('siswa_id', $siswaId)
                ->whereNotNull('nilai_akhir')
                ->with('ujian')
                ->orderBy('created_at', 'desc')
                ->get();
            
            $data = [];
            foreach ($nilai as $n) {
                $data[] = [
                    'exam_name' => $n->ujian->nama_ujian ?? 'Unknown',
                    'score' => $n->nilai_akhir,
                    'status' => $n->status,
                    'date' => $n->created_at->format('d M Y'),
                    'pg_score' => $n->nilai_otomatis,
                    'essay_score' => $n->nilai_essay,
                ];
            }
            
            return $data;
        } catch (\Exception $e) {
            \Log::error('[StatisticsService] Error getting student performance', [
                'error' => $e->getMessage(),
                'siswa_id' => $siswaId,
            ]);
            return [];
        }
    }

    /**
     * Get percentage of students who passed exams by category
     *
     * @return array
     */
    public function getPassRateByCategory(): array
    {
        try {
            $tenantId = tenancy()->tenant?->id;
            
            $categories = DB::table('ujians')
                ->join('kategori_ujians', 'ujians.kategori_ujian_id', '=', 'kategori_ujians.id')
                ->where('ujians.tenant_id', $tenantId)
                ->select('kategori_ujians.nama', 'kategori_ujians.id')
                ->distinct()
                ->get();
            
            $data = [];
            foreach ($categories as $category) {
                $total = Nilai::whereTenantId($tenantId)
                    ->whereIn('ujian_id', function ($query) use ($category) {
                        $query->select('id')
                            ->from('ujians')
                            ->where('kategori_ujian_id', $category->id);
                    })
                    ->whereNotNull('nilai_akhir')
                    ->count();
                
                $passed = Nilai::whereTenantId($tenantId)
                    ->whereIn('ujian_id', function ($query) use ($category) {
                        $query->select('id')
                            ->from('ujians')
                            ->where('kategori_ujian_id', $category->id);
                    })
                    ->where('status', 'lulus')
                    ->count();
                
                $data[] = [
                    'category' => $category->nama,
                    'pass_rate' => $total > 0 ? round(($passed / $total) * 100, 2) : 0,
                    'passed' => $passed,
                    'total' => $total,
                ];
            }
            
            return $data;
        } catch (\Exception $e) {
            \Log::error('[StatisticsService] Error getting pass rate by category', [
                'error' => $e->getMessage(),
            ]);
            return [];
        }
    }

    /**
     * Get difficulty analysis for questions
     *
     * @return array
     */
    public function getDifficultyAnalysis(): array
    {
        try {
            $tenantId = tenancy()->tenant?->id;
            
            $soals = Soal::whereTenantId($tenantId)
                ->where('tipe_soal', 'pg')
                ->get();
            
            $data = [];
            foreach ($soals as $soal) {
                $total = JawabanSiswa::where('soal_id', $soal->id)
                    ->where('is_submitted', true)
                    ->count();
                
                $correct = JawabanSiswa::where('soal_id', $soal->id)
                    ->where('jawaban', $soal->kunci_jawaban)
                    ->where('is_submitted', true)
                    ->count();
                
                $correctRate = $total > 0 ? round(($correct / $total) * 100, 2) : 0;
                
                $difficulty = 'Medium';
                if ($correctRate >= 80) $difficulty = 'Easy';
                elseif ($correctRate < 40) $difficulty = 'Hard';
                
                $data[] = [
                    'question_id' => $soal->id,
                    'question' => substr($soal->pertanyaan, 0, 50) . '...',
                    'correct_rate' => $correctRate,
                    'total_responses' => $total,
                    'difficulty' => $difficulty,
                ];
            }
            
            return $data;
        } catch (\Exception $e) {
            \Log::error('[StatisticsService] Error getting difficulty analysis', [
                'error' => $e->getMessage(),
            ]);
            return [];
        }
    }

    // ===== HELPER METHODS =====

    private function getUpcomingExams(): array
    {
        $tenantId = tenancy()->tenant?->id;
        
        return Ujian::whereTenantId($tenantId)
            ->where('tanggal_mulai', '>=', now())
            ->where('tanggal_mulai', '<=', now()->addDays(7))
            ->orderBy('tanggal_mulai')
            ->limit(5)
            ->get(['id', 'nama_ujian', 'tanggal_mulai'])
            ->map(function ($exam) {
                return [
                    'id' => $exam->id,
                    'name' => $exam->nama_ujian,
                    'date' => $exam->tanggal_mulai->format('d M Y H:i'),
                ];
            })
            ->toArray();
    }

    private function getRecentActivities(): array
    {
        $tenantId = tenancy()->tenant?->id;
        
        return Nilai::whereTenantId($tenantId)
            ->with('siswa', 'ujian')
            ->orderBy('updated_at', 'desc')
            ->limit(5)
            ->get()
            ->map(function ($nilai) {
                return [
                    'message' => $nilai->siswa->nama_siswa . ' completed ' . $nilai->ujian->nama_ujian,
                    'date' => $nilai->updated_at->diffForHumans(),
                ];
            })
            ->toArray();
    }

    private function getAverageScore(): float
    {
        $tenantId = tenancy()->tenant?->id;
        
        $avg = Nilai::whereTenantId($tenantId)
            ->whereNotNull('nilai_akhir')
            ->avg('nilai_akhir');
        
        return $avg ? round($avg, 2) : 0;
    }

    private function getPassRate(): float
    {
        $tenantId = tenancy()->tenant?->id;
        
        $total = Nilai::whereTenantId($tenantId)
            ->whereNotNull('nilai_akhir')
            ->count();
        
        if ($total === 0) return 0;
        
        $passed = Nilai::whereTenantId($tenantId)
            ->where('status', 'lulus')
            ->count();
        
        return round(($passed / $total) * 100, 2);
    }

    private function getGuruStudentCount(int $guruId): int
    {
        $tenantId = tenancy()->tenant?->id;
        
        return DB::table('ujians')
            ->join('nilais', 'ujians.id', '=', 'nilais.ujian_id')
            ->where('ujians.tenant_id', $tenantId)
            ->where('ujians.guru_id', $guruId)
            ->distinct('nilais.siswa_id')
            ->count('nilais.siswa_id');
    }

    private function getGuruUpcomingExams(int $guruId): array
    {
        $tenantId = tenancy()->tenant?->id;
        
        return Ujian::whereTenantId($tenantId)
            ->where('guru_id', $guruId)
            ->where('tanggal_mulai', '>=', now())
            ->orderBy('tanggal_mulai')
            ->limit(3)
            ->get(['id', 'nama_ujian', 'tanggal_mulai', 'tanggal_selesai'])
            ->toArray();
    }

    private function getGuruPastExams(int $guruId): array
    {
        $tenantId = tenancy()->tenant?->id;
        
        return Ujian::whereTenantId($tenantId)
            ->where('guru_id', $guruId)
            ->where('tanggal_selesai', '<', now())
            ->orderBy('tanggal_selesai', 'desc')
            ->limit(3)
            ->get(['id', 'nama_ujian', 'tanggal_selesai'])
            ->toArray();
    }

    private function getGuruAverageScore(int $guruId): float
    {
        $tenantId = tenancy()->tenant?->id;
        
        $avg = Nilai::whereTenantId($tenantId)
            ->whereIn('ujian_id', function ($query) use ($guruId, $tenantId) {
                $query->select('id')
                    ->from('ujians')
                    ->where('tenant_id', $tenantId)
                    ->where('guru_id', $guruId);
            })
            ->whereNotNull('nilai_akhir')
            ->avg('nilai_akhir');
        
        return $avg ? round($avg, 2) : 0;
    }

    private function getGuruCompletionRate(int $guruId): float
    {
        $tenantId = tenancy()->tenant?->id;
        
        $exams = Ujian::whereTenantId($tenantId)
            ->where('guru_id', $guruId)
            ->count();
        
        if ($exams === 0) return 0;
        
        $completed = Ujian::whereTenantId($tenantId)
            ->where('guru_id', $guruId)
            ->where('tanggal_selesai', '<', now())
            ->count();
        
        return round(($completed / $exams) * 100, 2);
    }

    private function getSiswaActiveExam(int $siswaId): ?array
    {
        $tenantId = tenancy()->tenant?->id;
        
        $exam = Ujian::whereTenantId($tenantId)
            ->where('tanggal_mulai', '<=', now())
            ->where('tanggal_selesai', '>=', now())
            ->first(['id', 'nama_ujian', 'tanggal_selesai', 'durasi_menit']);
        
        if (!$exam) return null;
        
        return [
            'id' => $exam->id,
            'name' => $exam->nama_ujian,
            'end_time' => $exam->tanggal_selesai->format('d M Y H:i'),
            'duration_minutes' => $exam->durasi_menit,
        ];
    }

    private function getSiswaUpcomingExams(int $siswaId): array
    {
        $tenantId = tenancy()->tenant?->id;
        
        return Ujian::whereTenantId($tenantId)
            ->where('tanggal_mulai', '>', now())
            ->where('tanggal_mulai', '<=', now()->addDays(7))
            ->orderBy('tanggal_mulai')
            ->limit(3)
            ->get(['id', 'nama_ujian', 'tanggal_mulai'])
            ->map(function ($exam) {
                return [
                    'name' => $exam->nama_ujian,
                    'date' => $exam->tanggal_mulai->format('d M Y H:i'),
                ];
            })
            ->toArray();
    }

    private function getSiswaExamHistory(int $siswaId): array
    {
        $tenantId = tenancy()->tenant?->id;
        
        return Nilai::whereTenantId($tenantId)
            ->where('siswa_id', $siswaId)
            ->with('ujian')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get()
            ->map(function ($nilai) {
                return [
                    'exam' => $nilai->ujian->nama_ujian,
                    'score' => $nilai->nilai_akhir,
                    'status' => $nilai->status,
                    'date' => $nilai->created_at->format('d M Y'),
                ];
            })
            ->toArray();
    }

    private function getSiswaTotalExamsTaken(int $siswaId): int
    {
        $tenantId = tenancy()->tenant?->id;
        
        return Nilai::whereTenantId($tenantId)
            ->where('siswa_id', $siswaId)
            ->whereNotNull('nilai_akhir')
            ->count();
    }

    private function getSiswaAverageScore(int $siswaId): float
    {
        $tenantId = tenancy()->tenant?->id;
        
        $avg = Nilai::whereTenantId($tenantId)
            ->where('siswa_id', $siswaId)
            ->whereNotNull('nilai_akhir')
            ->avg('nilai_akhir');
        
        return $avg ? round($avg, 2) : 0;
    }

    private function getSiswaLastExamScore(int $siswaId): ?float
    {
        $tenantId = tenancy()->tenant?->id;
        
        $lastExam = Nilai::whereTenantId($tenantId)
            ->where('siswa_id', $siswaId)
            ->whereNotNull('nilai_akhir')
            ->orderBy('created_at', 'desc')
            ->first(['nilai_akhir']);
        
        return $lastExam ? round($lastExam->nilai_akhir, 2) : null;
    }

    private function getScoreDistribution(array $scores): array
    {
        if (empty($scores)) {
            return ['0-20' => 0, '21-40' => 0, '41-60' => 0, '61-80' => 0, '81-100' => 0];
        }
        
        $distribution = [
            '0-20' => 0,
            '21-40' => 0,
            '41-60' => 0,
            '61-80' => 0,
            '81-100' => 0,
        ];
        
        foreach ($scores as $score) {
            if ($score <= 20) $distribution['0-20']++;
            elseif ($score <= 40) $distribution['21-40']++;
            elseif ($score <= 60) $distribution['41-60']++;
            elseif ($score <= 80) $distribution['61-80']++;
            else $distribution['81-100']++;
        }
        
        return $distribution;
    }
}
