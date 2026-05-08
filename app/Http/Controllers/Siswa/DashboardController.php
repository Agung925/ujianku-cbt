<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Services\StatisticsService;
use Illuminate\View\View;

class DashboardController extends Controller
{
    protected StatisticsService $statisticsService;

    public function __construct(StatisticsService $statisticsService)
    {
        $this->statisticsService = $statisticsService;
    }

    /**
     * Display siswa main dashboard
     * Shows active exam, upcoming exams, and history
     */
    public function index(): View
    {
        $siswaId = auth()->user()->id; // Get from authenticated siswa

        $stats = $this->statisticsService->getSiswaDashboardStats($siswaId);

        return view('siswa.dashboard', compact('stats'));
    }
}
