<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-bold text-base-content">Bank Soal</h1>
            <a href="{{ route('guru.soal.create') }}" class="btn btn-primary btn-sm">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                + Buat Soal
            </a>
        </div>
    </x-slot>

    @if ($soals->count() > 0)
        <!-- Filter dan Search -->
        <div class="card bg-base-100 shadow mb-4">
            <div class="card-body">
                <form method="GET" action="{{ route('guru.soal.index') }}" class="flex gap-3 flex-wrap">
                    <!-- Filter Kategori -->
                    <select name="kategori_ujian_id" class="select select-bordered select-sm flex-1 min-w-[150px]" onchange="this.form.submit()">
                        <option value="">Semua Kategori</option>
                        @foreach ($kategoriUjians as $kategori)
                            <option value="{{ $kategori->id }}" @selected(request('kategori_ujian_id') == $kategori->id)>
                                {{ $kategori->nama }}
                            </option>
                        @endforeach
                    </select>

                    <!-- Search Soal -->
                    <input 
                        type="text" 
                        name="search" 
                        placeholder="Cari pertanyaan..." 
                        value="{{ request('search') }}"
                        class="input input-bordered input-sm flex-1 min-w-[200px]"
                    />
                    <button type="submit" class="btn btn-sm btn-outline">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                    </button>
                </form>
            </div>
        </div>

        <!-- Flash Messages -->
        @if ($message = Session::get('success'))
            <div class="alert alert-success shadow-lg mb-4">
                <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                <span>{{ $message }}</span>
            </div>
        @endif

        <!-- Table Soal -->
        <div class="card bg-base-100 shadow overflow-x-auto">
            <table class="table table-compact w-full">
                <thead>
                    <tr class="bg-base-200">
                        <th class="w-12">#</th>
                        <th>Pertanyaan</th>
                        <th class="w-32">Kategori</th>
                        <th class="w-24">Tipe</th>
                        <th class="w-16">Bobot</th>
                        <th class="w-16">Status</th>
                        <th class="w-32">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($soals as $index => $soal)
                        <tr class="hover">
                            <td>{{ ($soals->currentPage() - 1) * 20 + $index + 1 }}</td>
                            <td title="{{ $soal->pertanyaan }}">
                                {{ Str::limit($soal->pertanyaan, 60) }}
                            </td>
                            <td>
                                <span class="text-sm">{{ $soal->kategoriUjian?->nama ?? '-' }}</span>
                            </td>
                            <td>
                                <span class="badge @if($soal->tipe_soal === 'pilihan_ganda') badge-primary @else badge-secondary @endif text-xs">
                                    {{ $soal->tipe_soal === 'pilihan_ganda' ? 'PG' : 'Essay' }}
                                </span>
                            </td>
                            <td class="text-center">{{ $soal->bobot }}</td>
                            <td>
                                <span class="badge @if($soal->is_active) badge-success @else badge-ghost @endif text-xs">
                                    {{ $soal->is_active ? 'Aktif' : 'Nonaktif' }}
                                </span>
                            </td>
                            <td>
                                <div class="flex gap-1">
                                    <a href="{{ route('guru.soal.edit', $soal) }}" class="btn btn-ghost btn-xs tooltip" data-tip="Edit">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                                    </a>
                                    <form action="{{ route('guru.soal.duplicate', $soal) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="btn btn-ghost btn-xs tooltip" data-tip="Duplikat">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" /></svg>
                                        </button>
                                    </form>
                                    <form action="{{ route('guru.soal.destroy', $soal) }}" method="POST" class="inline" onsubmit="return confirm('Hapus soal ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-ghost btn-xs text-error tooltip" data-tip="Hapus">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-8 text-base-content/50">
                                Tidak ada soal. <a href="{{ route('guru.soal.create') }}" class="link link-primary">Buat soal baru</a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="mt-4">
            {{ $soals->links() }}
        </div>
    @else
        <div class="alert alert-info shadow-lg">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="stroke-current shrink-0 w-6 h-6"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            <div>
                <h3 class="font-bold">Belum Ada Soal</h3>
                <div class="text-sm">Anda belum membuat soal. <a href="{{ route('guru.soal.create') }}" class="link link-primary font-bold">Buat soal pertama Anda sekarang</a></div>
            </div>
        </div>
    @endif
</x-app-layout>
