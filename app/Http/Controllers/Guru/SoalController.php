<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Http\Requests\SoalRequest;
use App\Models\KategoriUjian;
use App\Models\Soal;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * @method void authorize(string $ability, mixed $arguments = null)
 * @method \Illuminate\View\View view(string $view, array $data = [])
 */
class SoalController extends Controller
{
    /**
     * Display a listing of soal milik guru ini.
     */
    public function index(Request $request): View
    {
        $authId = Auth::id();
        $query = Soal::where('guru_id', $authId);

        // Filter by kategori if provided
        if ($request->filled('kategori_ujian_id')) {
            $query->where('kategori_ujian_id', $request->kategori_ujian_id);
        }

        // Search by pertanyaan if provided
        if ($request->filled('search')) {
            $query->where('pertanyaan', 'like', '%' . $request->search . '%');
        }

        $soals = $query->with('kategoriUjian')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        $kategoriUjians = KategoriUjian::where('is_active', true)
            ->orderBy('urutan')
            ->get();

        return view('guru.soal.index', compact('soals', 'kategoriUjians'));
    }

    /**
     * Show the form for creating a new soal.
     */
    public function create(): View
    {
        $kategoriUjians = KategoriUjian::where('is_active', true)
            ->orderBy('urutan')
            ->get();

        return view('guru.soal.create', compact('kategoriUjians'));
    }

    /**
     * Store a newly created soal in storage.
     */
    public function store(SoalRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $authId = Auth::id();
        
        Soal::create([
            ...$validated,
            'guru_id' => $authId,
            'is_active' => $request->boolean('is_active', true),
        ]);

        return redirect()->route('guru.soal.index')
            ->with('success', 'Soal berhasil dibuat');
    }

    /**
     * Show the form for editing the specified soal.
     */
    public function edit(Soal $soal): View
    {
        $this->authorize('update', $soal);

        $kategoriUjians = KategoriUjian::where('is_active', true)
            ->orderBy('urutan')
            ->get();

        return view('guru.soal.edit', compact('soal', 'kategoriUjians'));
    }

    /**
     * Update the specified soal in storage.
     */
    public function update(SoalRequest $request, Soal $soal): RedirectResponse
    {
        $this->authorize('update', $soal);

        $validated = $request->validated();

        $soal->update([
            ...$validated,
            'is_active' => $request->boolean('is_active', $soal->is_active),
        ]);

        return redirect()->route('guru.soal.index')
            ->with('success', 'Soal berhasil diupdate');
    }

    /**
     * Remove the specified soal from storage.
     */
    public function destroy(Soal $soal): RedirectResponse
    {
        $this->authorize('delete', $soal);

        $soal->delete();

        return redirect()->route('guru.soal.index')
            ->with('success', 'Soal berhasil dihapus');
    }

    /**
     * Duplicate the specified soal.
     */
    public function duplicate(Soal $soal): RedirectResponse
    {
        $this->authorize('view', $soal);

        $newSoal = $soal->replicate();
        $newSoal->push();

        return redirect()->route('guru.soal.edit', $newSoal->id)
            ->with('success', 'Soal berhasil diduplikat. Silakan ubah jika diperlukan.');
    }
}
