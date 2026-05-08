/**
 * Anti-Cheat System for UJIANKU-CBT Exam Platform
 * Implements comprehensive anti-cheat mechanisms:
 * - Fullscreen enforcement with exit detection
 * - Tab/window switch detection
 * - Copy-paste prevention
 * - Right-click context menu prevention
 * - Time sync verification
 * - Multiple window detection
 */

class AntiCheatSystem {
    constructor(config = {}) {
        this.ujianId = config.ujianId;
        this.maxTabSwitches = config.maxTabSwitches || 3;
        this.timeSyncInterval = config.timeSyncInterval || 10000; // 10 seconds
        this.tabSwitchCount = 0;
        this.isFullscreenWarningShown = false;
        this.warningDismissCount = 0;
        this.maxWarningDismiss = 2;
        this.isExamActive = true;

        this.initializeAntiCheat();
    }

    /**
     * Initialize all anti-cheat mechanisms
     */
    initializeAntiCheat() {
        this.initFullscreenDetection();
        this.initVisibilityDetection();
        this.initPreventCopyPaste();
        this.initPreventRightClick();
        this.initTimeSync();
        this.initSessionDetection();
        this.initBeforeUnload();
        console.log('[AntiCheat] Anti-cheat system initialized');
    }

    /**
     * Fullscreen Detection & Enforcement
     */
    initFullscreenDetection() {
        // Request fullscreen on exam start
        this.requestFullscreen();

        // Detect fullscreen exit
        document.addEventListener('fullscreenchange', () => this.handleFullscreenChange());
        document.addEventListener('webkitfullscreenchange', () => this.handleFullscreenChange());
        document.addEventListener('mozfullscreenchange', () => this.handleFullscreenChange());
        document.addEventListener('MSFullscreenChange', () => this.handleFullscreenChange());
    }

    /**
     * Request fullscreen for exam container
     */
    async requestFullscreen() {
        const examContainer = document.getElementById('exam-container');
        if (!examContainer) return;

        try {
            if (examContainer.requestFullscreen) {
                await examContainer.requestFullscreen();
            } else if (examContainer.webkitRequestFullscreen) {
                await examContainer.webkitRequestFullscreen();
            } else if (examContainer.mozRequestFullScreen) {
                await examContainer.mozRequestFullScreen();
            }
            console.log('[AntiCheat] Fullscreen requested');
        } catch (err) {
            console.warn('[AntiCheat] Fullscreen request failed:', err.message);
            this.showWarning('Aktivitas mencurigai: Mode fullscreen diperlukan untuk ujian ini.');
        }
    }

    /**
     * Handle fullscreen change events
     */
    handleFullscreenChange() {
        const isInFullscreen = document.fullscreenElement || document.webkitFullscreenElement
            || document.mozFullScreenElement || document.msFullscreenElement;

        if (!isInFullscreen && this.isExamActive) {
            this.handleFullscreenExit();
        }
    }

    /**
     * Handle fullscreen exit
     */
    handleFullscreenExit() {
        this.warningDismissCount++;

        if (this.warningDismissCount === 1) {
            // First warning
            this.showWarning('⚠️ PERINGATAN: Keluar dari fullscreen akan mengakhiri ujian. Mode fullscreen diperlukan untuk keamanan ujian.');
            // Try to re-enter fullscreen after 2 seconds
            setTimeout(() => this.requestFullscreen(), 2000);
        } else if (this.warningDismissCount >= this.maxWarningDismiss) {
            // Force submit exam
            this.forceExitExam('Ujian berakhir karena keluar dari fullscreen berulang kali.');
        }
    }

    /**
     * Visibility Detection (Tab/Window Switch)
     */
    initVisibilityDetection() {
        document.addEventListener('visibilitychange', () => this.handleVisibilityChange());
        window.addEventListener('blur', () => this.handleWindowBlur());
        window.addEventListener('focus', () => this.handleWindowFocus());
    }

