<x-app-layout>
    <x-slot name="header">
        <h1 class="text-2xl font-bold text-base-content">Dashboard Siswa</h1>
    </x-slot>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <x-stats-card title="Ujian Tersedia" value="-" desc="Bisa dikerjakan" color="primary" />
        <x-stats-card title="Ujian Selesai" value="-" desc="Total dikerjakan" color="success" />
        <x-stats-card title="Rata-rata Nilai" value="-" desc="Semua ujian" color="info" />
    </div>

    <div class="card bg-base-100 shadow">
        <div class="card-body">
            <h2 class="card-title">Selamat Datang!</h2>
            <p class="text-base-content/70">Lihat jadwal ujian dan kerjakan soal dari sini.</p>
        </div>
    </div>
</x-app-layout>
