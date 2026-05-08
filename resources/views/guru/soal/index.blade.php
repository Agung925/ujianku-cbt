<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-bold text-base-content">Bank Soal</h1>
            <a href="#" class="btn btn-primary btn-sm disabled">+ Buat Soal</a>
        </div>
    </x-slot>

    <div class="card bg-base-100 shadow">
        <div class="card-body">
            <h2 class="card-title">Daftar Soal Anda</h2>
            <p class="text-base-content/70">Fitur bank soal akan segera tersedia. Anda dapat membuat dan mengelola soal di sini.</p>
            <div class="alert alert-info mt-4">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="stroke-current shrink-0 w-6 h-6"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <span>Fitur ini akan tersedia di Phase 4.</span>
            </div>
        </div>
    </div>
</x-app-layout>
