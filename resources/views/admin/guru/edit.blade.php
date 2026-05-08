<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.guru.index') }}" class="btn btn-ghost btn-sm">← Kembali</a>
            <h1 class="text-2xl font-bold text-base-content">Edit Guru — {{ $guru->nama }}</h1>
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
                    <h2 class="card-title text-lg mb-4">Informasi Guru</h2>
                    <form method="POST" action="{{ route('admin.guru.update', $guru) }}">
                        @csrf
                        @method('PUT')

                        <div class="form-control mb-4">
                            <label class="label"><span class="label-text font-medium">Nama Lengkap <span class="text-error">*</span></span></label>
                            <input type="text" name="nama" value="{{ old('nama', $guru->nama) }}"
                                   class="input input-bordered @error('nama') input-error @enderror" required />
                            @error('nama')
                                <label class="label"><span class="label-text-alt text-error">{{ $message }}</span></label>
                            @enderror
                        </div>

                        <div class="form-control mb-4">
                            <label class="label"><span class="label-text font-medium">Email <span class="text-error">*</span></span></label>
                            <input type="email" name="email" value="{{ old('email', $guru->email) }}"
                                   class="input input-bordered @error('email') input-error @enderror" required />
                            @error('email')
                                <label class="label"><span class="label-text-alt text-error">{{ $message }}</span></label>
                            @enderror
                        </div>

                        <div class="form-control mb-4">
                            <label class="label"><span class="label-text font-medium">NIP</span></label>
                            <input type="text" name="nip" value="{{ old('nip', $guru->nip) }}"
                                   class="input input-bordered @error('nip') input-error @enderror" />
                            @error('nip')
                                <label class="label"><span class="label-text-alt text-error">{{ $message }}</span></label>
                            @enderror
                        </div>

                        <div class="flex gap-6 mb-6">
                            <div class="form-control">
                                <label class="label cursor-pointer gap-3">
                                    <span class="label-text">Wali Kelas</span>
                                    <input type="hidden" name="is_wali_kelas" value="0" />
                                    <input type="checkbox" name="is_wali_kelas" value="1"
                                           class="toggle toggle-info" {{ old('is_wali_kelas', $guru->is_wali_kelas) ? 'checked' : '' }} />
                                </label>
                            </div>
                            <div class="form-control">
                                <label class="label cursor-pointer gap-3">
                                    <span class="label-text">Aktif</span>
                                    <input type="hidden" name="is_active" value="0" />
                                    <input type="checkbox" name="is_active" value="1"
                                           class="toggle toggle-success" {{ old('is_active', $guru->is_active) ? 'checked' : '' }} />
                                </label>
                            </div>
                        </div>

                        <div class="card-actions">
                            <button type="submit" class="btn btn-primary">Perbarui Data</button>
                            <a href="{{ route('admin.guru.index') }}" class="btn btn-ghost">Batal</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Foto Profil -->
        <div>
            <div class="card bg-base-100 shadow">
                <div class="card-body items-center text-center">
                    <h2 class="card-title text-lg mb-2">Foto Profil</h2>

                    @if($guru->foto_profil)
                        <div class="avatar mb-3">
                            <div class="w-24 rounded-full ring ring-primary ring-offset-2">
                                <img src="{{ Storage::url($guru->foto_profil) }}" alt="{{ $guru->nama }}" />
                            </div>
                        </div>
                    @else
                        <div class="avatar placeholder mb-3">
                            <div class="bg-neutral text-neutral-content rounded-full w-24">
                                <span class="text-2xl">{{ strtoupper(substr($guru->nama, 0, 2)) }}</span>
                            </div>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('admin.guru.upload-photo', $guru) }}" enctype="multipart/form-data">
                        @csrf
                        <div class="form-control mb-3">
                            <input type="file" name="foto" accept="image/jpeg,image/png"
                                   class="file-input file-input-bordered file-input-sm w-full" required />
                        </div>
                        <button type="submit" class="btn btn-outline btn-sm w-full">Upload Foto</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
