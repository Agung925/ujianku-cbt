<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-bold text-base-content">Daftar Ujian</h1>
            <a href="{{ route('guru.ujian.create') }}" class="btn btn-primary btn-sm">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                + Buat Ujian
            </a>
        </div>
    </x-slot>

    @if (session('success'))
        <div class="alert alert-success shadow-lg mb-4">
            <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-error shadow-lg mb-4">
            <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    @if ($ujians->count() > 0)
        <div class="card bg-base-100 shadow overflow-x-auto">
            <table class="table table-compact w-full">
                <thead>
                    <tr class="bg-base-200">
                        <th class="w-12">#</th>
                        <th>Judul Ujian</th>
                        <th class="w-32">Kategori</th>
                        <th class="w-40">Waktu Mulai</th>
                        <th class="w-40">Waktu Selesai</th>
                        <th class="w-20">Durasi</th>
                        <th class="w-16">Soal</th>
                        <th class="w-20">Status</th>
                        <th class="w-40">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($ujians as $index => $ujian)
                        <tr class="hover">
                            <td>{{ ($ujians->currentPage() - 1) * 15 + $index + 1 }}</td>
                            <td>
                                <a href="{{ route('guru.ujian.show', $ujian->id) }}" class="font-semibold link link-hover">
                                    {{ $ujian->judul }}
                                </a>
                                @if ($ujian->deskripsi)
                                    <div class="text-xs text-base-content/60 mt-0.5">{{ Str::limit($ujian->deskripsi, 50) }}</div>
                                @endif
                            </td>
                            <td><span class="text-sm">{{ $ujian->kategoriUjian?->nama ?? '-' }}</span></td>
                            <td class="text-sm">{{ $ujian->tgl_mulai->format('d M Y H:i') }}</td>
                            <td class="text-sm">{{ $ujian->tgl_selesai->format('d M Y H:i') }}</td>
                            <td class="text-sm">{{ $ujian->waktu_durasi }} mnt</td>
                            <td class="text-center">{{ $ujian->soal->count() }}</td>
                            <td>
                                @if ($ujian->is_active)
                                    <span class="badge badge-success badge-sm">Aktif</span>
                                @else
                                    <span class="badge badge-ghost badge-sm">Nonaktif</span>
                                @endif
                            </td>
                            <td>
                                <div class="flex gap-1">
                                    <a href="{{ route('guru.ujian.show', $ujian->id) }}" class="btn btn-ghost btn-xs" title="Detail">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                                    </a>
                                    @if ($ujian->tgl_mulai->isFuture())
                                        <a href="{{ route('guru.ujian.edit', $ujian->id) }}" class="btn btn-ghost btn-xs" title="Edit">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                                        </a>
                                    @endif
                                    <form action="{{ route('guru.ujian.destroy', $ujian->id) }}" method="POST" onsubmit="return confirm('Hapus ujian ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-ghost btn-xs text-error" title="Hapus">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $ujians->links() }}
        </div>
    @else
        <div class="card bg-base-100 shadow">
            <div class="card-body text-center py-16">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mx-auto text-base-content/30 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                <h3 class="text-lg font-semibold text-base-content/60 mb-2">Belum ada ujian</h3>
                <p class="text-base-content/50 mb-4">Buat ujian pertama Anda untuk memulai.</p>
                <a href="{{ route('guru.ujian.create') }}" class="btn btn-primary btn-sm mx-auto">
                    + Buat Ujian Pertama
                </a>
            </div>
        </div>
    @endif
</x-app-layout>
