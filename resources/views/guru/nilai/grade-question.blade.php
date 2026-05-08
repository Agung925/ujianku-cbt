@extends('layouts.app')

@section('title', 'Nilai Soal - ' . $ujian->judul)

@section('content')
<div class="container mx-auto p-6">
    <!-- Header -->
    <div class="mb-8">
        <div class="breadcrumbs text-sm mb-4">
            <ul>
                <li><a href="{{ route('guru.nilai.index') }}">Penilaian</a></li>
                <li><a href="{{ route('guru.nilai.grade-exam', $ujian->id) }}">{{ $ujian->judul }}</a></li>
                <li class="font-bold">Soal #{{ $soal->id }}</li>
            </ul>
        </div>
        <h1 class="text-3xl font-bold text-gray-900">Penilaian Soal</h1>
        <p class="text-gray-600 mt-2">{{ $ujian->judul }}</p>
    </div>

    <!-- Question Details Card -->
    <div class="card bg-white shadow-lg mb-6">
        <div class="card-body">
            <div class="flex justify-between items-start mb-4">
                <h2 class="card-title">Soal</h2>
                <div class="flex gap-2">
                    <span class="badge badge-lg">{{ strtoupper($soal->tipe_soal) }}</span>
                    <span class="badge badge-lg badge-outline">Bobot: {{ $soal->bobot }}</span>
                </div>
            </div>

            <div class="bg-base-200 p-4 rounded-lg mb-4">
                <p class="text-gray-700 whitespace-pre-wrap">{{ $soal->teks_soal }}</p>
            </div>

            @if ($soal->tipe_soal === 'pg')
                <div class="space-y-2">
                    <p class="font-semibold text-gray-700">Pilihan Jawaban:</p>
                    <div class="space-y-1 ml-4">
                        <p><strong>A.</strong> {{ $soal->opsi_a }}</p>
                        <p><strong>B.</strong> {{ $soal->opsi_b }}</p>
                        <p><strong>C.</strong> {{ $soal->opsi_c }}</p>
                        <p><strong>D.</strong> {{ $soal->opsi_d }}</p>
                    </div>
                    <p class="mt-4">
                        <strong>Kunci Jawaban:</strong>
                        <span class="badge badge-success">{{ $soal->kunci_jawaban }}</span>
                    </p>
                </div>
            @endif
        </div>
    </div>

    <!-- Student Answers -->
    <div class="card bg-white shadow-lg">
        <div class="card-body">
            <h2 class="card-title mb-4">Jawaban Siswa ({{ $studentAnswers->count() }})</h2>

            @if ($studentAnswers->count() > 0)
                <div class="space-y-4">
                    @foreach ($studentAnswers as $index => $answer)
                        <div class="card bg-base-100 border-l-4 {{ $soal->tipe_soal === 'pg' && $answer->jawaban === $soal->kunci_jawaban ? 'border-success' : ($soal->tipe_soal === 'pg' ? 'border-error' : 'border-info') }}">
                            <div class="card-body">
                                <div class="flex justify-between items-start mb-2">
                                    <h3 class="card-title text-base">
                                        {{ $index + 1 }}. {{ $answer->siswa->nama_siswa }}
                                    </h3>
                                    @if ($soal->tipe_soal === 'pg')
                                        @if ($answer->jawaban === $soal->kunci_jawaban)
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
                                        <span class="badge badge-info">Perlu Dinilai</span>
                                    @endif
                                </div>

                                <div class="grid grid-cols-2 gap-4 mb-4">
                                    <div>
                                        <p class="text-sm text-gray-600">Jawaban Siswa</p>
                                        <p class="text-lg font-bold text-primary">
                                            {{ $answer->jawaban ?? '-' }}
                                        </p>
                                    </div>
                                    @if ($soal->tipe_soal === 'pg')
                                        <div>
                                            <p class="text-sm text-gray-600">Kunci Jawaban</p>
                                            <p class="text-lg font-bold text-success">
                                                {{ $soal->kunci_jawaban }}
                                            </p>
                                        </div>
                                    @else
                                        <!-- Grading Form for Essay -->
                                        <form method="POST" action="{{ route('guru.nilai.submit-grade', $ujian->id) }}"
                                            class="space-y-2">
                                            @csrf
                                            <input type="hidden" name="siswa_id" value="{{ $answer->siswa_id }}">
                                            <input type="hidden" name="soal_id" value="{{ $answer->soal_id }}">

                                            <div>
                                                <label class="text-sm text-gray-600">Nilai Essay</label>
                                                <input type="number" name="nilai" class="input input-sm input-bordered w-full"
                                                    min="0" max="100" step="0.5" required
                                                    placeholder="Masukkan nilai (0-100)">
                                            </div>

                                            <button type="submit" class="btn btn-sm btn-primary w-full">
                                                Simpan Nilai
                                            </button>
                                        </form>
                                    @endif
                                </div>

                                @if ($soal->tipe_soal === 'essay')
                                    <div class="bg-base-200 p-3 rounded-lg">
                                        <p class="text-sm text-gray-600 mb-2">Jawaban Essay:</p>
                                        <p class="text-gray-800 whitespace-pre-wrap">{{ $answer->jawaban }}</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-12">
                    <p class="text-gray-500 text-lg">Belum ada jawaban siswa untuk soal ini</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Back Button -->
    <div class="mt-6">
        <a href="{{ route('guru.nilai.grade-exam', $ujian->id) }}" class="btn btn-outline gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
            Kembali
        </a>
    </div>
</div>
@endsection
