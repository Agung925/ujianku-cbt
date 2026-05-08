<x-app-layout>
    <x-slot name="header">
        <h1 class="text-2xl font-bold text-base-content">Dashboard Guru</h1>
    </x-slot>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <x-stats-card title="Total Soal" value="-" desc="Bank soal saya" color="primary" />
        <x-stats-card title="Ujian Aktif" value="-" desc="Sedang berlangsung" color="warning" />
        <x-stats-card title="Siswa Ikut Ujian" value="-" desc="Hari ini" color="success" />
    </div>

    <div class="card bg-base-100 shadow">
        <div class="card-body">
            <h2 class="card-title">Panel Guru</h2>
            <p class="text-base-content/70">Buat soal, jadwalkan ujian, dan pantau hasil siswa Anda.</p>
        </div>
    </div>
</x-app-layout>
