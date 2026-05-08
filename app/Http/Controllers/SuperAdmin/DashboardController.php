<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Guru;
use App\Models\KategoriUjian;
use App\Models\Siswa;
use App\Models\Ujian;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Display the super admin dashboard.
     */
    public function index(): View
    {
        // Hitung statistik dari semua tenant
        $stats = [
            'total_guru' => Guru::count(),
            'total_siswa' => Siswa::count(),
            'total_ujian' => Ujian::count(),
            'total_kategori' => KategoriUjian::count(),
            'ujian_hari_ini' => Ujian::whereDate('created_at', today())->count(),
        ];

        return view('superadmin.dashboard', $stats);
    }
}