    /**
     * Handle visibility change (tab switch)
     */
    handleVisibilityChange() {
        if (document.hidden) {
            this.tabSwitchCount++;
            console.warn('[AntiCheat] Tab switched away. Count:', this.tabSwitchCount);

            if (this.tabSwitchCount === 1) {
                this.showWarning('⚠️ PERINGATAN: Pindah ke tab lain akan mengakhiri ujian. Tetap di jendela ujian ini.');
            } else if (this.tabSwitchCount >= this.maxTabSwitches) {
                this.forceExitExam('Ujian berakhir: Pindah tab/window terlalu sering.');
            } else {
                this.pauseTimer();
            }
        } else {
            // Tab/window is focused again
            this.resumeTimer();
        }
    }

    /**
     * Handle window blur (focus loss)
     */
    handleWindowBlur() {
        if (this.isExamActive) {
            this.pauseTimer();
            console.warn('[AntiCheat] Window lost focus');
        }
    }

    /**
     * Handle window focus (regained)
     */
    handleWindowFocus() {
        if (this.isExamActive) {
            this.resumeTimer();
            console.log('[AntiCheat] Window regained focus');
        }
    }

    /**
     * Prevent Copy-Paste
     */
    initPreventCopyPaste() {
        document.addEventListener('copy', (e) => this.preventCopyPaste(e));
        document.addEventListener('cut', (e) => this.preventCopyPaste(e));
        document.addEventListener('paste', (e) => this.preventCopyPaste(e));
    }

    /**
     * Prevent copy/cut/paste action
     */
    preventCopyPaste(e) {
        e.preventDefault();
        console.warn('[AntiCheat] Copy/Paste/Cut attempt blocked');
        return false;
    }

    /**
     * Prevent Right-Click Context Menu
     */
    initPreventRightClick() {
        document.addEventListener('contextmenu', (e) => this.preventRightClick(e));
    }

    /**
     * Prevent right-click
     */
    preventRightClick(e) {
        e.preventDefault();
        console.warn('[AntiCheat] Right-click attempt blocked');
        return false;
    }

    /**
     * Time Sync Verification
     */
    initTimeSync() {
        setInterval(() => this.verifyTimeSync(), this.timeSyncInterval);
    }

