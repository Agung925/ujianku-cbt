<x-app-layout>
    <x-slot name="header">
        <h1 class="text-2xl font-bold text-base-content">Kategori Ujian</h1>
    </x-slot>

    <div class="card bg-base-100 shadow">
        <div class="card-body">
            <h2 class="card-title mb-4">Daftar Kategori Ujian yang Tersedia</h2>

            @if ($kategoriUjians->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach ($kategoriUjians as $kategori)
                        <div class="card border border-base-300 bg-base-50 hover:shadow-lg transition cursor-pointer" onclick="window.location.href='{{ route('guru.kategori-ujian.show', $kategori) }}'">
                            <div class="card-body">
                                <h3 class="card-title text-lg">{{ $kategori->nama }}</h3>
                                @if ($kategori->deskripsi)
                                    <p class="text-sm text-base-content/70">{{ Str::limit($kategori->deskripsi, 100) }}</p>
                                @endif
                                <div class="divider my-2"></div>
                                <div class="flex items-center justify-between text-sm">
                                    <span class="text-base-content/60">
                                        <strong>{{ $kategori->soals->count() }}</strong> soal
                                    </span>
                                    <span class="link link-primary">Lihat Detail →</span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="alert alert-info">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="stroke-current shrink-0 w-6 h-6"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <span>Belum ada kategori ujian yang tersedia.</span>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
