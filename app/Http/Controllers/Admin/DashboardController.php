<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Guru;
use App\Models\KategoriUjian;
use App\Models\Siswa;
use App\Models\Ujian;
use App\Services\StatisticsService;
use App\Services\NewsService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    protected StatisticsService $statisticsService;
    protected NewsService $newsService;

    public function __construct(StatisticsService $statisticsService, NewsService $newsService)
    {
        $this->statisticsService = $statisticsService;
        $this->newsService = $newsService;
    }

    /**
     * Display the admin dashboard.
     * 
     * For Platform Admins: Shows cross-tenant statistics
     * For Tenant Admins: Shows tenant-scoped statistics
     */
    public function index(): View
    {
        $stats = $this->statisticsService->getAdminDashboardStats();
        $news = $this->newsService->getNewsForDisplay(5);

        return view('admin.dashboard', compact('stats', 'news'));
    }

    /**
     * Display detailed statistics page with charts and filters
     */
    public function statisticsPage(Request $request): View
    {
        $kategori = $request->get('kategori');
        $bulan = $request->get('bulan', now()->month);
        $tahun = $request->get('tahun', now()->year);

        // Get pass rate by category
        $pasRateByCategory = $this->statisticsService->getPassRateByCategory();

        // Get monthly trend
        $monthlyTrend = $this->statisticsService->getMonthlyTrend(1, 12, $tahun);

        // Get difficulty analysis
        $difficultyAnalysis = $this->statisticsService->getDifficultyAnalysis();

        // Get available categories for filter
        $categories = KategoriUjian::whereTenantId(tenancy()->tenant?->id)
            ->select('id', 'nama')
            ->get();

        return view('admin.statistics', compact(
            'pasRateByCategory',
            'monthlyTrend',
            'difficultyAnalysis',
            'categories',
            'bulan',
            'tahun'
        ));
    }

    /**
     * Get chart data for AJAX requests
     */
    public function chartData(Request $request)
    {
        $type = $request->get('type'); // 'pass_rate', 'monthly_trend', 'difficulty'
        $ujianId = $request->get('ujian_id');
        $bulan = $request->get('bulan', now()->month);
        $tahun = $request->get('tahun', now()->year);

        $data = match($type) {
            'exam_scores' => $this->statisticsService->getExamScores($ujianId, $bulan, $tahun),
            'monthly_trend' => $this->statisticsService->getMonthlyTrend(1, 12, $tahun),
            'pass_rate' => $this->statisticsService->getPassRateByCategory(),
            'difficulty' => $this->statisticsService->getDifficultyAnalysis(),
            default => []
        };

        return response()->json($data);
    }

    /**
     * Export statistics to CSV
     */
    public function exportStatistics(Request $request)
    {
        $type = $request->get('type'); // 'grades', 'performance', 'difficulty'
        
        $filename = 'statistics_' . now()->format('Y-m-d_H-i-s') . '.csv';

        $data = match($type) {
            'pass_rate' => $this->statisticsService->getPassRateByCategory(),
            'difficulty' => $this->statisticsService->getDifficultyAnalysis(),
            default => []
        };

        $headers = [
            'Content-Type' => 'text/csv; charset=utf-8',
            'Content-Disposition' => 'attachment; filename=' . $filename,
        ];

        $callback = function () use ($data, $type) {
            $file = fopen('php://output', 'w');
            
            // Write header
            if ($type === 'pass_rate') {
                fputcsv($file, ['Category', 'Pass Rate (%)', 'Passed', 'Total']);
                foreach ($data as $row) {
                    fputcsv($file, [
                        $row['category'],
                        $row['pass_rate'],
                        $row['passed'],
                        $row['total'],
                    ]);
                }
            } elseif ($type === 'difficulty') {
                fputcsv($file, ['Question', 'Correct Rate (%)', 'Total Responses', 'Difficulty']);
                foreach ($data as $row) {
                    fputcsv($file, [
                        $row['question'],
                        $row['correct_rate'],
                        $row['total_responses'],
                        $row['difficulty'],
                    ]);
                }
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
