<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.siswa.index') }}" class="btn btn-ghost btn-sm">← Kembali</a>
            <h1 class="text-2xl font-bold text-base-content">Tambah Siswa</h1>
        </div>
    </x-slot>

    <div class="max-w-2xl">
        <div class="card bg-base-100 shadow">
            <div class="card-body">
                <form method="POST" action="{{ route('admin.siswa.store') }}">
                    @csrf

                    <!-- NIS -->
                    <div class="form-control mb-4">
                        <label class="label"><span class="label-text font-medium">NIS <span class="text-error">*</span></span></label>
                        <input type="text" name="nis" value="{{ old('nis') }}"
                               class="input input-bordered font-mono @error('nis') input-error @enderror"
                               placeholder="Nomor Induk Siswa" required maxlength="20" />
                        @error('nis')
                            <label class="label"><span class="label-text-alt text-error">{{ $message }}</span></label>
                        @enderror
                        <label class="label"><span class="label-text-alt text-base-content/50">Password default = NIS</span></label>
                    </div>

                    <!-- Nama -->
                    <div class="form-control mb-4">
                        <label class="label"><span class="label-text font-medium">Nama Lengkap <span class="text-error">*</span></span></label>
                        <input type="text" name="nama" value="{{ old('nama') }}"
                               class="input input-bordered @error('nama') input-error @enderror"
                               placeholder="Nama lengkap siswa" required />
                        @error('nama')
                            <label class="label"><span class="label-text-alt text-error">{{ $message }}</span></label>
                        @enderror
                    </div>

                    <!-- Kelas -->
                    <div class="form-control mb-4">
                        <label class="label"><span class="label-text font-medium">Kelas <span class="text-error">*</span></span></label>
                        <input type="text" name="kelas" value="{{ old('kelas') }}"
                               class="input input-bordered @error('kelas') input-error @enderror"
                               placeholder="Contoh: VII-A, VIII-B, IX-C" required maxlength="50" />
                        @error('kelas')
                            <label class="label"><span class="label-text-alt text-error">{{ $message }}</span></label>
                        @enderror
                    </div>

                    <!-- Email (optional) -->
                    <div class="form-control mb-4">
                        <label class="label">
                            <span class="label-text font-medium">Email</span>
                            <span class="label-text-alt text-base-content/50">Opsional</span>
                        </label>
                        <input type="email" name="email" value="{{ old('email') }}"
                               class="input input-bordered @error('email') input-error @enderror"
                               placeholder="email@siswa.sch.id" />
                        @error('email')
                            <label class="label"><span class="label-text-alt text-error">{{ $message }}</span></label>
                        @enderror
                    </div>

                    <!-- Status -->
                    <div class="form-control mb-6">
                        <label class="label cursor-pointer justify-start gap-3">
                            <input type="hidden" name="is_active" value="0" />
                            <input type="checkbox" name="is_active" value="1"
                                   class="toggle toggle-success" {{ old('is_active', true) ? 'checked' : '' }} />
                            <span class="label-text">Aktifkan akun siswa</span>
                        </label>
                    </div>

                    <div class="card-actions">
                        <button type="submit" class="btn btn-primary">Simpan Siswa</button>
                        <a href="{{ route('admin.siswa.index') }}" class="btn btn-ghost">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
