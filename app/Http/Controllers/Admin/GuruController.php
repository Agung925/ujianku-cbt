<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\GuruRequest;
use App\Models\Guru;
use App\Models\User;
use App\Services\FileUploadService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\View\View;

class GuruController extends Controller
{
    public function __construct(private FileUploadService $uploadService) {}

    public function index(): View
    {
        $gurus = Guru::orderBy('nama')->paginate(15);

        return view('admin.guru.index', compact('gurus'));
    }

    public function create(): View
    {
        return view('admin.guru.create');
    }

    public function store(GuruRequest $request): RedirectResponse
    {
        $data = $request->validated();

        DB::transaction(function () use ($data) {
            // Generate NIP otomatis jika kosong
            if (empty($data['nip'])) {
                $data['nip'] = 'NIP-' . strtoupper(Str::random(8));
            }

            // Buat akun User untuk guru (untuk OAuth login)
            $user = User::create([
                'name'     => $data['nama'],
                'email'    => $data['email'],
                'password' => Hash::make($data['nip']),
                'is_active' => true,
            ]);
            $user->assignRole('guru');

            Guru::create([
                'user_id'       => $user->id,
                'email'         => $data['email'],
                'nama'          => $data['nama'],
                'nip'           => $data['nip'],
                'is_wali_kelas' => $data['is_wali_kelas'] ?? false,
                'is_active'     => $data['is_active'] ?? true,
            ]);
        });

        return redirect()->route('admin.guru.index')
            ->with('success', 'Guru berhasil ditambahkan.');
    }

    public function show(Guru $guru): View
    {
        return view('admin.guru.show', compact('guru'));
    }

    public function edit(Guru $guru): View
    {
        return view('admin.guru.edit', compact('guru'));
    }

    public function update(GuruRequest $request, Guru $guru): RedirectResponse
    {
        $data = $request->validated();

        DB::transaction(function () use ($data, $guru) {
            $guru->update([
                'nama'          => $data['nama'],
                'email'         => $data['email'],
                'nip'           => $data['nip'],
                'is_wali_kelas' => $data['is_wali_kelas'] ?? $guru->is_wali_kelas,
                'is_active'     => $data['is_active'] ?? $guru->is_active,
            ]);

            // Sync email ke tabel users
            if ($guru->user) {
                $guru->user->update([
                    'name'  => $data['nama'],
                    'email' => $data['email'],
                ]);
            }
        });

        return redirect()->route('admin.guru.index')
            ->with('success', 'Data guru berhasil diperbarui.');
    }

    public function destroy(Guru $guru): RedirectResponse
    {
        DB::transaction(function () use ($guru) {
            // Nonaktifkan user terkait (tidak hapus permanen)
            if ($guru->user) {
                $guru->user->update(['is_active' => false]);
            }

            $guru->delete();
        });

        return redirect()->route('admin.guru.index')
            ->with('success', 'Guru berhasil dihapus.');
    }

    public function uploadPhoto(Request $request, Guru $guru): RedirectResponse
    {
        $request->validate([
            'foto' => ['required', 'file', 'max:2048', 'mimes:jpg,jpeg,png'],
        ]);

        // Hapus foto lama jika ada
        if ($guru->foto_profil) {
            $this->uploadService->deleteFile($guru->foto_profil);
        }

        $path = $this->uploadService->uploadProfilePhoto(
            $request->file('foto'),
            'guru',
            $guru->id
        );

        $guru->update(['foto_profil' => $path]);

        return back()->with('success', 'Foto profil berhasil diperbarui.');
    }
}
