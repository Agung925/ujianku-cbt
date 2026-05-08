<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Http\Requests\BulkSiswaRequest;
use App\Models\Guru;
use App\Models\Siswa;
use App\Services\FileUploadService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class SiswaManagementController extends Controller
{
    public function __construct(private FileUploadService $uploadService) {}

    private function getGuru(): Guru
    {
        return Guru::where('user_id', Auth::id())->firstOrFail();
    }

    public function index(): View
    {
        $guru = $this->getGuru();

        // Wali kelas hanya bisa lihat siswa di kelas yang diajar
        $siswas = Siswa::orderBy('nama')->paginate(20);

        return view('guru.siswa.index', compact('siswas', 'guru'));
    }

    public function create(): View
    {
        $guru = $this->getGuru();

        if (! $guru->is_wali_kelas) {
            abort(403, 'Hanya wali kelas yang dapat menambah siswa.');
        }

        return view('guru.siswa.create', compact('guru'));
    }

    public function store(BulkSiswaRequest $request): RedirectResponse
    {
        $guru = $this->getGuru();

        if (! $guru->is_wali_kelas) {
            abort(403, 'Hanya wali kelas yang dapat menambah siswa.');
        }

        $created = 0;
        $skipped = 0;

        DB::transaction(function () use ($request, &$created, &$skipped) {
            foreach ($request->validated()['siswas'] as $item) {
                $exists = Siswa::where('nis', $item['nis'])->exists();

                if ($exists) {
                    $skipped++;
                    continue;
                }

                Siswa::create([
                    'nis'      => $item['nis'],
                    'nama'     => $item['nama'],
                    'kelas'    => $item['kelas'],
                    'email'    => $item['email'] ?? null,
                    'password' => Hash::make($item['nis']),
                    'is_active' => true,
                ]);
                $created++;
            }
        });

        $message = "{$created} siswa berhasil ditambahkan.";
        if ($skipped > 0) {
            $message .= " {$skipped} data dilewati karena NIS sudah terdaftar.";
        }

        return redirect()->route('guru.siswa.index')->with('success', $message);
    }

    public function uploadStudentPhoto(Request $request, Siswa $siswa): RedirectResponse
    {
        $guru = $this->getGuru();

        if (! $guru->is_wali_kelas) {
            abort(403, 'Hanya wali kelas yang dapat upload foto siswa.');
        }

        $request->validate([
            'foto' => ['required', 'file', 'max:2048', 'mimes:jpg,jpeg,png'],
        ]);

        if ($siswa->foto) {
            $this->uploadService->deleteFile($siswa->foto);
        }

        $path = $this->uploadService->uploadStudentPhoto($request->file('foto'), $siswa->id);
        $siswa->update(['foto' => $path]);

        return back()->with('success', 'Foto siswa berhasil diperbarui.');
    }
}
