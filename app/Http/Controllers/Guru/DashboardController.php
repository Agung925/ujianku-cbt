<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Services\StatisticsService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    protected StatisticsService $statisticsService;

    public function __construct(StatisticsService $statisticsService)
    {
        $this->statisticsService = $statisticsService;
    }

    /**
     * Display guru main dashboard
     */
    public function index(): View
    {
        $guruId = auth()->user()->id; // Get from authenticated guru
        $stats = $this->statisticsService->getGuruDashboardStats($guruId);

        return view('guru.dashboard', compact('stats'));
    }

    /**
     * Display detailed statistics page for guru
     */
    public function statisticsPage(Request $request): View
    {
        $guruId = auth()->user()->id;
        $bulan = $request->get('bulan', now()->month);
        $tahun = $request->get('tahun', now()->year);

        // Get monthly trend
        $monthlyTrend = $this->statisticsService->getMonthlyTrend(1, 12, $tahun);

        // Get difficulty analysis
        $difficultyAnalysis = $this->statisticsService->getDifficultyAnalysis();

        // Get student performance data
        $studentPerformance = []; // Will be populated via AJAX for specific exam

        return view('guru.statistics', compact(
            'monthlyTrend',
            'difficultyAnalysis',
            'studentPerformance',
            'bulan',
            'tahun'
        ));
    }

    /**
     * Get student performance for specific exam (AJAX)
     */
    public function studentPerformance(Request $request)
    {
        $ujianId = $request->get('ujian_id');
        
        if (!$ujianId) {
            return response()->json(['error' => 'Ujian ID required'], 422);
        }

        // Get exam scores
        $examScores = $this->statisticsService->getExamScores($ujianId);

        return response()->json($examScores);
    }

    /**
     * Get chart data for AJAX requests
     */
    public function chartData(Request $request)
    {
        $type = $request->get('type'); // 'monthly_trend', 'difficulty', 'exam_scores'
        $ujianId = $request->get('ujian_id');
        $bulan = $request->get('bulan', now()->month);
        $tahun = $request->get('tahun', now()->year);

        $data = match($type) {
            'exam_scores' => $ujianId ? $this->statisticsService->getExamScores($ujianId, $bulan, $tahun) : [],
            'monthly_trend' => $this->statisticsService->getMonthlyTrend(1, 12, $tahun),
            'difficulty' => $this->statisticsService->getDifficultyAnalysis(),
            default => []
        };

        return response()->json($data);
    }

    /**
     * Export student grades to CSV
     */
    public function exportGrades(Request $request)
    {
        $ujianId = $request->get('ujian_id');

        if (!$ujianId) {
            return response()->json(['error' => 'Ujian ID required'], 422);
        }

        $filename = 'grades_' . now()->format('Y-m-d_H-i-s') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=utf-8',
            'Content-Disposition' => 'attachment; filename=' . $filename,
        ];

        $callback = function () use ($ujianId) {
            $file = fopen('php://output', 'w');
            
            // Write header
            fputcsv($file, ['Student Name', 'NIS', 'PG Score', 'Essay Score', 'Final Score', 'Status']);

            // Get grades for exam
            $grades = \App\Models\Nilai::where('ujian_id', $ujianId)
                ->with('siswa')
                ->orderBy('created_at')
                ->get();

            foreach ($grades as $grade) {
                fputcsv($file, [
                    $grade->siswa->nama_siswa,
                    $grade->siswa->nis,
                    $grade->nilai_otomatis,
                    $grade->nilai_essay,
                    $grade->nilai_akhir,
                    $grade->status,
                ]);
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
