<x-app-layout>
    <x-slot name="header">
        <h1 class="text-2xl font-bold text-base-content">Daftar Ujian</h1>
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

    @if ($availableExams->count() > 0)
        <div class="card bg-base-100 shadow">
            <div class="card-body">
                <h2 class="card-title mb-4">Ujian Tersedia</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach ($availableExams as $exam)
                        <div class="card border border-base-300 hover:shadow-lg transition-shadow">
                            <div class="card-body">
                                <h3 class="card-title text-base">{{ $exam->judul }}</h3>
                                <p class="text-sm text-base-content/60">{{ $exam->kategoriUjian?->nama ?? '-' }}</p>

                                @if ($exam->deskripsi)
                                    <p class="text-sm my-2">{{ Str::limit($exam->deskripsi, 80) }}</p>
                                @endif

                                <div class="space-y-1 text-xs text-base-content/60 my-3">
                                    <div>📅 Mulai: {{ $exam->tgl_mulai->format('d M Y, H:i') }}</div>
                                    <div>⏱️ Durasi: {{ $exam->waktu_durasi }} menit</div>
                                    <div>📊 Soal: {{ $exam->soal->count() }} butir</div>
                                </div>

                                <div class="divider my-2"></div>

                                @if ($examStatuses[$exam->id]['submitted'])
                                    <div class="alert alert-info alert-sm py-1">
                                        <span class="text-xs">✓ Anda sudah menyelesaikan ujian ini</span>
                                    </div>
                                    <a href="{{ route('siswa.ujian.results', $exam->id) }}" class="btn btn-sm btn-outline w-full mt-2">
                                        Lihat Hasil
                                    </a>
                                @elseif ($examStatuses[$exam->id]['started'])
                                    <div class="alert alert-warning alert-sm py-1">
                                        <span class="text-xs">⏳ Anda sedang mengerjakan ujian ini</span>
                                    </div>
                                    <a href="{{ route('siswa.ujian.start', $exam->id) }}" class="btn btn-sm btn-primary w-full mt-2">
                                        Lanjutkan Ujian
                                    </a>
                                @else
                                    <a href="{{ route('siswa.ujian.start', $exam->id) }}" class="btn btn-sm btn-primary w-full">
                                        Mulai Ujian
                                    </a>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @else
        <div class="card bg-base-100 shadow">
            <div class="card-body text-center py-16">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mx-auto text-base-content/30 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C6.5 6.253 2 10.998 2 17s4.5 10.747 10 10.747c5.5 0 10-4.998 10-10.747S17.5 6.253 12 6.253z" /></svg>
                <h3 class="text-lg font-semibold text-base-content/60 mb-2">Tidak ada ujian tersedia</h3>
                <p class="text-base-content/50">Ujian akan ditampilkan di sini sesuai jadwal yang ditentukan guru.</p>
            </div>
        </div>
    @endif
</x-app-layout>
