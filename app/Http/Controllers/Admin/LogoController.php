<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\LogoHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\FileUploadRequest;
use App\Models\LogoIdentitas;
use App\Services\FileUploadService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Route;
use Illuminate\View\View;
use Stancl\Tenancy\Database\Models\Tenant;

class LogoController extends Controller
{
    protected $fileUploadService;

    public function __construct(FileUploadService $fileUploadService)
    {
        $this->fileUploadService = $fileUploadService;
    }

    /**
     * Display list of all tenants dan logos mereka
     */
    public function index(): View
    {
        // Ambil semua tenant (admin bisa manage semua)
        $tenants = Tenant::with(['logos' => function ($query) {
            $query->latest('uploaded_at')->limit(1);
        }])->paginate(15);

        return view('admin.logo.index', compact('tenants'));
    }

    /**
     * Show upload form untuk logo tenant
     */
    public function edit($tenantId): View
    {
        $tenant = Tenant::findOrFail($tenantId);
        $currentLogos = LogoIdentitas::where('tenant_id', $tenantId)
            ->latest('uploaded_at')
            ->get();

        return view('admin.logo.edit', compact('tenant', 'currentLogos'));
    }

    /**
     * Save logo baru untuk tenant
     */
    public function update($tenantId, FileUploadRequest $request): RedirectResponse
    {
        $tenant = Tenant::findOrFail($tenantId);

        if (!$request->hasFile('logo')) {
            return redirect()
                ->back()
                ->with('error', 'File logo harus dipilih');
        }

        try {
            $file = $request->file('logo');

            // Validate file size (max 1MB untuk logo)
            if ($file->getSize() > 1048576) { // 1MB in bytes
                return redirect()
                    ->back()
                    ->with('error', 'Ukuran file maksimal 1MB');
            }

            // Store file di direktori tenant-specific
            $filename = time() . '_' . preg_replace('/\s+/', '_', $file->getClientOriginalName());
            $path = 'tenants/' . $tenantId . '/logos/' . $filename;

            Storage::disk('public')->putFileAs(
                'tenants/' . $tenantId . '/logos',
                $file,
                $filename
            );

            // Create LogoIdentitas record
            LogoIdentitas::create([
                'tenant_id' => $tenantId,
                'nama_file' => $filename,
                'path' => $path,
                'file_type' => pathinfo($filename, PATHINFO_EXTENSION),
                'mime_type' => $file->getMimeType(),
                'size' => $file->getSize(),
                'uploaded_by' => auth()->id(),
                'uploaded_at' => now(),
            ]);

            // Clear cache
            \Cache::forget('tenant_' . $tenantId . '_logo');

            return redirect()
                ->route('admin.logo.edit', $tenantId)
                ->with('success', 'Logo berhasil diupload');

        } catch (\Exception $e) {
            \Log::error('Logo upload error: ' . $e->getMessage());

            return redirect()
                ->back()
                ->with('error', 'Gagal mengupload logo: ' . $e->getMessage());
        }
    }

    /**
     * Delete logo tertentu
     */
    public function destroy($logoId): RedirectResponse
    {
        try {
            $logo = LogoIdentitas::findOrFail($logoId);
            $tenantId = $logo->tenant_id;

            // Delete file dari storage
            if (Storage::disk('public')->exists($logo->path)) {
                Storage::disk('public')->delete($logo->path);
            }

            // Delete record
            $logo->delete();

            // Clear cache
            \Cache::forget('tenant_' . $tenantId . '_logo');

            return redirect()
                ->back()
                ->with('success', 'Logo berhasil dihapus');

        } catch (\Exception $e) {
            \Log::error('Logo delete error: ' . $e->getMessage());

            return redirect()
                ->back()
                ->with('error', 'Gagal menghapus logo');
        }
    }

    /**
     * Show tenant logo details
     */
    public function show($tenantId): View
    {
        $tenant = Tenant::findOrFail($tenantId);
        $logos = LogoIdentitas::where('tenant_id', $tenantId)
            ->latest('uploaded_at')
            ->get();

        $currentLogo = $logos->first();
        $logoUrl = LogoHelper::getLogoUrl($tenantId);

        return view('admin.logo.show', compact('tenant', 'logos', 'currentLogo', 'logoUrl'));
    }

    /**
     * Restore logo lama (set as active)
     */
    public function restore($logoId): RedirectResponse
    {
        try {
            $logo = LogoIdentitas::findOrFail($logoId);
            $tenantId = $logo->tenant_id;

            // Update timestamps untuk jadi yang paling baru
            $logo->update([
                'uploaded_at' => now(),
            ]);

            // Clear cache
            \Cache::forget('tenant_' . $tenantId . '_logo');

            return redirect()
                ->back()
                ->with('success', 'Logo berhasil diaktifkan kembali');

        } catch (\Exception $e) {
            \Log::error('Logo restore error: ' . $e->getMessage());

            return redirect()
                ->back()
                ->with('error', 'Gagal mengaktifkan logo');
        }
    }
}
