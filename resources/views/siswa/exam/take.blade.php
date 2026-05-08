<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $ujian->judul }} - UJIANKU-CBT</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="{{ asset('js/anti-cheat.js') }}"></script>
</head>
<body class="bg-base-200">
    <div id="exam-container" class="min-h-screen flex flex-col" data-ujian-id="{{ $ujian->id }}" data-end-time="{{ $ujian->tgl_selesai->getTimestamp() }}">
        <!-- Header -->
        <div class="bg-base-100 border-b border-base-300 sticky top-0 z-40 shadow">
            <div class="container mx-auto px-4 py-3">
                <div class="flex items-center justify-between">
                    <!-- Left: Exam Title -->
                    <div>
                        <h1 class="text-xl font-bold text-base-content">{{ $ujian->judul }}</h1>
                        <p class="text-sm text-base-content/60">{{ $ujian->kategoriUjian?->nama ?? '-' }}</p>
                    </div>

                    <!-- Center: Progress -->
                    <div class="flex items-center gap-4">
                        <div class="text-center">
                            <div class="text-xs text-base-content/60">Soal</div>
                            <div class="text-lg font-bold"><span id="current-question">1</span>/<span id="total-questions">{{ $ujian->soal->count() }}</span></div>
                        </div>
                        <progress id="exam-progress" class="progress progress-primary w-32" value="1" max="{{ $ujian->soal->count() }}"></progress>
                    </div>

                    <!-- Right: Timer & Exit -->
                    <div class="flex items-center gap-4">
                        <div id="exam-timer" class="text-center">
                            <div class="text-xs text-base-content/60">Sisa Waktu</div>
                            <div class="text-2xl font-bold font-mono text-warning" id="timer-display">{{ sprintf('%02d:%02d', intdiv($ujian->waktu_durasi, 60), $ujian->waktu_durasi % 60) }}</div>
                        </div>
                        <button type="button" onclick="confirmFinishExam()" class="btn btn-outline btn-sm">Selesaikan Ujian</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="flex-1 container mx-auto px-4 py-6">
            <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
                <!-- Left Column: Questions Navigation -->
                <div class="lg:col-span-1">
                    <div class="sticky top-24 card bg-base-100 shadow">
                        <div class="card-body">
                            <h3 class="card-title text-base">Daftar Soal</h3>
                            <div id="question-list" class="grid grid-cols-4 lg:grid-cols-3 gap-2 mt-2">
                                @foreach ($ujian->soal as $index => $soal)
                                    <button type="button" 
                                            class="btn btn-sm question-btn" 
                                            data-question-index="{{ $index }}"
                                            onclick="goToQuestion({{ $index }})"
                                            @if ($index === 0) data-active="true" @endif>
                                        {{ $index + 1 }}
                                    </button>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Middle & Right: Question & Answers -->
                <div class="lg:col-span-3">
                    <!-- Question Display -->
                    <div class="card bg-base-100 shadow mb-6">
                        <div class="card-body">
                            <div id="question-container" class="space-y-4">
                                <!-- Loaded via AJAX -->
                                <div class="skeleton h-32"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Navigation Buttons -->
                    <div class="flex gap-3 justify-between">
                        <button type="button" 
                                id="btn-previous" 
                                onclick="previousQuestion()"
                                class="btn btn-outline">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" /></svg>
                            Sebelumnya
                        </button>

                        <button type="button" 
                                onclick="saveCurrentAnswer()"
                                class="btn btn-ghost">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                            Simpan Jawaban
                        </button>

                        <button type="button" 
                                id="btn-next" 
                                onclick="nextQuestion()"
                                class="btn btn-outline">
                            Berikutnya
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Hidden form for finish exam -->
    <form id="exam-form" action="{{ route('siswa.exam.finish', $ujian->id) }}" method="POST" style="display: none;">
        @csrf
    </form>

    <script>
        // Global exam state
        const examState = {
            ujianId: {{ $ujian->id }},
            currentQuestion: 0,
            totalQuestions: {{ $ujian->soal->count() }},
            endTime: {{ $ujian->tgl_selesai->getTimestamp() * 1000 }},
            durationMinutes: {{ $ujian->waktu_durasi }},
            isSubmitting: false,
            autoSaveInterval: 5000, // Auto-save every 5 seconds
        };

        // Timer state
        let timerInterval = null;
        let lastSaveTime = 0;

        // Initialize
        document.addEventListener('DOMContentLoaded', () => {
            initializeExam();
        });

        /**
         * Initialize exam interface
         */
        function initializeExam() {
            updateTimerDisplay();
            startTimer();
            loadQuestion(0);
            setupAutoSave();
            console.log('[Exam] Exam interface initialized');
        }

        /**
         * Load specific question
         */
        async function loadQuestion(index) {
            if (index < 0 || index >= examState.totalQuestions) return;

            try {
                const response = await fetch(`/api/ujian/${examState.ujianId}/soal/${index}`, {
                    method: 'GET',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                    }
                });

                if (!response.ok) throw new Error('Failed to load question');

                const data = await response.json();
                renderQuestion(data);
                updateProgress(index);
                updateQuestionButtons();

            } catch (err) {
                console.error('[Exam] Error loading question:', err);
                showError('Gagal memuat soal. Silakan refresh halaman.');
            }
        }

        /**
         * Render question in UI
         */
        function renderQuestion(data) {
            const container = document.getElementById('question-container');
            const soal = data.soal;
            const jawabanSiswa = data.jawaban_siswa;

            let optionsHtml = '';
            ['a', 'b', 'c', 'd'].forEach(option => {
                const optionText = soal.opsi[option];
                const isSelected = jawabanSiswa === option;
                optionsHtml += `
                    <label class="flex items-start gap-3 p-3 rounded-lg border-2 cursor-pointer transition-all 
                                  ${isSelected ? 'border-primary bg-primary/5' : 'border-base-300 hover:border-primary'}">
                        <input type="radio" 
                               name="jawaban" 
                               value="${option}"
                               class="radio radio-primary mt-1"
                               ${isSelected ? 'checked' : ''}
                               onchange="markAnswerChanged()">
                        <div class="flex-1">
                            <div class="font-semibold text-base-content">${option.toUpperCase()}.</div>
                            <div class="text-base-content/70">${optionText}</div>
                        </div>
                    </label>
                `;
            });

            container.innerHTML = `
                <div class="mb-6">
                    <div class="flex items-center justify-between mb-2">
                        <h2 class="text-lg font-bold text-base-content">Soal ${data.index + 1}</h2>
                        <span class="badge badge-outline">Bobot: ${soal.bobot}</span>
                    </div>
                    <p class="text-base-content text-lg">${soal.pertanyaan}</p>
                </div>

                <div class="space-y-2">
                    <h3 class="font-semibold text-base-content mb-3">Pilih jawaban Anda:</h3>
                    ${optionsHtml}
                </div>
            `;

            examState.currentQuestion = data.index;
            document.getElementById('current-question').textContent = data.index + 1;
            document.getElementById('total-questions').textContent = data.total;
        }

        /**
         * Save current answer
         */
        async function saveCurrentAnswer() {
            const jawaban = document.querySelector('input[name="jawaban"]:checked')?.value || null;
            if (jawaban === null) {
                showWarning('Pilih salah satu jawaban terlebih dahulu');
                return;
            }

            try {
                const response = await fetch(`/api/ujian/${examState.ujianId}/answer`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    body: JSON.stringify({
                        soal_id: getSoalIdByIndex(examState.currentQuestion),
                        jawaban: jawaban,
                    })
                });

                if (!response.ok) throw new Error('Failed to save answer');

                const data = await response.json();
                console.log('[Exam] Answer saved:', data);
                markQuestionAnswered(examState.currentQuestion);
                showSuccess('Jawaban disimpan');
                lastSaveTime = Date.now();

            } catch (err) {
                console.error('[Exam] Error saving answer:', err);
                showError('Gagal menyimpan jawaban');
            }
        }

        /**
         * Auto-save answers every 5 seconds
         */
        function setupAutoSave() {
            setInterval(() => {
                const jawaban = document.querySelector('input[name="jawaban"]:checked')?.value;
                if (jawaban && Date.now() - lastSaveTime > 3000) {
                    saveCurrentAnswer();
                }
            }, examState.autoSaveInterval);
        }

        /**
         * Navigate to next question
         */
        function nextQuestion() {
            if (examState.currentQuestion < examState.totalQuestions - 1) {
                loadQuestion(examState.currentQuestion + 1);
            }
        }

        /**
         * Navigate to previous question
         */
        function previousQuestion() {
            if (examState.currentQuestion > 0) {
                loadQuestion(examState.currentQuestion - 1);
            }
        }

        /**
         * Go to specific question
         */
        function goToQuestion(index) {
            saveCurrentAnswer(); // Auto-save before switching
            loadQuestion(index);
        }

        /**
         * Update progress bar
         */
        function updateProgress(index) {
            document.getElementById('exam-progress').value = index + 1;
        }

        /**
         * Update question button states
         */
        function updateQuestionButtons() {
            document.getElementById('btn-previous').disabled = examState.currentQuestion === 0;
            document.getElementById('btn-next').disabled = examState.currentQuestion === examState.totalQuestions - 1;
        }

        /**
         * Mark question as answered
         */
        function markQuestionAnswered(index) {
            const btn = document.querySelector(`[data-question-index="${index}"]`);
            if (btn) {
                btn.classList.add('btn-primary');
                btn.classList.remove('btn-outline');
            }
        }

        /**
         * Mark answer changed
         */
        function markAnswerChanged() {
            markQuestionAnswered(examState.currentQuestion);
        }

        /**
         * Start timer countdown
         */
        function startTimer() {
            timerInterval = setInterval(() => {
                updateTimerDisplay();

                // Check if time expired
                const now = Date.now();
                if (now >= examState.endTime) {
                    clearInterval(timerInterval);
                    finishExam();
                }
            }, 1000);
        }

        /**
         * Update timer display
         */
        function updateTimerDisplay() {
            const now = Date.now();
            const secondsRemaining = Math.max(0, Math.floor((examState.endTime - now) / 1000));

            const minutes = Math.floor(secondsRemaining / 60);
            const seconds = secondsRemaining % 60;

            document.getElementById('timer-display').textContent = 
                `${String(minutes).padStart(2, '0')}:${String(seconds).padStart(2, '0')}`;

            // Warning when 5 minutes left
            if (secondsRemaining === 300) {
                showWarning('⚠️ Waktu ujian tinggal 5 menit!');
                showBrowserNotification('Waktu ujian tinggal 5 menit!');
            }

            // Critical warning when 1 minute left
            if (secondsRemaining === 60) {
                showWarning('⚠️ WAKTU HABIS DALAM 1 MENIT!');
                showBrowserNotification('Waktu ujian tinggal 1 menit!');
                document.getElementById('timer-display').classList.add('text-error', 'animate-pulse');
            }
        }

        /**
         * Confirm finish exam
         */
        function confirmFinishExam() {
            if (confirm('Apakah Anda yakin ingin menyelesaikan ujian? Anda tidak dapat mengubah jawaban setelah ini.')) {
                finishExam();
            }
        }

        /**
         * Finish exam and submit
         */
        async function finishExam() {
            if (examState.isSubmitting) return;
            examState.isSubmitting = true;

            try {
                // Save current answer first
                const jawaban = document.querySelector('input[name="jawaban"]:checked')?.value;
                if (jawaban) {
                    await saveCurrentAnswer();
                }

                // Show loading state
                const modal = document.createElement('div');
                modal.className = 'modal modal-open';
                modal.id = 'finishing-modal';
                modal.innerHTML = `
                    <div class="modal-box">
                        <h3 class="font-bold text-lg">Menyelesaikan Ujian</h3>
                        <p class="py-4">Jawaban Anda sedang dikirim ke server...</p>
                        <div class="loading loading-spinner"></div>
                    </div>
                `;
                document.body.appendChild(modal);

                // Finish exam on server
                const response = await fetch(`/api/ujian/${examState.ujianId}/finish`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'X-Requested-With': 'XMLHttpRequest',
                    }
                });

                const data = await response.json();
                if (data.success) {
                    showSuccess('Ujian selesai. Jawaban Anda telah disimpan.');
                    setTimeout(() => {
                        window.location.href = '/siswa/ujian';
                    }, 2000);
                } else {
                    throw new Error(data.message || 'Failed to finish exam');
                }

            } catch (err) {
                console.error('[Exam] Error finishing exam:', err);
                showError('Gagal menyelesaikan ujian. Silakan coba lagi atau hubungi administrator.');
                examState.isSubmitting = false;
            }
        }

        /**
         * Show browser notification
         */
        function showBrowserNotification(message) {
            if ('Notification' in window && Notification.permission === 'granted') {
                new Notification('UJIANKU-CBT', {
                    body: message,
                    icon: '/img/logo.png',
                });
            }
        }

        /**
         * Show success message
         */
        function showSuccess(message) {
            const alert = document.createElement('div');
            alert.className = 'alert alert-success shadow-lg fixed top-4 right-4 w-96 z-50';
            alert.innerHTML = `
                <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                <span>${message}</span>
            `;
            document.body.appendChild(alert);
            setTimeout(() => alert.remove(), 3000);
        }

        /**
         * Show warning message
         */
        function showWarning(message) {
            const alert = document.createElement('div');
            alert.className = 'alert alert-warning shadow-lg fixed top-4 right-4 w-96 z-50';
            alert.innerHTML = `
                <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4v2m0 4v2M7.08 6.24a9 9 0 1011.84 11.52M7.08 6.24L9.9 9m11.84 11.52l2.82 2.82" /></svg>
                <span>${message}</span>
            `;
            document.body.appendChild(alert);
            setTimeout(() => alert.remove(), 5000);
        }

        /**
         * Show error message
         */
        function showError(message) {
            const alert = document.createElement('div');
            alert.className = 'alert alert-error shadow-lg fixed top-4 right-4 w-96 z-50';
            alert.innerHTML = `
                <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                <span>${message}</span>
            `;
            document.body.appendChild(alert);
            setTimeout(() => alert.remove(), 5000);
        }

        /**
         * Get soal ID by index (placeholder - would be passed from server)
         */
        function getSoalIdByIndex(index) {
            // This should be populated from the rendered soal data
            const soalIds = @json($ujian->soal->pluck('id')->toArray());
            return soalIds[index] || null;
        }

        // Request notification permission
        if ('Notification' in window && Notification.permission === 'default') {
            Notification.requestPermission();
        }
    </script>
</body>
</html>
