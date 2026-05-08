<x-app-layout>
    <x-slot name="header">
        <h1 class="text-2xl font-bold text-base-content">Dashboard Super Admin</h1>
    </x-slot>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <x-stats-card title="Total Sekolah" value="-" desc="Sekolah terdaftar" color="primary" />
        <x-stats-card title="Total Guru" value="-" desc="Semua sekolah" color="secondary" />
        <x-stats-card title="Total Siswa" value="-" desc="Semua sekolah" color="accent" />
        <x-stats-card title="Total Ujian" value="-" desc="Hari ini" color="info" />
    </div>

    <div class="card bg-base-100 shadow">
        <div class="card-body">
            <h2 class="card-title">Selamat Datang di Super Admin Panel</h2>
            <p class="text-base-content/70">Kelola semua sekolah dan data platform dari sini.</p>
        </div>
    </div>
</x-app-layout>
