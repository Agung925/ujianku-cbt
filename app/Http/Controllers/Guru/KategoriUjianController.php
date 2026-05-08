<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\KategoriUjian;
use Illuminate\View\View;

class KategoriUjianController extends Controller
{
    /**
     * Display a listing of kategori ujian (READ ONLY).
     */
    public function index(): View
    {
        $kategoriUjians = KategoriUjian::where('is_active', true)
            ->orderBy('urutan')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('guru.kategori-ujian.index', compact('kategoriUjians'));
    }

    /**
     * Display the specified kategori ujian dengan list soal.
     */
    public function show(KategoriUjian $kategoriUjian): View
    {
        $kategoriUjian->load('soals');

        return view('guru.kategori-ujian.show', compact('kategoriUjian'));
    }
}
