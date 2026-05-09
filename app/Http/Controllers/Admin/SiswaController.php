<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\SiswaRequest;
use App\Models\Siswa;
use App\Services\FileUploadService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class SiswaController extends Controller
{
    public function __construct(private FileUploadService $uploadService) {}

    public function index(Request $request): View
    {
        $query = Siswa::orderBy('kelas')->orderBy('nama');

        if ($request->filled('kelas')) {
            $query->where('kelas', $request->kelas);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'ilike', "%{$search}%")
                  ->orWhere('nis', 'ilike', "%{$search}%");
            });
        }

        $siswas = $query->paginate(20)->withQueryString();
        $kelasList = Siswa::select('kelas')->distinct()->orderBy('kelas')->pluck('kelas');

        return view('admin.siswa.index', compact('siswas', 'kelasList'));
    }

    public function create(): View
    {
        return view('admin.siswa.create');
    }

    public function store(SiswaRequest $request): RedirectResponse
    {
        $data = $request->validated();
        
        // Get tenant context (admin can manage their tenant)
        $tenantId = tenancy()->tenant?->id ?? \Stancl\Tenancy\Database\Models\Tenant::first()?->id;
        
        if (!$tenantId) {
            return redirect()->back()
                ->withErrors('Tidak ada tenant yang tersedia untuk membuat siswa.');
        }

        Siswa::create([
            'tenant_id' => $tenantId,
            'nis'       => $data['nis'],
            'nama'      => $data['nama'],
            'kelas'     => $data['kelas'],
            'email'     => $data['email'] ?? null,
            'password'  => Hash::make($data['nis']), // Default password = NIS
            'is_active' => $data['is_active'] ?? true,
        ]);

        return redirect()->route('admin.siswa.index')
            ->with('success', 'Siswa berhasil ditambahkan. Password default = NIS.');
    }

    public function show(Siswa $siswa): View
    {
        return view('admin.siswa.show', compact('siswa'));
    }

    public function edit(Siswa $siswa): View
    {
        return view('admin.siswa.edit', compact('siswa'));
    }

    public function update(SiswaRequest $request, Siswa $siswa): RedirectResponse
    {
        $data = $request->validated();

        $siswa->update([
            'nis'       => $data['nis'],
            'nama'      => $data['nama'],
            'kelas'     => $data['kelas'],
            'email'     => $data['email'] ?? null,
            'is_active' => $data['is_active'] ?? $siswa->is_active,
        ]);

        return redirect()->route('admin.siswa.index')
            ->with('success', 'Data siswa berhasil diperbarui.');
    }

    public function destroy(Siswa $siswa): RedirectResponse
    {
        $siswa->delete(); // Soft delete

        return redirect()->route('admin.siswa.index')
            ->with('success', 'Siswa berhasil dihapus.');
    }

    public function activate(Siswa $siswa): RedirectResponse
    {
        $siswa->update(['is_active' => true]);

        return back()->with('success', "Akun siswa {$siswa->nama} berhasil diaktifkan.");
    }

    public function deactivate(Siswa $siswa): RedirectResponse
    {
        $siswa->update(['is_active' => false]);

        return back()->with('success', "Akun siswa {$siswa->nama} berhasil dinonaktifkan.");
    }

    public function uploadPhoto(Request $request, Siswa $siswa): RedirectResponse
    {
        $request->validate([
            'foto' => ['required', 'file', 'max:2048', 'mimes:jpg,jpeg,png'],
        ]);

        // Hapus foto lama jika ada
        if ($siswa->foto) {
            $this->uploadService->deleteFile($siswa->foto);
        }

        $path = $this->uploadService->uploadStudentPhoto(
            $request->file('foto'),
            $siswa->id
        );

        $siswa->update(['foto' => $path]);

        return back()->with('success', 'Foto siswa berhasil diperbarui.');
    }

    public function resetPassword(Siswa $siswa): RedirectResponse
    {
        $siswa->update(['password' => \Illuminate\Support\Facades\Hash::make($siswa->nis)]);

        return back()->with('success', "Password siswa {$siswa->nama} berhasil direset ke NIS.");
    }
}
