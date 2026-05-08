<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2">
            <a href="{{ route('guru.siswa.index') }}" class="btn btn-ghost btn-sm">← Kembali</a>
            <h1 class="text-2xl font-bold text-base-content">Tambah Siswa (Bulk)</h1>
        </div>
    </x-slot>

    <div class="max-w-3xl">
        @if($errors->any())
            <div class="alert alert-error mb-4">
                <ul class="list-disc list-inside text-sm">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="card bg-base-100 shadow">
            <div class="card-body">
                <p class="text-base-content/70 mb-4 text-sm">
                    Isi data siswa di bawah. Klik <strong>+ Tambah Baris</strong> untuk menambah siswa lebih banyak (maks. 50).
                    Password default setiap siswa = NIS masing-masing.
                </p>

                <form method="POST" action="{{ route('guru.siswa.store') }}" id="bulk-form">
                    @csrf

                    <div id="siswa-rows">
                        <!-- Row 1 -->
                        <div class="grid grid-cols-12 gap-2 mb-2 items-end" data-row="0">
                            <div class="col-span-3">
                                <label class="label py-0"><span class="label-text text-xs">NIS</span></label>
                                <input type="text" name="siswas[0][nis]" class="input input-bordered input-sm w-full font-mono"
                                       placeholder="NIS" required maxlength="20" />
                            </div>
                            <div class="col-span-4">
                                <label class="label py-0"><span class="label-text text-xs">Nama Lengkap</span></label>
                                <input type="text" name="siswas[0][nama]" class="input input-bordered input-sm w-full"
                                       placeholder="Nama siswa" required />
                            </div>
                            <div class="col-span-2">
                                <label class="label py-0"><span class="label-text text-xs">Kelas</span></label>
                                <input type="text" name="siswas[0][kelas]" class="input input-bordered input-sm w-full"
                                       placeholder="VII-A" required maxlength="50" />
                            </div>
                            <div class="col-span-3">
                                <label class="label py-0"><span class="label-text text-xs">Email (opsional)</span></label>
                                <input type="email" name="siswas[0][email]" class="input input-bordered input-sm w-full"
                                       placeholder="email@..." />
                            </div>
                        </div>
                    </div>

                    <button type="button" id="add-row" class="btn btn-outline btn-sm mt-2 mb-6">
                        + Tambah Baris
                    </button>

                    <div class="card-actions">
                        <button type="submit" class="btn btn-primary">Simpan Semua Siswa</button>
                        <a href="{{ route('guru.siswa.index') }}" class="btn btn-ghost">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        let rowIndex = 1;
        document.getElementById('add-row').addEventListener('click', function() {
            if (rowIndex >= 50) {
                alert('Maksimum 50 siswa sekaligus.');
                return;
            }
            const container = document.getElementById('siswa-rows');
            const row = document.createElement('div');
            row.className = 'grid grid-cols-12 gap-2 mb-2 items-end';
            row.dataset.row = rowIndex;
            row.innerHTML = `
                <div class="col-span-3">
                    <input type="text" name="siswas[${rowIndex}][nis]" class="input input-bordered input-sm w-full font-mono"
                           placeholder="NIS" required maxlength="20" />
                </div>
                <div class="col-span-4">
                    <input type="text" name="siswas[${rowIndex}][nama]" class="input input-bordered input-sm w-full"
                           placeholder="Nama siswa" required />
                </div>
                <div class="col-span-2">
                    <input type="text" name="siswas[${rowIndex}][kelas]" class="input input-bordered input-sm w-full"
                           placeholder="VII-A" required maxlength="50" />
                </div>
                <div class="col-span-2">
                    <input type="email" name="siswas[${rowIndex}][email]" class="input input-bordered input-sm w-full"
                           placeholder="email@..." />
                </div>
                <div class="col-span-1">
                    <button type="button" class="btn btn-ghost btn-sm btn-error remove-row">✕</button>
                </div>
            `;
            container.appendChild(row);
            rowIndex++;
        });

        document.getElementById('siswa-rows').addEventListener('click', function(e) {
            if (e.target.classList.contains('remove-row')) {
                e.target.closest('[data-row]')?.remove();
            }
        });
    </script>
    @endpush
</x-app-layout>
