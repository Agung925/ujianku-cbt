{{-- resources/views/layouts/exam.blade.php --}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'UJIANKU-CBT') }} - Ujian</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @stack('styles')
</head>
<body class="font-sans antialiased bg-base-100 min-h-screen">

    <!-- Exam Header (Sticky) -->
    <div class="sticky top-0 z-50 bg-base-100 shadow-md border-b border-base-300">
        <div class="flex items-center justify-between p-4">
            <div class="flex items-center gap-4">
                <h1 class="text-lg font-bold text-base-content">{{ $exam->judul ?? 'Ujian' }}</h1>
                <div class="badge badge-primary">{{ $exam->kategori_ujian->nama ?? 'Kategori' }}</div>
            </div>
            
            <!-- Timer -->
            <div class="flex items-center gap-2">
                <div class="countdown font-mono text-2xl gap-2" id="exam-timer">
                    <span style="--value:00;"></span>:
                    <span style="--value:30;"></span>
                </div>
                <button class="btn btn-sm btn-warning" onclick="confirmSubmit()">
                    Selesai
                </button>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <main class="flex-1 p-6">
        {{ $slot }}
    </main>

    <!-- Anti-Cheat JavaScript -->
    <script>
        // Disable right-click
        document.addEventListener('contextmenu', function(e) {
            e.preventDefault();
            return false;
        });

        // Detect fullscreen exit
        document.addEventListener('fullscreenchange', function() {
            if (!document.fullscreenElement) {
                alert('Ujian harus dalam mode fullscreen. Ujian akan dihentikan.');
                window.location.href = '{{ route('siswa.ujian.index') }}';
            }
        });

        // Detect tab/window switch
        document.addEventListener('visibilitychange', function() {
            if (document.hidden) {
                alert('Anda tidak boleh pindah tab atau window saat ujian berlangsung!');
            }
        });

        // Disable copy-paste
        document.addEventListener('copy', function(e) {
            e.preventDefault();
            return false;
        });

        document.addEventListener('paste', function(e) {
            e.preventDefault();
            return false;
        });

        // Prevent exam submission form multiple times
        function confirmSubmit() {
            if (confirm('Apakah Anda yakin ingin mengakhiri ujian? Jawaban yang belum disimpan akan hilang.')) {
                document.getElementById('exam-form').submit();
            }
        }

        // Timer countdown
        function startExamTimer(durationInMinutes) {
            let totalSeconds = durationInMinutes * 60;
            setInterval(function() {
                if (totalSeconds <= 0) {
                    alert('Waktu ujian telah habis!');
                    document.getElementById('exam-form').submit();
                    return;
                }
                
                const minutes = Math.floor(totalSeconds / 60);
                const seconds = totalSeconds % 60;
                
                document.querySelector('[style*="--value:00"]').style.setProperty('--value', 
                    String(Math.floor(minutes / 10)).padStart(2, '0'));
                document.querySelector('[style*="--value:30"]').style.setProperty('--value', 
                    String(Math.floor(minutes % 10)).padStart(2, '0'));
                
                totalSeconds--;
            }, 1000);
        }

        // Start timer on page load
        document.addEventListener('DOMContentLoaded', function() {
            startExamTimer({{ $exam->waktu_durasi ?? 60 }});
        });
    </script>

    @stack('scripts')
</body>
</html>
