<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-bold text-base-content">Data Guru</h1>
            <a href="{{ route('admin.guru.create') }}" class="btn btn-primary btn-sm">
                + Tambah Guru
            </a>
        </div>
    </x-slot>

    @if(session('success'))
        <div class="alert alert-success mb-4">
            <span>{{ session('success') }}</span>
        </div>
    @endif

    <div class="card bg-base-100 shadow">
        <div class="card-body p-0">
            <div class="overflow-x-auto">
                <table class="table table-zebra w-full">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Foto</th>
                            <th>Nama</th>
                            <th>Email</th>
                            <th>NIP</th>
                            <th>Wali Kelas</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($gurus as $guru)
                            <tr>
                                <td>{{ $loop->iteration + ($gurus->currentPage() - 1) * $gurus->perPage() }}</td>
                                <td>
                                    @if($guru->foto_profil)
                                        <div class="avatar">
                                            <div class="w-10 rounded-full">
                                                <img src="{{ Storage::url($guru->foto_profil) }}" alt="{{ $guru->nama }}" />
                                            </div>
                                        </div>
                                    @else
                                        <div class="avatar placeholder">
                                            <div class="bg-neutral text-neutral-content rounded-full w-10">
                                                <span class="text-xs">{{ strtoupper(substr($guru->nama, 0, 2)) }}</span>
                                            </div>
                                        </div>
                                    @endif
                                </td>
                                <td class="font-medium">{{ $guru->nama }}</td>
                                <td>{{ $guru->email }}</td>
                                <td>{{ $guru->nip ?? '-' }}</td>
                                <td>
                                    @if($guru->is_wali_kelas)
                                        <span class="badge badge-info badge-sm">Wali Kelas</span>
                                    @else
                                        <span class="text-base-content/40 text-sm">-</span>
                                    @endif
                                </td>
                                <td>
                                    @if($guru->is_active)
                                        <span class="badge badge-success badge-sm">Aktif</span>
                                    @else
                                        <span class="badge badge-error badge-sm">Nonaktif</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="flex gap-1">
                                        <a href="{{ route('admin.guru.show', $guru) }}" class="btn btn-ghost btn-xs">Lihat</a>
                                        <a href="{{ route('admin.guru.edit', $guru) }}" class="btn btn-info btn-xs">Edit</a>
                                        <form method="POST" action="{{ route('admin.guru.destroy', $guru) }}"
                                              onsubmit="return confirm('Hapus guru {{ $guru->nama }}?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-error btn-xs">Hapus</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center text-base-content/40 py-8">
                                    Belum ada data guru.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($gurus->hasPages())
                <div class="p-4">
                    {{ $gurus->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
