<x-app-layout>
    <x-slot name="header">
        <h1 class="text-2xl font-bold text-base-content">Riwayat & Hasil Ujian</h1>
    </x-slot>

    <div class="card bg-base-100 shadow">
        <div class="card-body">
            <h2 class="card-title">Hasil Ujian Anda</h2>
            <p class="text-base-content/70">Hasil ujian akan ditampilkan di sini setelah Anda menyelesaikan ujian.</p>
            <div class="alert alert-info mt-4">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="stroke-current shrink-0 w-6 h-6"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <span>Belum ada hasil ujian. Mulai ujian dari menu Daftar Ujian.</span>
            </div>
        </div>
    </div>
</x-app-layout>
