<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.guru.index') }}" class="btn btn-ghost btn-sm">← Kembali</a>
            <h1 class="text-2xl font-bold text-base-content">Tambah Guru</h1>
        </div>
    </x-slot>

    <div class="max-w-2xl">
        <div class="card bg-base-100 shadow">
            <div class="card-body">
                <form method="POST" action="{{ route('admin.guru.store') }}">
                    @csrf

                    <!-- Nama -->
                    <div class="form-control mb-4">
                        <label class="label"><span class="label-text font-medium">Nama Lengkap <span class="text-error">*</span></span></label>
                        <input type="text" name="nama" value="{{ old('nama') }}"
                               class="input input-bordered @error('nama') input-error @enderror"
                               placeholder="Nama lengkap guru" required />
                        @error('nama')
                            <label class="label"><span class="label-text-alt text-error">{{ $message }}</span></label>
                        @enderror
                    </div>

                    <!-- Email -->
                    <div class="form-control mb-4">
                        <label class="label"><span class="label-text font-medium">Email <span class="text-error">*</span></span></label>
                        <input type="email" name="email" value="{{ old('email') }}"
                               class="input input-bordered @error('email') input-error @enderror"
                               placeholder="email@sekolah.sch.id" required />
                        @error('email')
                            <label class="label"><span class="label-text-alt text-error">{{ $message }}</span></label>
                        @enderror
                    </div>

                    <!-- NIP -->
                    <div class="form-control mb-4">
                        <label class="label">
                            <span class="label-text font-medium">NIP</span>
                            <span class="label-text-alt text-base-content/50">Opsional — otomatis dibuat jika kosong</span>
                        </label>
                        <input type="text" name="nip" value="{{ old('nip') }}"
                               class="input input-bordered @error('nip') input-error @enderror"
                               placeholder="Nomor Induk Pegawai" />
                        @error('nip')
                            <label class="label"><span class="label-text-alt text-error">{{ $message }}</span></label>
                        @enderror
                    </div>

                    <!-- Toggle options -->
                    <div class="flex gap-6 mb-6">
                        <div class="form-control">
                            <label class="label cursor-pointer gap-3">
                                <span class="label-text">Wali Kelas</span>
                                <input type="hidden" name="is_wali_kelas" value="0" />
                                <input type="checkbox" name="is_wali_kelas" value="1"
                                       class="toggle toggle-info" {{ old('is_wali_kelas') ? 'checked' : '' }} />
                            </label>
                        </div>
                        <div class="form-control">
                            <label class="label cursor-pointer gap-3">
                                <span class="label-text">Aktif</span>
                                <input type="hidden" name="is_active" value="0" />
                                <input type="checkbox" name="is_active" value="1"
                                       class="toggle toggle-success" {{ old('is_active', true) ? 'checked' : '' }} />
                            </label>
                        </div>
                    </div>

                    <div class="card-actions">
                        <button type="submit" class="btn btn-primary">Simpan Guru</button>
                        <a href="{{ route('admin.guru.index') }}" class="btn btn-ghost">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
