<x-app-layout>
    <x-slot name="header">
        <h1 class="text-2xl font-bold text-base-content">Dashboard Admin</h1>
    </x-slot>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <x-stats-card title="Total Guru" value="-" desc="Guru aktif" color="primary" />
        <x-stats-card title="Total Siswa" value="-" desc="Siswa aktif" color="secondary" />
        <x-stats-card title="Ujian Aktif" value="-" desc="Sedang berjalan" color="success" />
        <x-stats-card title="Ujian Selesai" value="-" desc="Bulan ini" color="info" />
    </div>

    <div class="card bg-base-100 shadow">
        <div class="card-body">
            <h2 class="card-title">Panel Admin Sekolah</h2>
            <p class="text-base-content/70">Kelola data guru, siswa, dan laporan ujian sekolah Anda.</p>
        </div>
    </div>
</x-app-layout>
