<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Http\Requests\UjianRequest;
use App\Models\KategoriUjian;
use App\Models\Soal;
use App\Models\Ujian;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class UjianController extends Controller
{
    /**
     * List semua ujian milik guru yang login.
     */
    public function index(): View
    {
        $user = Auth::user();
        $tenantId = tenancy()->tenant?->id;

        $ujians = Ujian::where('tenant_id', $tenantId)
            ->where('guru_id', $user->guru->id)
            ->with(['kategoriUjian', 'soal'])
            ->orderByDesc('created_at')
            ->paginate(15);

        return view('guru.ujian.index', compact('ujians'));
    }

    /**
     * Form create ujian baru.
     */
    public function create(): View|RedirectResponse
    {
        $tenantId = tenancy()->tenant?->id;
        $user = Auth::user();

        if (!$tenantId || !$user->guru) {
            return redirect()->route('guru.ujian.index')
                ->with('error', 'Konteks tenant tidak valid.');
        }

        $kategoriUjians = KategoriUjian::where('tenant_id', $tenantId)
            ->where('is_active', true)
            ->orderBy('nama')
            ->get();

        return view('guru.ujian.create', compact('kategoriUjians'));
    }

    /**
     * Simpan ujian baru.
     */
    public function store(UjianRequest $request): RedirectResponse
    {
        $user = Auth::user();
        $tenantId = tenancy()->tenant?->id;

        $ujian = Ujian::create([
            'tenant_id'         => $tenantId,
            'guru_id'           => $user->guru->id,
            'kategori_ujian_id' => $request->kategori_ujian_id,
            'judul'             => $request->judul,
            'deskripsi'         => $request->deskripsi,
            'tgl_mulai'         => $request->tgl_mulai,
            'tgl_selesai'       => $request->tgl_selesai,
            'waktu_durasi'      => $request->waktu_durasi,
            'is_acak_soal'      => $request->is_acak_soal,
            'is_acak_opsi'      => $request->is_acak_opsi,
            'is_active'         => false,
        ]);

        return redirect()->route('guru.ujian.show', $ujian->id)
            ->with('success', 'Ujian berhasil dibuat. Tambahkan soal untuk melengkapi ujian.');
    }

    /**
     * Detail ujian + daftar soal.
     */
    public function show(int $id): View|RedirectResponse
    {
        $ujian = $this->findOwnedUjian($id);
        if (!$ujian) {
            return redirect()->route('guru.ujian.index')
                ->with('error', 'Ujian tidak ditemukan.');
        }

        $ujian->load(['kategoriUjian', 'soal' => fn($q) => $q->orderByPivot('urutan')]);

        return view('guru.ujian.show', compact('ujian'));
    }

    /**
     * Form edit ujian (hanya jika belum mulai).
     */
    public function edit(int $id): View|RedirectResponse
    {
        $ujian = $this->findOwnedUjian($id);
        if (!$ujian) {
            return redirect()->route('guru.ujian.index')
                ->with('error', 'Ujian tidak ditemukan.');
        }

        if ($ujian->tgl_mulai->isPast()) {
            return redirect()->route('guru.ujian.show', $ujian->id)
                ->with('error', 'Ujian yang sudah dimulai tidak dapat diedit.');
        }

        $tenantId = tenancy()->tenant?->id;
        $kategoriUjians = KategoriUjian::where('tenant_id', $tenantId)
            ->where('is_active', true)
            ->orderBy('nama')
            ->get();

        return view('guru.ujian.edit', compact('ujian', 'kategoriUjians'));
    }

    /**
     * Update ujian.
     */
    public function update(UjianRequest $request, int $id): RedirectResponse
    {
        $ujian = $this->findOwnedUjian($id);
        if (!$ujian) {
            return redirect()->route('guru.ujian.index')
                ->with('error', 'Ujian tidak ditemukan.');
        }

        if ($ujian->tgl_mulai->isPast()) {
            return redirect()->route('guru.ujian.show', $ujian->id)
                ->with('error', 'Ujian yang sudah dimulai tidak dapat diedit.');
        }

        $ujian->update([
            'kategori_ujian_id' => $request->kategori_ujian_id,
            'judul'             => $request->judul,
            'deskripsi'         => $request->deskripsi,
            'tgl_mulai'         => $request->tgl_mulai,
            'tgl_selesai'       => $request->tgl_selesai,
            'waktu_durasi'      => $request->waktu_durasi,
            'is_acak_soal'      => $request->is_acak_soal,
            'is_acak_opsi'      => $request->is_acak_opsi,
        ]);

        return redirect()->route('guru.ujian.show', $ujian->id)
            ->with('success', 'Ujian berhasil diperbarui.');
    }

    /**
     * Hapus ujian.
     */
    public function destroy(int $id): RedirectResponse
    {
        $ujian = $this->findOwnedUjian($id);
        if (!$ujian) {
            return redirect()->route('guru.ujian.index')
                ->with('error', 'Ujian tidak ditemukan.');
        }

        $ujian->soal()->detach();
        $ujian->delete();

        return redirect()->route('guru.ujian.index')
            ->with('success', 'Ujian berhasil dihapus.');
    }

    /**
     * Form kelola soal dalam ujian.
     */
    public function manageQuestions(int $id): View|RedirectResponse
    {
        $ujian = $this->findOwnedUjian($id);
        if (!$ujian) {
            return redirect()->route('guru.ujian.index')
                ->with('error', 'Ujian tidak ditemukan.');
        }

        $tenantId = tenancy()->tenant?->id;
        $user = Auth::user();

        $ujian->load(['soal' => fn($q) => $q->orderByPivot('urutan')]);

        // Soal milik guru ini di kategori yang sama, yang belum masuk ujian ini
        $selectedIds = $ujian->soal->pluck('id')->toArray();

        $availableSoal = Soal::where('tenant_id', $tenantId)
            ->where('guru_id', $user->guru->id)
            ->where('kategori_ujian_id', $ujian->kategori_ujian_id)
            ->where('is_active', true)
            ->whereNotIn('id', $selectedIds)
            ->orderBy('pertanyaan')
            ->get();

        return view('guru.ujian.manage-questions', compact('ujian', 'availableSoal'));
    }

    /**
     * Simpan soal yang dipilih ke ujian.
     */
    public function assignQuestions(Request $request, int $id): RedirectResponse
    {
        $ujian = $this->findOwnedUjian($id);
        if (!$ujian) {
            return redirect()->route('guru.ujian.index')
                ->with('error', 'Ujian tidak ditemukan.');
        }

        $soalIds = $request->input('soal_ids', []);

        if (empty($soalIds)) {
            return redirect()->route('guru.ujian.manage-questions', $ujian->id)
                ->with('error', 'Pilih minimal satu soal.');
        }

        // Validasi soal milik guru & tenant yang sama
        $tenantId = tenancy()->tenant?->id;
        $user = Auth::user();

        $validSoal = Soal::where('tenant_id', $tenantId)
            ->where('guru_id', $user->guru->id)
            ->whereIn('id', $soalIds)
            ->pluck('id')
            ->toArray();

        if (empty($validSoal)) {
            return redirect()->route('guru.ujian.manage-questions', $ujian->id)
                ->with('error', 'Soal yang dipilih tidak valid.');
        }

        // Hitung urutan awal berdasarkan soal yang sudah ada
        $maxUrutan = $ujian->soal()->max('urutan') ?? 0;

        $pivotData = [];
        foreach ($validSoal as $index => $soalId) {
            $pivotData[$soalId] = ['urutan' => $maxUrutan + $index + 1];
        }

        $ujian->soal()->attach($pivotData);

        return redirect()->route('guru.ujian.manage-questions', $ujian->id)
            ->with('success', count($validSoal) . ' soal berhasil ditambahkan.');
    }

    /**
     * Hapus satu soal dari ujian.
     */
    public function removeQuestion(int $id, int $soalId): RedirectResponse
    {
        $ujian = $this->findOwnedUjian($id);
        if (!$ujian) {
            return redirect()->route('guru.ujian.index')
                ->with('error', 'Ujian tidak ditemukan.');
        }

        $ujian->soal()->detach($soalId);

        return redirect()->route('guru.ujian.manage-questions', $ujian->id)
            ->with('success', 'Soal berhasil dihapus dari ujian.');
    }

    /**
     * Aktifkan ujian (siswa bisa akses).
     */
    public function activate(int $id): RedirectResponse
    {
        $ujian = $this->findOwnedUjian($id);
        if (!$ujian) {
            return redirect()->route('guru.ujian.index')
                ->with('error', 'Ujian tidak ditemukan.');
        }

        if ($ujian->soal()->count() === 0) {
            return redirect()->route('guru.ujian.show', $ujian->id)
                ->with('error', 'Tambahkan minimal satu soal sebelum mengaktifkan ujian.');
        }

        $ujian->update(['is_active' => true]);

        return redirect()->route('guru.ujian.show', $ujian->id)
            ->with('success', 'Ujian berhasil diaktifkan.');
    }

    /**
     * Nonaktifkan ujian (siswa tidak bisa akses).
     */
    public function deactivate(int $id): RedirectResponse
    {
        $ujian = $this->findOwnedUjian($id);
        if (!$ujian) {
            return redirect()->route('guru.ujian.index')
                ->with('error', 'Ujian tidak ditemukan.');
        }

        $ujian->update(['is_active' => false]);

        return redirect()->route('guru.ujian.show', $ujian->id)
            ->with('success', 'Ujian berhasil dinonaktifkan.');
    }

    /**
     * Helper: cari ujian milik guru yang sedang login di tenant ini.
     */
    private function findOwnedUjian(int $id): ?Ujian
    {
        $tenantId = tenancy()->tenant?->id;
        $user = Auth::user();

        if (!$tenantId || !$user->guru) {
            return null;
        }

        return Ujian::where('id', $id)
            ->where('tenant_id', $tenantId)
            ->where('guru_id', $user->guru->id)
            ->first();
    }
}
