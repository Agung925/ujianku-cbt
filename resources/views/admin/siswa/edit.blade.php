<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.siswa.index') }}" class="btn btn-ghost btn-sm">← Kembali</a>
            <h1 class="text-2xl font-bold text-base-content">Edit Siswa — {{ $siswa->nama }}</h1>
        </div>
    </x-slot>

    @if(session('success'))
        <div class="alert alert-success mb-4"><span>{{ session('success') }}</span></div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 max-w-4xl">
        <!-- Edit Form -->
        <div class="lg:col-span-2">
            <div class="card bg-base-100 shadow">
                <div class="card-body">
                    <h2 class="card-title text-lg mb-2">Data Siswa</h2>
                    <form method="POST" action="{{ route('admin.siswa.update', $siswa) }}">
                        @csrf
                        @method('PUT')

                        <div class="form-control mb-4">
                            <label class="label"><span class="label-text font-medium">NIS <span class="text-error">*</span></span></label>
                            <input type="text" name="nis" value="{{ old('nis', $siswa->nis) }}"
                                   class="input input-bordered font-mono @error('nis') input-error @enderror" required />
                            @error('nis')
                                <label class="label"><span class="label-text-alt text-error">{{ $message }}</span></label>
                            @enderror
                        </div>

                        <div class="form-control mb-4">
                            <label class="label"><span class="label-text font-medium">Nama Lengkap <span class="text-error">*</span></span></label>
                            <input type="text" name="nama" value="{{ old('nama', $siswa->nama) }}"
                                   class="input input-bordered @error('nama') input-error @enderror" required />
                            @error('nama')
                                <label class="label"><span class="label-text-alt text-error">{{ $message }}</span></label>
                            @enderror
                        </div>

                        <div class="form-control mb-4">
                            <label class="label"><span class="label-text font-medium">Kelas <span class="text-error">*</span></span></label>
                            <input type="text" name="kelas" value="{{ old('kelas', $siswa->kelas) }}"
                                   class="input input-bordered @error('kelas') input-error @enderror" required />
                            @error('kelas')
                                <label class="label"><span class="label-text-alt text-error">{{ $message }}</span></label>
                            @enderror
                        </div>

                        <div class="form-control mb-4">
                            <label class="label">
                                <span class="label-text font-medium">Email</span>
                                <span class="label-text-alt text-base-content/50">Opsional</span>
                            </label>
                            <input type="email" name="email" value="{{ old('email', $siswa->email) }}"
                                   class="input input-bordered @error('email') input-error @enderror" />
                            @error('email')
                                <label class="label"><span class="label-text-alt text-error">{{ $message }}</span></label>
                            @enderror
                        </div>

                        <div class="form-control mb-6">
                            <label class="label cursor-pointer justify-start gap-3">
                                <input type="hidden" name="is_active" value="0" />
                                <input type="checkbox" name="is_active" value="1"
                                       class="toggle toggle-success" {{ old('is_active', $siswa->is_active) ? 'checked' : '' }} />
                                <span class="label-text">Akun Aktif</span>
                            </label>
                        </div>

                        <div class="card-actions">
                            <button type="submit" class="btn btn-primary">Perbarui Data</button>
                            <a href="{{ route('admin.siswa.index') }}" class="btn btn-ghost">Batal</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Foto + Actions -->
        <div class="flex flex-col gap-4">
            <!-- Upload Foto -->
            <div class="card bg-base-100 shadow items-center text-center">
                <div class="card-body">
                    <h2 class="card-title text-base justify-center mb-2">Foto Siswa</h2>
                    @if($siswa->foto)
                        <div class="avatar mb-2">
                            <div class="w-20 rounded-full ring ring-secondary ring-offset-2">
                                <img src="{{ Storage::url($siswa->foto) }}" alt="{{ $siswa->nama }}" />
                            </div>
                        </div>
                    @else
                        <div class="avatar placeholder mb-2">
                            <div class="bg-secondary text-secondary-content rounded-full w-20">
                                <span class="text-2xl">{{ strtoupper(substr($siswa->nama, 0, 2)) }}</span>
                            </div>
                        </div>
                    @endif
                    <form method="POST" action="{{ route('admin.siswa.upload-photo', $siswa) }}" enctype="multipart/form-data">
                        @csrf
                        <input type="file" name="foto" accept="image/jpeg,image/png"
                               class="file-input file-input-bordered file-input-sm w-full mb-2" required />
                        <button type="submit" class="btn btn-outline btn-sm w-full">Upload</button>
                    </form>
                </div>
            </div>

            <!-- Reset Password -->
            <div class="card bg-base-100 shadow">
                <div class="card-body">
                    <h2 class="card-title text-base">Reset Password</h2>
                    <p class="text-sm text-base-content/60">Password akan direset ke NIS siswa.</p>
                    <form method="POST" action="{{ route('admin.siswa.reset-password', $siswa) }}"
                          onsubmit="return confirm('Reset password ke NIS?')">
                        @csrf
                        <button type="submit" class="btn btn-warning btn-sm w-full mt-2">Reset Password</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
