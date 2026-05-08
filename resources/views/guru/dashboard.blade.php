<x-app-layout>
    <x-slot name="header">
        <h1 class="text-2xl font-bold text-base-content">Dashboard Guru</h1>
    </x-slot>

    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="card bg-primary text-primary-content">
            <div class="card-body">
                <p class="text-sm opacity-75">Total Soal</p>
                <p class="text-3xl font-bold">{{ $stats['total_soal'] ?? 0 }}</p>
            </div>
        </div>
        <div class="card bg-secondary text-secondary-content">
            <div class="card-body">
                <p class="text-sm opacity-75">Total Ujian</p>
                <p class="text-3xl font-bold">{{ $stats['total_exam'] ?? 0 }}</p>
            </div>
        </div>
        <div class="card bg-accent text-accent-content">
            <div class="card-body">
                <p class="text-sm opacity-75">Total Siswa</p>
                <p class="text-3xl font-bold">{{ $stats['total_siswa'] ?? 0 }}</p>
            </div>
        </div>
        <div class="card bg-info text-info-content">
            <div class="card-body">
                <p class="text-sm opacity-75">Rata-rata Nilai</p>
                <p class="text-3xl font-bold">{{ $stats['average_student_score'] ?? 0 }}%</p>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
        <div class="card bg-base-100 shadow">
            <div class="card-body">
                <h2 class="card-title">Ujian Mendatang</h2>
                <div class="mt-4">
                    @if($stats['upcoming_exams'] && count($stats['upcoming_exams']) > 0)
                        <div class="space-y-2">
                            @foreach(array_slice($stats['upcoming_exams'], 0, 3) as $exam)
                            <div class="p-2 bg-base-200 rounded">
                                <p class="text-sm font-semibold">{{ Str::limit($exam['nama_ujian'] ?? '', 25) }}</p>
                                <p class="text-xs text-base-content/60">{{ $exam['tanggal_mulai']->format('d M Y H:i') ?? '' }}</p>
                            </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-center text-base-content/50 py-4">Tidak ada ujian mendatang</p>
                    @endif
                </div>
            </div>
        </div>

        <div class="card bg-base-100 shadow">
            <div class="card-body">
                <h2 class="card-title">Tingkat Penyelesaian</h2>
                <div class="mt-4">
                    <p class="text-4xl font-bold text-success">{{ $stats['completion_rate'] ?? 0 }}%</p>
                    <p class="text-sm text-base-content/70 mt-2">Ujian yang sudah selesai</p>
                    <div class="mt-4 w-full bg-base-300 rounded-full h-3">
                        <div class="bg-success h-3 rounded-full" style="width: {{ $stats['completion_rate'] ?? 0 }}%"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <a href="{{ route('guru.soal.index') }}" class="btn btn-outline btn-primary">Kelola Soal</a>
        <a href="{{ route('guru.ujian.index') }}" class="btn btn-outline btn-secondary">Kelola Ujian</a>
        <a href="{{ route('guru.nilai.index') }}" class="btn btn-outline btn-accent">Nilai Siswa</a>
    </div>

    <div class="card bg-base-100 shadow">
        <div class="card-body">
            <h2 class="card-title">Ujian Selesai Terbaru</h2>
            <div class="mt-4">
                @if($stats['past_exams'] && count($stats['past_exams']) > 0)
                    <div class="overflow-x-auto">
                        <table class="table table-compact w-full">
                            <thead>
                                <tr>
                                    <th>Ujian</th>
                                    <th>Tanggal Selesai</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach(array_slice($stats['past_exams'], 0, 5) as $exam)
                                <tr>
                                    <td class="text-sm">{{ Str::limit($exam['nama_ujian'] ?? '', 30) }}</td>
                                    <td class="text-sm">{{ $exam['tanggal_selesai']->format('d M Y') ?? '' }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-center text-base-content/50 py-4">Tidak ada ujian selesai</p>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
