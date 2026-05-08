<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-bold text-base-content">Data Siswa</h1>
            <a href="{{ route('admin.siswa.create') }}" class="btn btn-primary btn-sm">+ Tambah Siswa</a>
        </div>
    </x-slot>

    @if(session('success'))
        <div class="alert alert-success mb-4"><span>{{ session('success') }}</span></div>
    @endif

    <!-- Filter Bar -->
    <form method="GET" class="flex flex-wrap gap-3 mb-4">
        <input type="text" name="search" value="{{ request('search') }}"
               placeholder="Cari nama / NIS..."
               class="input input-bordered input-sm w-60" />
        <select name="kelas" class="select select-bordered select-sm">
            <option value="">Semua Kelas</option>
            @foreach($kelasList as $kelas)
                <option value="{{ $kelas }}" {{ request('kelas') === $kelas ? 'selected' : '' }}>{{ $kelas }}</option>
            @endforeach
        </select>
        <button type="submit" class="btn btn-outline btn-sm">Filter</button>
        @if(request()->hasAny(['search', 'kelas']))
            <a href="{{ route('admin.siswa.index') }}" class="btn btn-ghost btn-sm">Reset</a>
        @endif
    </form>

    <div class="card bg-base-100 shadow">
        <div class="card-body p-0">
            <div class="overflow-x-auto">
                <table class="table table-zebra w-full">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Foto</th>
                            <th>NIS</th>
                            <th>Nama</th>
                            <th>Kelas</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($siswas as $siswa)
                            <tr>
                                <td>{{ $loop->iteration + ($siswas->currentPage() - 1) * $siswas->perPage() }}</td>
                                <td>
                                    @if($siswa->foto)
                                        <div class="avatar">
                                            <div class="w-9 rounded-full">
                                                <img src="{{ Storage::url($siswa->foto) }}" alt="{{ $siswa->nama }}" />
                                            </div>
                                        </div>
                                    @else
                                        <div class="avatar placeholder">
                                            <div class="bg-secondary text-secondary-content rounded-full w-9">
                                                <span class="text-xs">{{ strtoupper(substr($siswa->nama, 0, 2)) }}</span>
                                            </div>
                                        </div>
                                    @endif
                                </td>
                                <td class="font-mono text-sm">{{ $siswa->nis }}</td>
                                <td class="font-medium">{{ $siswa->nama }}</td>
                                <td>{{ $siswa->kelas }}</td>
                                <td>
                                    @if($siswa->is_active)
                                        <span class="badge badge-success badge-sm">Aktif</span>
                                    @else
                                        <span class="badge badge-error badge-sm">Nonaktif</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="flex gap-1 flex-wrap">
                                        <a href="{{ route('admin.siswa.show', $siswa) }}" class="btn btn-ghost btn-xs">Lihat</a>
                                        <a href="{{ route('admin.siswa.edit', $siswa) }}" class="btn btn-info btn-xs">Edit</a>
                                        @if($siswa->is_active)
                                            <form method="POST" action="{{ route('admin.siswa.deactivate', $siswa) }}">
                                                @csrf
                                                <button type="submit" class="btn btn-warning btn-xs">Nonaktifkan</button>
                                            </form>
                                        @else
                                            <form method="POST" action="{{ route('admin.siswa.activate', $siswa) }}">
                                                @csrf
                                                <button type="submit" class="btn btn-success btn-xs">Aktifkan</button>
                                            </form>
                                        @endif
                                        <form method="POST" action="{{ route('admin.siswa.destroy', $siswa) }}"
                                              onsubmit="return confirm('Hapus siswa {{ $siswa->nama }}?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-error btn-xs">Hapus</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-base-content/40 py-8">
                                    Belum ada data siswa.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($siswas->hasPages())
                <div class="p-4">{{ $siswas->links() }}</div>
            @endif
        </div>
    </div>
</x-app-layout>
