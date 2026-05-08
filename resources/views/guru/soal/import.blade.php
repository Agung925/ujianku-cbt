<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-bold text-base-content">Import Soal dari Excel</h1>
            <a href="{{ route('guru.soal.index') }}" class="btn btn-ghost btn-sm">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
                Kembali
            </a>
        </div>
    </x-slot>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
        <!-- Main Content -->
        <div class="lg:col-span-2">
            <!-- Card: Form Import -->
            <div class="card bg-base-100 shadow mb-4">
                <div class="card-body">
                    <h2 class="card-title text-lg mb-4">Upload File Excel</h2>

                    @if ($errors->any())
                        <div class="alert alert-error shadow-lg mb-4">
                            <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4v.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                            <div>
                                <h3 class="font-bold">Error validasi:</h3>
                                <ul class="list-disc list-inside">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    @endif

                    <form action="{{ route('guru.soal.import') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                        @csrf

                        <!-- Kategori Ujian -->
                        <div class="form-control">
                            <label class="label">
                                <span class="label-text font-semibold">Pilih Kategori Ujian <span class="text-error">*</span></span>
                            </label>
                            <select name="kategori_ujian_id" class="select select-bordered @error('kategori_ujian_id') select-error @enderror" required>
                                <option value="">-- Pilih Kategori --</option>
                                @foreach ($kategoriUjian as $kategori)
                                    <option value="{{ $kategori->id }}" @selected(old('kategori_ujian_id') == $kategori->id)>
                                        {{ $kategori->nama }}
                                    </option>
                                @endforeach
                            </select>
                            @error('kategori_ujian_id')
                                <label class="label">
                                    <span class="label-text-alt text-error">{{ $message }}</span>
                                </label>
                            @enderror
                        </div>

                        <!-- File Upload -->
                        <div class="form-control">
                            <label class="label">
                                <span class="label-text font-semibold">Pilih File Excel <span class="text-error">*</span></span>
                            </label>
                            <input 
                                type="file" 
                                name="file" 
                                class="file-input file-input-bordered @error('file') file-input-error @enderror"
                                accept=".xlsx,.xls,.csv"
                                required
                            />
                            <label class="label">
                                <span class="label-text-alt">Format: .xlsx, .xls, .csv (Max: 5MB)</span>
                            </label>
                            @error('file')
                                <label class="label">
                                    <span class="label-text-alt text-error">{{ $message }}</span>
                                </label>
                            @enderror
                        </div>

                        <!-- Submit -->
                        <div class="flex gap-2">
                            <button type="submit" class="btn btn-primary flex-1">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                                Upload & Import
                            </button>
                            <a href="{{ route('guru.soal.index') }}" class="btn btn-ghost">Batal</a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Card: Panduan -->
            <div class="card bg-base-100 shadow">
                <div class="card-body">
                    <h2 class="card-title text-lg mb-3">📋 Panduan Import</h2>

                    <div class="space-y-3 text-sm">
                        <div>
                            <p class="font-semibold text-base mb-1">Kolom yang Diperlukan:</p>
                            <ul class="list-disc list-inside space-y-1 text-gray-700">
                                <li><strong>Pertanyaan</strong> - Soal atau pertanyaan (wajib)</li>
                                <li><strong>Tipe</strong> - PG atau ESSAY (wajib)</li>
                                <li><strong>Opsi A, B, C, D</strong> - Wajib untuk soal PG</li>
                                <li><strong>Kunci Jawaban</strong> - Jawaban benar (wajib)</li>
                                <li><strong>Bobot</strong> - Poin soal (default: 1)</li>
                            </ul>
                        </div>

                        <div>
                            <p class="font-semibold text-base mb-1">Format Tipe Soal:</p>
                            <ul class="list-disc list-inside space-y-1 text-gray-700">
                                <li><strong>PG</strong> - Pilihan Ganda (isikan A, B, C, D sebagai kunci)</li>
                                <li><strong>ESSAY</strong> - Essay (isikan jawaban rujukan di kunci jawaban)</li>
                            </ul>
                        </div>

                        <div>
                            <p class="font-semibold text-base mb-1">Catatan Penting:</p>
                            <ul class="list-disc list-inside space-y-1 text-gray-700">
                                <li>Header harus di baris pertama</li>
                                <li>Data mulai dari baris ke-2</li>
                                <li>Soal duplikat akan ditolak</li>
                                <li>Baris dengan error tidak akan diimport</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar: Download Template -->
        <div>
            <div class="card bg-base-100 shadow sticky top-4">
                <div class="card-body">
                    <h3 class="card-title text-base mb-4">📥 Download Template</h3>
                    
                    <p class="text-sm text-gray-600 mb-4">
                        Gunakan template Excel ini sebagai acuan format import soal Anda.
                    </p>

                    <div class="flex gap-2">
                        <a href="{{ route('guru.soal.template') }}" class="btn btn-outline btn-sm flex-1">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" /></svg>
                            Template Excel
                        </a>
                    </div>

                    <div class="divider my-3">atau</div>

                    <a href="{{ route('guru.soal.index') }}" class="btn btn-sm btn-ghost w-full justify-start">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
                        Kembali ke Bank Soal
                    </a>
                </div>
            </div>

            <!-- Card: Tips -->
            <div class="card bg-info bg-opacity-20 shadow mt-4">
                <div class="card-body py-3">
                    <h4 class="font-semibold text-sm mb-2">💡 Tips</h4>
                    <p class="text-xs text-gray-700">
                        Pastikan semua data terisi dengan benar sebelum upload untuk menghindari banyak error pada proses import.
                    </p>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
