<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-bold text-base-content">{{ $kategoriUjian->nama }}</h1>
            <a href="{{ route('admin.kategori-ujian.index') }}" class="btn btn-ghost btn-sm">← Kembali</a>
        </div>
    </x-slot>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
        <!-- Info Card -->
        <div class="lg:col-span-1">
            <div class="card bg-base-100 shadow">
                <div class="card-body">
                    <h2 class="card-title text-lg mb-4">Informasi</h2>
                    
                    <div class="space-y-3">
                        <div>
                            <p class="text-xs font-semibold text-base-content/60 uppercase">Status</p>
                            @if ($kategoriUjian->is_active)
                                <p class="badge badge-success">Aktif</p>
                            @else
                                <p class="badge badge-outline">Nonaktif</p>
                            @endif
                        </div>
                        <div>
                            <p class="text-xs font-semibold text-base-content/60 uppercase">Urutan</p>
                            <p class="text-base">{{ $kategoriUjian->urutan ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="text-xs font-semibold text-base-content/60 uppercase">Dibuat</p>
                            <p class="text-sm">{{ $kategoriUjian->created_at->format('d M Y H:i') }}</p>
                        </div>
                        <div>
                            <p class="text-xs font-semibold text-base-content/60 uppercase">Diupdate</p>
                            <p class="text-sm">{{ $kategoriUjian->updated_at->format('d M Y H:i') }}</p>
                        </div>
                    </div>

                    <div class="divider"></div>

                    <div class="flex gap-2">
                        <a href="{{ route('admin.kategori-ujian.edit', $kategoriUjian) }}" class="btn btn-primary btn-sm flex-1">Edit</a>
                        <form action="{{ route('admin.kategori-ujian.destroy', $kategoriUjian) }}" method="POST" class="flex-1">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-error btn-sm w-full" onclick="return confirm('Apakah Anda yakin?')">Hapus</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Deskripsi & Soal -->
        <div class="lg:col-span-2 space-y-4">
            <!-- Deskripsi -->
            @if ($kategoriUjian->deskripsi)
                <div class="card bg-base-100 shadow">
                    <div class="card-body">
                        <h2 class="card-title text-lg mb-2">Deskripsi</h2>
                        <p class="text-base-content/70">{{ $kategoriUjian->deskripsi }}</p>
                    </div>
                </div>
            @endif

            <!-- Soal -->
            <div class="card bg-base-100 shadow">
                <div class="card-body">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="card-title text-lg">Daftar Soal ({{ $kategoriUjian->soal->count() }})</h2>
                    </div>

                    @if ($kategoriUjian->soal->count() > 0)
                        <div class="space-y-2">
                            @foreach ($kategoriUjian->soal as $soal)
                                <div class="border border-base-300 rounded-lg p-3 hover:bg-base-200 transition">
                                    <p class="font-semibold text-sm">{{ Str::limit($soal->pertanyaan, 100) }}</p>
                                    <div class="flex gap-2 mt-2 items-center text-xs text-base-content/60">
                                        <span class="badge badge-sm">{{ ucfirst(str_replace('_', ' ', $soal->tipe_soal)) }}</span>
                                        <span>Bobot: {{ $soal->bobot }}</span>
                                        <span>Oleh: {{ $soal->guru->nama ?? 'Guru Dihapus' }}</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="alert alert-info">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="stroke-current shrink-0 w-6 h-6"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            <span>Belum ada soal untuk kategori ini.</span>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
