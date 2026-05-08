<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-bold text-base-content">{{ $kategoriUjian->nama }}</h1>
            <a href="{{ route('guru.kategori-ujian.index') }}" class="btn btn-ghost btn-sm">← Kembali</a>
        </div>
    </x-slot>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
        <!-- Info -->
        <div class="lg:col-span-1">
            <div class="card bg-base-100 shadow">
                <div class="card-body">
                    <h2 class="card-title text-lg mb-4">Informasi</h2>
                    
                    <div class="space-y-3">
                        <div>
                            <p class="text-xs font-semibold text-base-content/60 uppercase">Total Soal</p>
                            <p class="text-2xl font-bold text-primary">{{ $kategoriUjian->soals->count() }}</p>
                        </div>
                        <div>
                            <p class="text-xs font-semibold text-base-content/60 uppercase">Urutan</p>
                            <p class="text-base">{{ $kategoriUjian->urutan ?? '-' }}</p>
                        </div>
                    </div>

                    <div class="divider"></div>

                    <a href="{{ route('guru.soal.create', ['kategori_ujian_id' => $kategoriUjian->id]) }}" class="btn btn-primary btn-sm w-full">
                        + Buat Soal Baru
                    </a>
                </div>
            </div>
        </div>

        <!-- Deskripsi & Soal -->
        <div class="lg:col-span-2 space-y-4">
            @if ($kategoriUjian->deskripsi)
                <div class="card bg-base-100 shadow">
                    <div class="card-body">
                        <h2 class="card-title text-lg mb-2">Deskripsi</h2>
                        <p class="text-base-content/70">{{ $kategoriUjian->deskripsi }}</p>
                    </div>
                </div>
            @endif

            <!-- Daftar Soal -->
            <div class="card bg-base-100 shadow">
                <div class="card-body">
                    <h2 class="card-title text-lg mb-4">Daftar Soal</h2>

                    @if ($kategoriUjian->soals->count() > 0)
                        <div class="space-y-2">
                            @foreach ($kategoriUjian->soals->where('guru_id', auth()->id()) as $soal)
                                <div class="border border-base-300 rounded-lg p-4 hover:bg-base-200 transition">
                                    <div class="flex items-start justify-between mb-2">
                                        <p class="font-semibold flex-1">{{ Str::limit($soal->pertanyaan, 150) }}</p>
                                        <span class="badge badge-sm">{{ ucfirst(str_replace('_', ' ', $soal->tipe_soal)) }}</span>
                                    </div>
                                    <div class="flex gap-2 text-xs text-base-content/60 mb-2">
                                        <span>Bobot: <strong>{{ $soal->bobot }}</strong></span>
                                        <span>•</span>
                                        <span class="@if($soal->is_active) text-success @else text-warning @endif">
                                            {{ $soal->is_active ? 'Aktif' : 'Nonaktif' }}
                                        </span>
                                    </div>
                                    <div class="flex gap-2">
                                        <a href="{{ route('guru.soal.edit', $soal) }}" class="btn btn-ghost btn-xs">Edit</a>
                                        <a href="{{ route('guru.soal.duplicate', $soal) }}" class="btn btn-ghost btn-xs">Duplikat</a>
                                        <form action="{{ route('guru.soal.destroy', $soal) }}" method="POST" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-ghost btn-xs text-error" onclick="return confirm('Apakah Anda yakin?')">Hapus</button>
                                        </form>
                                    </div>
                                </div>
                            @endforeach

                            <!-- Soal dari guru lain (read-only) -->
                            @php
                                $soalLain = $kategoriUjian->soals->where('guru_id', '!=', auth()->id());
                            @endphp

                            @if ($soalLain->count() > 0)
                                <div class="divider mt-4">Soal dari Guru Lain</div>
                                @foreach ($soalLain as $soal)
                                    <div class="border border-base-300 border-opacity-50 rounded-lg p-4 bg-base-50">
                                        <div class="flex items-start justify-between mb-2">
                                            <p class="font-semibold flex-1 text-base-content/70">{{ Str::limit($soal->pertanyaan, 150) }}</p>
                                            <span class="badge badge-sm badge-outline">{{ ucfirst(str_replace('_', ' ', $soal->tipe_soal)) }}</span>
                                        </div>
                                        <div class="flex gap-2 text-xs text-base-content/60">
                                            <span>Bobot: {{ $soal->bobot }}</span>
                                            <span>•</span>
                                            <span>Oleh: {{ $soal->guru->nama }}</span>
                                        </div>
                                    </div>
                                @endforeach
                            @endif
                        </div>
                    @else
                        <div class="alert alert-info">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="stroke-current shrink-0 w-6 h-6"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            <span>Belum ada soal untuk kategori ini. <a href="{{ route('guru.soal.create', ['kategori_ujian_id' => $kategoriUjian->id]) }}" class="link link-primary font-semibold">Buat soal baru</a></span>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
