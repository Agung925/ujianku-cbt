@extends('layouts.app')

@section('title', 'Nilai Ujian - ' . $ujian->judul)

@section('content')
<div class="container mx-auto p-6">
    <!-- Header -->
    <div class="mb-8">
        <div class="breadcrumbs text-sm mb-4">
            <ul>
                <li><a href="{{ route('guru.nilai.index') }}">Penilaian</a></li>
                <li class="font-bold">{{ $ujian->judul }}</li>
            </ul>
        </div>
        <h1 class="text-3xl font-bold text-gray-900">{{ $ujian->judul }}</h1>
        <p class="text-gray-600 mt-2">Penilaian dan Review Jawaban Siswa</p>
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

    <!-- Grading Summary -->
    <div class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-6">
        <div class="card bg-white shadow-lg">
            <div class="card-body">
                <p class="text-gray-600 text-sm">Total Siswa</p>
                <p class="text-2xl font-bold text-primary">{{ $summary['total_students'] ?? 0 }}</p>
            </div>
        </div>
        <div class="card bg-white shadow-lg">
            <div class="card-body">
                <p class="text-gray-600 text-sm">Sudah Dinilai</p>
                <p class="text-2xl font-bold text-success">{{ $summary['graded'] ?? 0 }}</p>
            </div>
        </div>
        <div class="card bg-white shadow-lg">
            <div class="card-body">
                <p class="text-gray-600 text-sm">Menunggu Essay</p>
                <p class="text-2xl font-bold text-warning">{{ $summary['pending_essay'] ?? 0 }}</p>
            </div>
        </div>
        <div class="card bg-white shadow-lg">
            <div class="card-body">
                <p class="text-gray-600 text-sm">Rata-rata Nilai</p>
                <p class="text-2xl font-bold text-info">{{ number_format($summary['average_score'] ?? 0, 1) }}</p>
            </div>
        </div>
        <div class="card bg-white shadow-lg">
            <div class="card-body">
                <p class="text-gray-600 text-sm">Tingkat Kelulusan</p>
                <p class="text-2xl font-bold text-accent">{{ $summary['pass_rate'] ?? 0 }}%</p>
            </div>
        </div>
    </div>

    <!-- Publish Grades Button -->
    @if (($summary['pending_essay'] ?? 0) == 0)
        <form method="POST" action="{{ route('guru.nilai.publish', $ujian->id) }}" class="mb-6">
            @csrf
            <button type="submit" class="btn btn-success gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                </svg>
                Publikasikan Nilai
            </button>
        </form>
    @endif

    <!-- Student Grades Table -->
    <div class="card bg-white shadow-lg">
        <div class="card-body">
            <h2 class="card-title mb-4">Daftar Nilai Siswa</h2>

            @if (count($studentAnswers) > 0)
                <div class="overflow-x-auto">
                    <table class="table table-zebra w-full">
                        <thead>
                            <tr class="bg-primary text-white">
                                <th>Nama Siswa</th>
                                <th>Email</th>
                                <th>Nilai Otomatis</th>
                                <th>Nilai Essay</th>
                                <th>Nilai Akhir</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($studentAnswers as $siswaId => $data)
                                @php
                                    $siswa = $data['siswa'];
                                    $nilai = $data['nilai'];
                                @endphp
                                <tr>
                                    <td class="font-semibold">{{ $siswa->nama_siswa }}</td>
                                    <td>{{ $siswa->user->email ?? '-' }}</td>
                                    <td class="text-center">
                                        @if ($nilai->nilai_otomatis)
                                            <span class="badge badge-info">{{ number_format($nilai->nilai_otomatis, 1) }}</span>
                                        @else
                                            <span class="text-gray-400">-</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if ($nilai->nilai_essay)
                                            <span class="badge badge-success">{{ number_format($nilai->nilai_essay, 1) }}</span>
                                        @else
                                            <span class="text-gray-400">-</span>
                                        @endif
                                    </td>
                                    <td class="text-center font-bold text-lg">
                                        @if ($nilai->nilai_akhir)
                                            <span class="badge badge-lg {{ $nilai->status === 'lulus' ? 'badge-success' : 'badge-error' }}">
                                                {{ number_format($nilai->nilai_akhir, 1) }}
                                            </span>
                                        @else
                                            <span class="text-gray-400">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge {{ $nilai->status === 'lulus' ? 'badge-success' : ($nilai->status === 'tidak_lulus' ? 'badge-error' : 'badge-warning') }}">
                                            {{ ucfirst(str_replace('_', ' ', $nilai->status)) }}
                                        </span>
                                    </td>
                                    <td>
                                        <a href="{{ route('guru.nilai.grade-exam', $ujian->id) }}#siswa-{{ $siswaId }}"
                                            class="btn btn-sm btn-outline gap-1">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.5H3v-3.5L16.732 2.732z" />
                                            </svg>
                                            Lihat
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-12">
                    <p class="text-gray-500 text-lg">Belum ada siswa yang mengikuti ujian ini</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Student Answer Details (Collapsible) -->
    <div class="mt-6 space-y-4">
        @foreach ($studentAnswers as $siswaId => $data)
            <div class="card bg-base-100 shadow-lg" id="siswa-{{ $siswaId }}">
                <div class="card-body">
                    <h3 class="card-title text-lg">
                        {{ $data['siswa']->nama_siswa }}
                        <span class="badge {{ $data['nilai']->status === 'lulus' ? 'badge-success' : ($data['nilai']->status === 'tidak_lulus' ? 'badge-error' : 'badge-warning') }}">
                            {{ number_format($data['nilai']->nilai_akhir ?? 0, 1) }}
                        </span>
                    </h3>

                    <!-- Answer Details -->
                    <div class="overflow-x-auto">
                        <table class="table table-sm w-full">
                            <thead>
                                <tr>
                                    <th>No.</th>
                                    <th>Pertanyaan</th>
                                    <th>Tipe</th>
                                    <th>Jawaban Siswa</th>
                                    <th>Kunci Jawaban</th>
                                    <th>Hasil</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($data['answers'] as $index => $answer)
                                    @php
                                        $soal = $answer->soal;
                                        $isCorrect = $soal->tipe_soal === 'pg' && $answer->jawaban === $soal->kunci_jawaban;
                                    @endphp
                                    <tr>
                                        <td class="font-semibold">{{ $index + 1 }}</td>
                                        <td class="text-sm max-w-xs">
                                            {{ Str::limit($soal->teks_soal, 50) }}
                                        </td>
                                        <td><span class="badge badge-outline">{{ strtoupper($soal->tipe_soal) }}</span></td>
                                        <td>
                                            <span class="font-semibold">{{ $answer->jawaban ?? '-' }}</span>
                                        </td>
                                        <td>
                                            @if ($soal->tipe_soal === 'pg')
                                                <span class="font-semibold">{{ $soal->kunci_jawaban }}</span>
                                            @else
                                                <span class="text-gray-400 text-sm">N/A</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if ($soal->tipe_soal === 'pg')
                                                @if ($isCorrect)
                                                    <span class="badge badge-success gap-1">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="currentColor" viewBox="0 0 20 20">
                                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                                        </svg>
                                                        Benar
                                                    </span>
                                                @else
                                                    <span class="badge badge-error gap-1">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="currentColor" viewBox="0 0 20 20">
                                                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                                                        </svg>
                                                        Salah
                                                    </span>
                                                @endif
                                            @else
                                                <span class="badge badge-info">Essay</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>
@endsection
