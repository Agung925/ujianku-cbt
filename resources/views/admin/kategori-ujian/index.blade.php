<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-bold text-base-content">Kategori Ujian</h1>
            <a href="{{ route('admin.kategori-ujian.create') }}" class="btn btn-primary btn-sm">+ Buat Kategori</a>
        </div>
    </x-slot>

    @if (session('success'))
        <div class="alert alert-success shadow-lg mb-4">
            <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
            <div>
                <h3 class="font-bold">Berhasil!</h3>
                <div class="text-sm">{{ session('success') }}</div>
            </div>
        </div>
    @endif

    <div class="card bg-base-100 shadow">
        <div class="card-body">
            <h2 class="card-title mb-4">Daftar Kategori Ujian</h2>
            
            @if ($kategoriUjians->count() > 0)
                <div class="overflow-x-auto">
                    <table class="table table-compact w-full">
                        <thead>
                            <tr class="bg-base-200">
                                <th>#</th>
                                <th>Nama Kategori</th>
                                <th>Deskripsi</th>
                                <th>Urutan</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($kategoriUjians as $kategori)
                                <tr class="hover:bg-base-200">
                                    <td>{{ $loop->iteration + ($kategoriUjians->currentPage() - 1) * $kategoriUjians->perPage() }}</td>
                                    <td class="font-semibold">{{ $kategori->nama }}</td>
                                    <td class="text-sm text-base-content/70">{{ Str::limit($kategori->deskripsi, 50) }}</td>
                                    <td>
                                        <span class="badge badge-sm">{{ $kategori->urutan ?? '-' }}</span>
                                    </td>
                                    <td>
                                        @if ($kategori->is_active)
                                            <span class="badge badge-success badge-sm">Aktif</span>
                                        @else
                                            <span class="badge badge-outline badge-sm">Nonaktif</span>
                                        @endif
                                    </td>
                                    <td class="flex gap-2">
                                        <a href="{{ route('admin.kategori-ujian.show', $kategori) }}" class="btn btn-ghost btn-xs" title="Lihat Detail">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                                        </a>
                                        <a href="{{ route('admin.kategori-ujian.edit', $kategori) }}" class="btn btn-ghost btn-xs" title="Edit">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                                        </a>
                                        <form action="{{ route('admin.kategori-ujian.destroy', $kategori) }}" method="POST" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-ghost btn-xs text-error" onclick="return confirm('Apakah Anda yakin ingin menghapus kategori ini?')" title="Hapus">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="mt-4">
                    {{ $kategoriUjians->links() }}
                </div>
            @else
                <div class="alert alert-info">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="stroke-current shrink-0 w-6 h-6"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <span>Belum ada kategori ujian. <a href="{{ route('admin.kategori-ujian.create') }}" class="link link-primary font-semibold">Buat kategori baru</a></span>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
