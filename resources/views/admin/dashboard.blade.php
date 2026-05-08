<x-app-layout>
    <x-slot name="header">
        <h1 class="text-2xl font-bold text-base-content">Dashboard Admin</h1>
    </x-slot>

    @php
        $totalGuru   = \App\Models\Guru::where('is_active', true)->count();
        $totalSiswa  = \App\Models\Siswa::where('is_active', true)->count();
    @endphp

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <x-stats-card title="Total Guru" :value="$totalGuru" desc="Guru aktif" color="primary" />
        <x-stats-card title="Total Siswa" :value="$totalSiswa" desc="Siswa aktif" color="secondary" />
        <x-stats-card title="Ujian Aktif" value="-" desc="Sedang berjalan" color="success" />
        <x-stats-card title="Ujian Selesai" value="-" desc="Bulan ini" color="info" />
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Quick Links -->
        <div class="card bg-base-100 shadow">
            <div class="card-body">
                <h2 class="card-title">Akses Cepat</h2>
                <div class="grid grid-cols-2 gap-3 mt-2">
                    <a href="{{ route('admin.guru.index') }}" class="btn btn-primary btn-sm">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        Kelola Guru
                    </a>
                    <a href="{{ route('admin.siswa.index') }}" class="btn btn-secondary btn-sm">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                        Kelola Siswa
                    </a>
                    <a href="{{ route('admin.guru.create') }}" class="btn btn-outline btn-sm">
                        + Tambah Guru
                    </a>
                    <a href="{{ route('admin.siswa.create') }}" class="btn btn-outline btn-sm">
                        + Tambah Siswa
                    </a>
                </div>
            </div>
        </div>

        <!-- Info Panel -->
        <div class="card bg-base-100 shadow">
            <div class="card-body">
                <h2 class="card-title">Panel Admin Sekolah</h2>
                <p class="text-base-content/70">Kelola data guru, siswa, dan laporan ujian sekolah Anda.</p>
                <div class="mt-3">
                    <div class="badge badge-primary badge-outline">{{ auth()->user()->name }}</div>
                    <div class="badge badge-ghost ml-1">Admin</div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