    /**
     * Verify server time matches client time
     */
    async verifyTimeSync() {
        if (!this.isExamActive) return;

        try {
            const response = await fetch(`/api/ujian/${this.ujianId}/time-remaining`, {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                }
            });

            const data = await response.json();

            if (data.expired) {
                this.forceExitExam('Waktu ujian telah berakhir.');
                return;
            }

            // Check if time difference is too large (potential client time tampering)
            const clientTimeRemaining = this.getClientTimeRemaining();
            const serverTimeRemaining = data.seconds_remaining;
            const timeDifference = Math.abs(clientTimeRemaining - serverTimeRemaining);

            if (timeDifference > 60) {
                // More than 60 seconds difference
                this.forceExitExam('Waktu tidak sinkron. Ujian berakhir karena deteksi kecurangan.');
            }

            console.debug('[AntiCheat] Time sync check passed', {
                client: clientTimeRemaining,
                server: serverTimeRemaining,
            });
        } catch (err) {
            console.error('[AntiCheat] Time sync verification failed:', err);
        }
    }

    /**
     * Get client-side time remaining (for comparison)
     */
    getClientTimeRemaining() {
        const endTimeElement = document.getElementById('exam-end-time');
        if (!endTimeElement) return 0;

        const endTime = parseInt(endTimeElement.dataset.endTime);
        const now = Math.floor(Date.now() / 1000);
        return Math.max(0, endTime - now);
    }

    /**
     * Session Detection (Multiple Window Prevention)
     */
    initSessionDetection() {
        const sessionId = 'exam_session_' + this.ujianId;
        const currentSessionId = sessionStorage.getItem(sessionId);

        if (currentSessionId) {
            // Session already exists - potential multiple window
            const sessionDuration = Date.now() - parseInt(sessionStorage.getItem(sessionId + '_time'));
            if (sessionDuration < 30000) { // Within 30 seconds
                this.forceExitExam('Ujian diakses dari jendela/perangkat lain. Hanya satu jendela ujian yang diizinkan.');
                return;
            }
        }

        // Store current session
        sessionStorage.setItem(sessionId, Date.now().toString());
        sessionStorage.setItem(sessionId + '_time', Date.now().toString());

        console.log('[AntiCheat] Session ID registered:', sessionId);
    }

    /**
     * Before Unload Handler
     */
    initBeforeUnload() {
        window.addEventListener('beforeunload', (e) => {
            if (this.isExamActive) {
                e.preventDefault();
                e.returnValue = 'Ujian sedang berlangsung. Apakah Anda yakin ingin keluar?';
                return 'Ujian sedang berlangsung. Apakah Anda yakin ingin keluar?';
            }
        });
    }

    /**
     * Show warning modal
     */
    showWarning(message) {
        if (this.isFullscreenWarningShown) return;
        this.isFullscreenWarningShown = true;

        // Use DaisyUI modal or simple alert
        const modal = document.createElement('div');
        modal.className = 'modal modal-open';
        modal.id = 'anticheat-warning';
        modal.innerHTML = `
            <div class="modal-box">
                <h3 class="font-bold text-lg text-warning">⚠️ Peringatan Keamanan Ujian</h3>
                <p class="py-4">${message}</p>
                <div class="modal-action">
                    <button onclick="document.getElementById('anticheat-warning').remove(); window.antiCheatSystem.isFullscreenWarningShown = false;" class="btn btn-primary">
                        Saya Pahami
                    </button>
                </div>
            </div>
        `;
        document.body.appendChild(modal);

        console.warn('[AntiCheat] Warning shown:', message);
    }

    /**
     * Pause timer when exam loses focus
     */
    pauseTimer() {
        const timerElement = document.getElementById('exam-timer');
        if (timerElement) {
            timerElement.classList.add('opacity-50');
        }
    }

    /**
     * Resume timer when exam regains focus
     */
    resumeTimer() {
        const timerElement = document.getElementById('exam-timer');
        if (timerElement) {
            timerElement.classList.remove('opacity-50');
        }
    }

    /**
     * Force exit exam and submit all answers
     */
    async forceExitExam(reason) {
        this.isExamActive = false;
        console.error('[AntiCheat] Force exit triggered:', reason);

        // Show alert
        const modal = document.createElement('div');
        modal.className = 'modal modal-open';
        modal.id = 'anticheat-force-exit';
        modal.innerHTML = `
            <div class="modal-box">
                <h3 class="font-bold text-lg text-error">Ujian Berakhir</h3>
                <p class="py-4">${reason}</p>
                <p class="py-2 text-sm text-base-content/60">Jawaban Anda akan dikirim ke server secara otomatis.</p>
                <div class="modal-action">
                    <button disabled class="btn">Mengirim...</button>
                </div>
            </div>
        `;
        document.body.appendChild(modal);

        // Auto-submit exam
        await this.autoSubmitExam();
    }

    /**
     * Auto-submit exam
     */
    async autoSubmitExam() {
        try {
            const form = document.getElementById('exam-form');
            if (!form) {
                console.error('[AntiCheat] Exam form not found');
                return;
            }

            const response = await fetch(form.action, {
                method: 'POST',
                body: new FormData(form),
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                }
            });

            const data = await response.json();
            if (data.success) {
                console.log('[AntiCheat] Exam auto-submitted successfully');
                setTimeout(() => {
                    window.location.href = data.redirect || '/siswa/ujian';
                }, 2000);
            }
        } catch (err) {
            console.error('[AntiCheat] Auto-submit failed:', err);
        }
    }

    /**
     * Disable developer tools
     */
    disableDeveloperTools() {
        // Disable F12
        document.addEventListener('keydown', (e) => {
            if (e.key === 'F12' || (e.ctrlKey && e.shiftKey && e.key === 'I')) {
                e.preventDefault();
                console.warn('[AntiCheat] Developer tools access blocked');
                return false;
            }
        });
    }
}

// Initialize anti-cheat system when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
    const ujianId = document.getElementById('exam-container')?.dataset.ujianId;
    if (ujianId) {
        window.antiCheatSystem = new AntiCheatSystem({
            ujianId: ujianId,
            maxTabSwitches: 3,
            timeSyncInterval: 10000,
        });
    }
});
