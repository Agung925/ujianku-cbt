@extends('layouts.app')

@section('title', 'Penilaian Ujian')

@section('content')
<div class="container mx-auto p-6">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Penilaian Ujian</h1>
        <p class="text-gray-600 mt-2">Kelola dan tinjau penilaian ujian siswa</p>
    </div>

    <!-- Success/Error Messages -->
    @if ($message = Session::get('success'))
        <div class="alert alert-success shadow-lg mb-6">
            <div>
                <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span>{{ $message }}</span>
            </div>
        </div>
    @endif

    @if ($message = Session::get('error'))
        <div class="alert alert-error shadow-lg mb-6">
            <div>
                <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l-2-2m0 0l-2-2m2 2l2-2m-2 2l-2 2m8-8l2 2m0 0l2 2m-2-2l-2-2m2 2l2 2M9 12a9 9 0 1118 0 9 9 0 01-18 0z" />
                </svg>
                <span>{{ $message }}</span>
            </div>
        </div>
    @endif

    <!-- Exams Table -->
    <div class="card bg-white shadow-lg">
        <div class="card-body">
            @if ($ujians->count() > 0)
                <div class="overflow-x-auto">
                    <table class="table table-zebra w-full">
                        <thead>
                            <tr class="bg-primary text-white">
                                <th>Judul Ujian</th>
                                <th>Kategori</th>
                                <th>Tanggal Ujian</th>
                                <th>Total Siswa</th>
                                <th>Status Penilaian</th>
                                <th>Nilai Rata-rata</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($ujians as $ujian)
                                @php
                                    $summary = $summaries[$ujian->id] ?? [];
                                    $totalStudents = $summary['total_students'] ?? 0;
                                    $graded = $summary['graded'] ?? 0;
                                    $pendingEssay = $summary['pending_essay'] ?? 0;
                                    $avgScore = $summary['average_score'] ?? 0;
                                    $passRate = $summary['pass_rate'] ?? 0;
                                @endphp
                                <tr>
                                    <td class="font-semibold">{{ $ujian->judul }}</td>
                                    <td>
                                        <span class="badge badge-outline">
                                            {{ $ujian->kategoriUjian->nama_kategori ?? '-' }}
                                        </span>
                                    </td>
                                    <td class="text-sm">
                                        {{ \Carbon\Carbon::parse($ujian->tgl_selesai)->format('d M Y H:i') }}
                                    </td>
                                    <td class="text-center font-bold">{{ $totalStudents }}</td>
                                    <td>
                                        <div class="flex flex-col gap-2">
                                            @if ($pendingEssay > 0)
                                                <span class="badge badge-warning gap-2">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4v.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                    </svg>
                                                    {{ $pendingEssay }} Essay menunggu
                                                </span>
                                            @endif
                                            @if ($graded > 0)
                                                <span class="badge badge-success gap-2">
                                                    {{ $graded }} Dinilai
                                                </span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <span class="font-bold text-lg">{{ number_format($avgScore, 1) }}</span>
                                        <br/>
                                        <span class="text-xs text-gray-500">
                                            Lulus: {{ $passRate }}%
                                        </span>
                                    </td>
                                    <td>
                                        <div class="flex gap-2">
                                            <a href="{{ route('guru.nilai.grade-exam', $ujian->id) }}"
                                                class="btn btn-sm btn-primary gap-2">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" />
                                                </svg>
                                                Nilai
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="mt-6">
                    {{ $ujians->links() }}
                </div>
            @else
                <div class="text-center py-12">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mx-auto text-gray-400 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" />
                    </svg>
                    <p class="text-gray-500 text-lg font-semibold">Belum ada ujian untuk dinilai</p>
                    <p class="text-gray-400">Buat ujian terlebih dahulu untuk mulai penilaian</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
