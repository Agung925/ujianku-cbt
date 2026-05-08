<x-app-layout>
    <x-slot name="header">
        <h1 class="text-2xl font-bold text-base-content">Dashboard Admin</h1>
    </x-slot>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="card bg-primary text-primary-content">
            <div class="card-body">
                <p class="text-sm opacity-75">Total Guru</p>
                <p class="text-3xl font-bold">{{ $stats['total_guru'] ?? 0 }}</p>
            </div>
        </div>
        <div class="card bg-secondary text-secondary-content">
            <div class="card-body">
                <p class="text-sm opacity-75">Total Siswa</p>
                <p class="text-3xl font-bold">{{ $stats['total_siswa'] ?? 0 }}</p>
            </div>
        </div>
        <div class="card bg-accent text-accent-content">
            <div class="card-body">
                <p class="text-sm opacity-75">Total Ujian</p>
                <p class="text-3xl font-bold">{{ $stats['total_exam'] ?? 0 }}</p>
            </div>
        </div>
        <div class="card bg-info text-info-content">
            <div class="card-body">
                <p class="text-sm opacity-75">Rata-rata Nilai</p>
                <p class="text-3xl font-bold">{{ $stats['average_score'] ?? 0 }}%</p>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
        <!-- Pass Rate Card -->
        <div class="card bg-base-100 shadow">
            <div class="card-body">
                <h2 class="card-title">Tingkat Kelulusan</h2>
                <div class="mt-4">
                    <p class="text-4xl font-bold text-success">{{ $stats['pass_rate'] ?? 0 }}%</p>
                    <p class="text-sm text-base-content/70 mt-2">Dari semua ujian yang telah diikuti</p>
                    <div class="mt-4 w-full bg-base-300 rounded-full h-3">
                        <div class="bg-success h-3 rounded-full" style="width: {{ $stats['pass_rate'] ?? 0 }}%"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Upcoming Exams -->
        <div class="card bg-base-100 shadow">
            <div class="card-body">
                <h2 class="card-title">Ujian Mendatang (7 Hari)</h2>
                <div class="mt-4">
                    @if($stats['upcoming_exams'] && count($stats['upcoming_exams']) > 0)
                        <div class="space-y-2">
                            @foreach(array_slice($stats['upcoming_exams'], 0, 3) as $exam)
                            <div class="flex justify-between items-center p-2 bg-base-200 rounded">
                                <span class="text-sm">{{ Str::limit($exam['name'], 25) }}</span>
                                <span class="text-xs">{{ $exam['date'] }}</span>
                            </div>
                            @endforeach
                            @if(count($stats['upcoming_exams']) > 3)
                                <p class="text-xs text-base-content/50 text-center mt-2">+{{ count($stats['upcoming_exams']) - 3 }} ujian lagi</p>
                            @endif
                        </div>
                    @else
                        <p class="text-center text-base-content/50 py-4">Tidak ada ujian mendatang</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Quick Links -->
        <div class="card bg-base-100 shadow">
            <div class="card-body">
                <h2 class="card-title">Akses Cepat</h2>
                <div class="grid grid-cols-2 gap-3 mt-4">
                    <a href="{{ route('admin.guru.index') }}" class="btn btn-primary btn-sm">
                        Kelola Guru
                    </a>
                    <a href="{{ route('admin.siswa.index') }}" class="btn btn-secondary btn-sm">
                        Kelola Siswa
                    </a>
                    <a href="{{ route('admin.dashboard.statistics') }}" class="btn btn-accent btn-sm">
                        Statistik
                    </a>
                    <a href="{{ route('admin.guru.create') }}" class="btn btn-outline btn-sm">
                        + Guru
                    </a>
                </div>
            </div>
        </div>

        <!-- Quick Info -->
        <div class="card bg-base-100 shadow">
            <div class="card-body">
                <h2 class="card-title">Info Panel</h2>
                <div class="mt-4 space-y-2 text-sm">
                    <div class="flex justify-between">
                        <span>User:</span>
                        <span class="font-semibold">{{ auth()->user()?->name ?? 'System' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Total Soal:</span>
                        <span class="font-semibold">{{ $stats['total_questions'] ?? 0 }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Waktu:</span>
                        <span class="font-semibold">{{ now()->format('d M Y') }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- News Feed -->
    <div class="mt-8">
        <x-news-feed :news="$news ?? []" />
    </div>
</x-app-layout>
