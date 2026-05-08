<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <a href="{{ route('guru.ujian.index') }}" class="btn btn-ghost btn-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" /></svg>
                </a>
                <h1 class="text-2xl font-bold text-base-content">{{ $ujian->judul }}</h1>
                @if ($ujian->is_active)
                    <span class="badge badge-success">Aktif</span>
                @else
                    <span class="badge badge-ghost">Nonaktif</span>
                @endif
            </div>
            <div class="flex gap-2">
                @if ($ujian->is_active)
                    <form action="{{ route('guru.ujian.deactivate', $ujian->id) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="btn btn-warning btn-sm" onclick="return confirm('Nonaktifkan ujian ini?')">
                            Nonaktifkan
                        </button>
                    </form>
                @else
                    <form action="{{ route('guru.ujian.activate', $ujian->id) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="btn btn-success btn-sm" onclick="return confirm('Aktifkan ujian ini?')">
                            Aktifkan
                        </button>
                    </form>
                @endif
                @if ($ujian->tgl_mulai->isFuture())
                    <a href="{{ route('guru.ujian.edit', $ujian->id) }}" class="btn btn-outline btn-sm">Edit</a>
                @endif
            </div>
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

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Info Ujian -->
        <div class="lg:col-span-1 space-y-4">
            <div class="card bg-base-100 shadow">
                <div class="card-body">
                    <h2 class="card-title text-base">Informasi Ujian</h2>
                    <div class="space-y-3 text-sm">
                        <div>
                            <div class="text-base-content/60">Kategori</div>
                            <div class="font-medium">{{ $ujian->kategoriUjian?->nama ?? '-' }}</div>
                        </div>
                        <div>
                            <div class="text-base-content/60">Mulai</div>
                            <div class="font-medium">{{ $ujian->tgl_mulai->format('d M Y, H:i') }}</div>
                        </div>
                        <div>
                            <div class="text-base-content/60">Selesai</div>
                            <div class="font-medium">{{ $ujian->tgl_selesai->format('d M Y, H:i') }}</div>
                        </div>
                        <div>
                            <div class="text-base-content/60">Durasi</div>
                            <div class="font-medium">{{ $ujian->waktu_durasi }} menit</div>
                        </div>
                        <div>
                            <div class="text-base-content/60">Acak Soal</div>
                            <div class="font-medium">{{ $ujian->is_acak_soal ? 'Ya' : 'Tidak' }}</div>
                        </div>
                        <div>
                            <div class="text-base-content/60">Acak Opsi</div>
                            <div class="font-medium">{{ $ujian->is_acak_opsi ? 'Ya' : 'Tidak' }}</div>
                        </div>
                        @if ($ujian->deskripsi)
                            <div>
                                <div class="text-base-content/60">Deskripsi</div>
                                <div>{{ $ujian->deskripsi }}</div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Danger Zone -->
            <div class="card bg-base-100 shadow border border-error/20">
                <div class="card-body">
                    <h2 class="card-title text-base text-error">Zona Berbahaya</h2>
                    <form action="{{ route('guru.ujian.destroy', $ujian->id) }}" method="POST" onsubmit="return confirm('Hapus ujian ini? Semua data soal terkait akan ikut terhapus.')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-error btn-outline btn-sm w-full">
                            Hapus Ujian Ini
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Daftar Soal -->
        <div class="lg:col-span-2">
            <div class="card bg-base-100 shadow">
                <div class="card-body">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="card-title text-base">
                            Daftar Soal
                            <span class="badge badge-neutral ml-1">{{ $ujian->soal->count() }}</span>
                        </h2>
                        <a href="{{ route('guru.ujian.manage-questions', $ujian->id) }}" class="btn btn-primary btn-sm">
                            + Kelola Soal
                        </a>
                    </div>

                    @if ($ujian->soal->count() > 0)
                        <div class="space-y-2">
                            @foreach ($ujian->soal as $index => $soal)
                                <div class="flex items-start gap-3 p-3 rounded-lg bg-base-200/50">
                                    <span class="badge badge-outline badge-sm mt-0.5 min-w-[2rem] justify-center">{{ $soal->pivot->urutan }}</span>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium">{{ Str::limit($soal->pertanyaan, 100) }}</p>
                                        <div class="flex gap-2 mt-1">
                                            <span class="text-xs text-base-content/50">Tipe: {{ $soal->tipe_soal }}</span>
                                            <span class="text-xs text-base-content/50">Bobot: {{ $soal->bobot }}</span>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8 text-base-content/50">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 mx-auto mb-2 opacity-40" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                            <p class="text-sm">Belum ada soal dalam ujian ini.</p>
                            <a href="{{ route('guru.ujian.manage-questions', $ujian->id) }}" class="btn btn-sm btn-primary mt-3">
                                Tambah Soal Sekarang
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
