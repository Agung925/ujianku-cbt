<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-bold text-base-content">Data Sekolah</h1>
            <a href="#" class="btn btn-primary btn-sm disabled">+ Tambah Sekolah</a>
        </div>
    </x-slot>

    <div class="card bg-base-100 shadow">
        <div class="card-body">
            <h2 class="card-title">Manajemen Sekolah Terdaftar</h2>
            
            <div class="alert alert-info my-4">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="stroke-current shrink-0 w-6 h-6"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <span>Fitur manajemen sekolah akan tersedia di Phase 4. Saat ini Anda dapat melihat ringkasan data dari semua sekolah di Dashboard.</span>
            </div>

            <p class="text-base-content/70 mb-4">Di sini Anda dapat:</p>
            <ul class="list-disc list-inside space-y-2 text-base-content/70">
                <li>Melihat daftar semua sekolah yang terdaftar di platform</li>
                <li>Mengelola profil dan konfigurasi masing-masing sekolah</li>
                <li>Melihat statistik pengguna (admin, guru, siswa) per sekolah</li>
                <li>Mengelola admin untuk setiap sekolah</li>
                <li>Melihat aktivitas dan laporan per sekolah</li>
            </ul>
        </div>
    </div>
</x-app-layout>
