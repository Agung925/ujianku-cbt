<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Guru;
use App\Models\KategoriUjian;
use App\Models\Siswa;
use App\Models\Ujian;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Display the admin dashboard.
     * 
     * For Platform Admins: Shows cross-tenant statistics
     * For Tenant Admins: Shows tenant-scoped statistics
     */
    public function index(): View
    {
        // Check if admin is platform-level (no tenant_id) or tenant-level
        $tenantId = tenancy()->tenant?->id;
        
        if ($tenantId) {
            // Tenant-level admin - scoped statistics
            $stats = [
                'total_guru' => Guru::where('tenant_id', $tenantId)->count(),
                'total_siswa' => Siswa::where('tenant_id', $tenantId)->count(),
                'total_ujian' => Ujian::where('tenant_id', $tenantId)->count(),
                'total_kategori' => KategoriUjian::where('tenant_id', $tenantId)->count(),
                'ujian_hari_ini' => Ujian::where('tenant_id', $tenantId)->whereDate('created_at', today())->count(),
            ];
        } else {
            // Platform-level admin - cross-tenant statistics
            $stats = [
                'total_guru' => Guru::count(),
                'total_siswa' => Siswa::count(),
                'total_ujian' => Ujian::count(),
                'total_kategori' => KategoriUjian::count(),
                'ujian_hari_ini' => Ujian::whereDate('created_at', today())->count(),
            ];
        }

        return view('admin.dashboard', $stats);
    }
}
