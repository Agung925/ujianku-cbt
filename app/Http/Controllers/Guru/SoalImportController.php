<?php

namespace App\Http\Controllers\Guru;

use App\Exports\SoalTemplate;
use App\Http\Controllers\Controller;
use App\Http\Requests\SoalImportRequest;
use App\Imports\SoalImport;
use App\Models\KategoriUjian;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;

class SoalImportController extends Controller
{
    /**
     * Show import form
     */
    public function showForm()
    {
        $tenantId = tenancy()->tenant?->id;
        $user = Auth::user();

        if (!$tenantId || !$user || !$user->guru) {
            return redirect()->route('guru.soal.index')
                ->with('error', 'Invalid tenant context or user is not a guru');
        }

        $kategoriUjian = KategoriUjian::where('tenant_id', $tenantId)
            ->orderBy('nama', 'asc')
            ->get();

        return view('guru.soal.import', [
            'kategoriUjian' => $kategoriUjian,
        ]);
    }

    /**
     * Process import
     */
    public function import(SoalImportRequest $request)
    {
        $tenantId = tenancy()->tenant?->id;
        $user = Auth::user();

        if (!$tenantId || !$user || !$user->guru) {
            return redirect()->route('guru.soal.index')
                ->with('error', 'Invalid tenant context or user is not a guru');
        }

        // Validasi kategori ujian belong to tenant
        $kategoriUjian = KategoriUjian::where('id', $request->kategori_ujian_id)
            ->where('tenant_id', $tenantId)
            ->first();

        if (!$kategoriUjian) {
            return redirect()->back()
                ->with('error', 'Kategori ujian tidak ditemukan atau tidak valid untuk tenant ini')
                ->withInput();
        }

        try {
            // Import Excel file
            $import = new SoalImport($request->kategori_ujian_id);
            Excel::import($import, $request->file('file'));

            $successCount = $import->getSuccessCount();
            $errors = $import->getErrors();
            $errorCount = $import->getErrorCount();

            return view('guru.soal.import-result', [
                'kategoriUjian' => $kategoriUjian,
                'successCount' => $successCount,
                'errors' => $errors,
                'errorCount' => $errorCount,
                'totalProcessed' => $successCount + $errorCount,
            ]);

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error processing file: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Download template
     */
    public function downloadTemplate()
    {
        return Excel::download(
            new SoalTemplate(),
            'Soal_Template_' . date('Y-m-d_His') . '.xlsx'
        );
    }
}
