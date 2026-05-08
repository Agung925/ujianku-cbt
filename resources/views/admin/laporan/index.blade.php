<x-app-layout>
    <x-slot name="header">
        <h1 class="text-2xl font-bold text-base-content">Laporan & Statistik</h1>
    </x-slot>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mb-6">
        <x-stats-card title="Total Ujian" value="-" desc="Seluruh waktu" color="primary" />
        <x-stats-card title="Rata-rata Nilai" value="-" desc="Semua ujian" color="secondary" />
        <x-stats-card title="Ujian Lalu" value="-" desc="Bulan ini" color="success" />
    </div>

    <div class="card bg-base-100 shadow">
        <div class="card-body">
            <h2 class="card-title">Laporan Analitik</h2>
            <p class="text-base-content/70">Fitur laporan dan statistik ujian akan ditampilkan di sini.</p>
            <div class="alert alert-info mt-4">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="stroke-current shrink-0 w-6 h-6"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <span>Statistik akan tersedia setelah ada data ujian yang cukup.</span>
            </div>
        </div>
    </div>
</x-app-layout>
