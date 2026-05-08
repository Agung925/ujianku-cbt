<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\KategoriUjianRequest;
use App\Models\KategoriUjian;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class KategoriUjianController extends Controller
{
    /**
     * Display a listing of kategori ujian.
     */
    public function index(): View
    {
        $tenantId = tenancy()->tenant?->id ?? 'sekolah-1';
        
        $kategoriUjians = KategoriUjian::where('tenant_id', $tenantId)
            ->orderBy('urutan')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('admin.kategori-ujian.index', compact('kategoriUjians'));
    }

    /**
     * Show the form for creating a new kategori ujian.
     */
    public function create(): View
    {
        return view('admin.kategori-ujian.create');
    }

    /**
     * Store a newly created kategori ujian in storage.
     */
    public function store(KategoriUjianRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        // For development: use first tenant or sekolah-1 as default
        // In production, this should be determined by admin user's tenant association
        $tenantId = tenancy()->tenant?->id ?? 'sekolah-1';

        KategoriUjian::create([
            ...$validated,
            'tenant_id' => $tenantId,
            'is_active' => $request->boolean('is_active', true),
        ]);

        return redirect()->route('admin.kategori-ujian.index')
            ->with('success', 'Kategori ujian berhasil dibuat');
    }

    /**
     * Display the specified kategori ujian.
     */
    public function show(KategoriUjian $kategoriUjian): View
    {
        $kategoriUjian->load('soal');

        return view('admin.kategori-ujian.show', compact('kategoriUjian'));
    }

    /**
     * Show the form for editing the specified kategori ujian.
     */
    public function edit(KategoriUjian $kategoriUjian): View
    {
        return view('admin.kategori-ujian.edit', compact('kategoriUjian'));
    }

    /**
     * Update the specified kategori ujian in storage.
     */
    public function update(KategoriUjianRequest $request, KategoriUjian $kategoriUjian): RedirectResponse
    {
        $validated = $request->validated();

        $kategoriUjian->update([
            ...$validated,
            'is_active' => $request->boolean('is_active', $kategoriUjian->is_active),
        ]);

        return redirect()->route('admin.kategori-ujian.index')
            ->with('success', 'Kategori ujian berhasil diupdate');
    }

    /**
     * Remove the specified kategori ujian from storage.
     */
    public function destroy(KategoriUjian $kategoriUjian): RedirectResponse
    {
        $kategoriUjian->delete();

        return redirect()->route('admin.kategori-ujian.index')
            ->with('success', 'Kategori ujian berhasil dihapus');
    }
}
