<x-app-layout>
    <x-slot name="header">
        <h1 class="text-2xl font-bold text-base-content">Dashboard Siswa</h1>
    </x-slot>

    <!-- Active Exam Alert -->
    @if($stats['active_exam'])
    <div class="alert alert-warning mb-6 shadow">
        <div class="flex justify-between items-center">
            <div>
                <h3 class="font-bold">⏰ Ujian Sedang Berlangsung</h3>
                <p class="text-sm">{{ $stats['active_exam']['name'] }}</p>
                <p class="text-sm mt-1">Berakhir: {{ $stats['active_exam']['end_time'] }}</p>
            </div>
            <a href="{{ route('siswa.exam.take', $stats['active_exam']['id']) }}" class="btn btn-warning btn-sm">
                Lanjutkan Ujian
            </a>
        </div>
    </div>
    @endif

    <!-- Stats Row -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <div class="card bg-primary text-primary-content">
            <div class="card-body">
                <p class="text-sm opacity-75">Ujian Diikuti</p>
                <p class="text-3xl font-bold">{{ $stats['total_exams_taken'] ?? 0 }}</p>
            </div>
        </div>
        <div class="card bg-success text-success-content">
            <div class="card-body">
                <p class="text-sm opacity-75">Rata-rata Nilai</p>
                <p class="text-3xl font-bold">{{ $stats['average_score'] ?? 0 }}%</p>
            </div>
        </div>
        <div class="card bg-info text-info-content">
            <div class="card-body">
                <p class="text-sm opacity-75">Nilai Terakhir</p>
                <p class="text-3xl font-bold">{{ $stats['last_exam_score'] ?? '-' }}%</p>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Ujian Mendatang -->
        <div class="card bg-base-100 shadow">
            <div class="card-body">
                <h2 class="card-title">Ujian Mendatang (7 Hari)</h2>
                <div class="mt-4">
                    @if($stats['upcoming_exams'] && count($stats['upcoming_exams']) > 0)
                        <div class="space-y-2">
                            @foreach(array_slice($stats['upcoming_exams'], 0, 3) as $exam)
                            <div class="p-2 bg-base-200 rounded">
                                <p class="text-sm font-semibold">{{ $exam['name'] }}</p>
                                <p class="text-xs text-base-content/60">📅 {{ $exam['date'] }}</p>
                            </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-center text-base-content/50 py-4">Tidak ada ujian mendatang</p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Riwayat Ujian -->
        <div class="card bg-base-100 shadow">
            <div class="card-body">
                <h2 class="card-title">Riwayat Ujian (Terbaru)</h2>
                <div class="mt-4">
                    @if($stats['exam_history'] && count($stats['exam_history']) > 0)
                        <div class="space-y-2 max-h-64 overflow-y-auto">
                            @foreach(array_slice($stats['exam_history'], 0, 5) as $exam)
                            <div class="flex justify-between items-center p-2 bg-base-200 rounded">
                                <div>
                                    <p class="text-sm font-semibold">{{ Str::limit($exam['exam'], 20) }}</p>
                                    <p class="text-xs text-base-content/60">{{ $exam['date'] }}</p>
                                </div>
                                <div class="text-right">
                                    <p class="text-sm font-bold @if($exam['status'] === 'lulus') text-success @else text-error @endif">
                                        {{ $exam['score'] }}%
                                    </p>
                                    <p class="text-xs @if($exam['status'] === 'lulus') text-success @else text-error @endif">
                                        {{ ucfirst($exam['status']) }}
                                    </p>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-center text-base-content/50 py-4">Belum ada riwayat ujian</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
