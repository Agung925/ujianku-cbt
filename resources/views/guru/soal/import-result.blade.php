<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-bold text-base-content">Hasil Import Soal</h1>
            <a href="{{ route('guru.soal.index') }}" class="btn btn-primary btn-sm">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
                Kembali ke Bank Soal
            </a>
        </div>
    </x-slot>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
        <!-- Main Content -->
        <div class="lg:col-span-2">
            <!-- Summary Card -->
            <div class="card bg-base-100 shadow mb-4">
                <div class="card-body">
                    <h2 class="card-title text-lg mb-4">📊 Ringkasan Import</h2>

                    <div class="grid grid-cols-3 gap-3 mb-4">
                        <!-- Total Processed -->
                        <div class="stat bg-gray-50 rounded-lg">
                            <div class="stat-title">Total Diproses</div>
                            <div class="stat-value text-primary">{{ $totalProcessed }}</div>
                        </div>

                        <!-- Success -->
                        <div class="stat bg-green-50 rounded-lg">
                            <div class="stat-title">Berhasil</div>
                            <div class="stat-value text-success">{{ $successCount }}</div>
                            <div class="stat-desc">{{ number_format(($successCount / max($totalProcessed, 1)) * 100, 1) }}%</div>
                        </div>

                        <!-- Error -->
                        <div class="stat {{ $errorCount > 0 ? 'bg-red-50' : 'bg-gray-50' }} rounded-lg">
                            <div class="stat-title">Gagal</div>
                            <div class="stat-value {{ $errorCount > 0 ? 'text-error' : 'text-success' }}">{{ $errorCount }}</div>
                            <div class="stat-desc">{{ number_format(($errorCount / max($totalProcessed, 1)) * 100, 1) }}%</div>
                        </div>
                    </div>

                    <!-- Status Alert -->
                    @if ($errorCount === 0 && $successCount > 0)
                        <div class="alert alert-success shadow-lg">
                            <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m7 0a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                            <span><strong>Berhasil!</strong> Semua soal telah diimport ke dalam kategori <strong>{{ $kategoriUjian->nama }}</strong></span>
                        </div>
                    @elseif ($successCount > 0 && $errorCount > 0)
                        <div class="alert alert-warning shadow-lg">
                            <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4v.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                            <div>
                                <span><strong>Sebagian Berhasil</strong> - {{ $successCount }} soal berhasil, {{ $errorCount }} soal gagal</span>
                            </div>
                        </div>
                    @else
                        <div class="alert alert-error shadow-lg">
                            <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                            <span><strong>Semua Gagal!</strong> Tidak ada soal yang berhasil diimport. Lihat error detail di bawah.</span>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Error Details (if any) -->
            @if ($errorCount > 0)
                <div class="card bg-base-100 shadow">
                    <div class="card-body">
                        <h3 class="card-title text-lg mb-4">⚠️ Detail Error</h3>

                        <div class="overflow-x-auto">
                            <div class="space-y-2 max-h-96 overflow-y-auto">
                                @foreach ($errors as $error)
                                    <div class="flex gap-3 p-3 bg-red-50 rounded-lg border border-red-200">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-error shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4v.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                        <p class="text-sm text-gray-700">{{ $error }}</p>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <div class="mt-4 p-3 bg-blue-50 rounded-lg text-sm text-gray-700">
                            <p class="font-semibold mb-1">Saran:</p>
                            <ul class="list-disc list-inside space-y-1">
                                <li>Periksa format data sesuai dengan panduan template</li>
                                <li>Pastikan tidak ada data yang duplikat</li>
                                <li>Validasi tipe soal (PG atau ESSAY)</li>
                                <li>Coba upload kembali setelah memperbaiki error</li>
                            </ul>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <!-- Sidebar: Actions -->
        <div>
            <div class="card bg-base-100 shadow sticky top-4">
                <div class="card-body">
                    <h3 class="card-title text-base mb-4">📌 Tindakan Lanjutan</h3>

                    <div class="space-y-2">
                        <a href="{{ route('guru.soal.index', ['kategori_ujian_id' => $kategoriUjian->id]) }}" class="btn btn-primary btn-sm w-full">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" /></svg>
                            Lihat Soal Terbaru
                        </a>

                        <a href="{{ route('guru.soal.import.form') }}" class="btn btn-outline btn-sm w-full">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                            Import Lagi
                        </a>

                        <a href="{{ route('guru.soal.create', ['kategori_ujian_id' => $kategoriUjian->id]) }}" class="btn btn-outline btn-sm w-full">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                            Buat Soal Manual
                        </a>
                    </div>

                    <div class="divider my-3"></div>

                    <!-- Category Info -->
                    <div class="text-sm">
                        <p class="text-gray-600">Kategori Target:</p>
                        <p class="font-semibold text-base">{{ $kategoriUjian->nama }}</p>
                        @if ($kategoriUjian->deskripsi)
                            <p class="text-xs text-gray-500 mt-2">{{ $kategoriUjian->deskripsi }}</p>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Tips Card -->
            <div class="card bg-info bg-opacity-20 shadow mt-4">
                <div class="card-body py-3">
                    <h4 class="font-semibold text-sm mb-2">💡 Tips Selanjutnya</h4>
                    <ul class="text-xs text-gray-700 space-y-1">
                        <li>✓ Review soal yang diimport</li>
                        <li>✓ Tambahkan soal lain jika diperlukan</li>
                        <li>✓ Buat ujian menggunakan soal ini</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
