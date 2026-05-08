<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('guru.ujian.show', $ujian->id) }}" class="btn btn-ghost btn-sm">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" /></svg>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-base-content">Kelola Soal Ujian</h1>
                <p class="text-sm text-base-content/60">{{ $ujian->judul }}</p>
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

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Soal dalam Ujian -->
        <div class="card bg-base-100 shadow">
            <div class="card-body">
                <h2 class="card-title text-base">
                    Soal dalam Ujian
                    <span class="badge badge-neutral ml-1">{{ $ujian->soal->count() }}</span>
                </h2>

                @if ($ujian->soal->count() > 0)
                    <div class="space-y-2 mt-2">
                        @foreach ($ujian->soal as $soal)
                            <div class="flex items-start gap-3 p-3 rounded-lg bg-base-200/50 group">
                                <span class="badge badge-outline badge-sm mt-0.5 min-w-[2rem] justify-center shrink-0">
                                    {{ $soal->pivot->urutan }}
                                </span>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm">{{ Str::limit($soal->pertanyaan, 80) }}</p>
                                    <div class="flex gap-2 mt-1">
                                        <span class="text-xs text-base-content/50">{{ $soal->tipe_soal }}</span>
                                        <span class="text-xs text-base-content/50">Bobot: {{ $soal->bobot }}</span>
                                    </div>
                                </div>
                                <form action="{{ route('guru.ujian.questions.remove', [$ujian->id, $soal->id]) }}" method="POST"
                                      onsubmit="return confirm('Hapus soal ini dari ujian?')"
                                      class="shrink-0">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-ghost btn-xs text-error opacity-0 group-hover:opacity-100" title="Hapus dari ujian">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                                    </button>
                                </form>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8 text-base-content/50">
                        <p class="text-sm">Belum ada soal. Pilih soal dari daftar sebelah kanan.</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Soal Tersedia -->
        <div class="card bg-base-100 shadow">
            <div class="card-body">
                <h2 class="card-title text-base">
                    Soal Tersedia
                    <span class="badge badge-neutral ml-1">{{ $availableSoal->count() }}</span>
                </h2>

                @if ($availableSoal->count() > 0)
                    <form action="{{ route('guru.ujian.questions.assign', $ujian->id) }}" method="POST" class="space-y-3 mt-2">
                        @csrf

                        <div class="space-y-2 max-h-96 overflow-y-auto pr-1">
                            @foreach ($availableSoal as $soal)
                                <label class="flex items-start gap-3 p-3 rounded-lg bg-base-200/50 cursor-pointer hover:bg-base-200 transition-colors">
                                    <input
                                        type="checkbox"
                                        name="soal_ids[]"
                                        value="{{ $soal->id }}"
                                        class="checkbox checkbox-primary checkbox-sm mt-0.5 shrink-0"
                                    />
                                    <div class="min-w-0">
                                        <p class="text-sm">{{ Str::limit($soal->pertanyaan, 80) }}</p>
                                        <div class="flex gap-2 mt-1">
                                            <span class="text-xs text-base-content/50">{{ $soal->tipe_soal }}</span>
                                            <span class="text-xs text-base-content/50">Bobot: {{ $soal->bobot }}</span>
                                        </div>
                                    </div>
                                </label>
                            @endforeach
                        </div>

                        <div class="flex gap-2 pt-2">
                            <button type="submit" class="btn btn-primary btn-sm">
                                + Tambahkan ke Ujian
                            </button>
                            <button type="button" onclick="toggleAll()" class="btn btn-ghost btn-sm">
                                Pilih Semua
                            </button>
                        </div>
                    </form>
                @else
                    <div class="text-center py-8 text-base-content/50">
                        <p class="text-sm">Semua soal dari kategori ini sudah ditambahkan ke ujian.</p>
                        <a href="{{ route('guru.soal.create') }}" class="btn btn-sm btn-outline mt-3">
                            Buat Soal Baru
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <script>
        function toggleAll() {
            const checkboxes = document.querySelectorAll('input[name="soal_ids[]"]');
            const allChecked = Array.from(checkboxes).every(cb => cb.checked);
            checkboxes.forEach(cb => cb.checked = !allChecked);
        }
    </script>
</x-app-layout>
