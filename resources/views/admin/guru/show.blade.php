<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.guru.index') }}" class="btn btn-ghost btn-sm">← Kembali</a>
            <h1 class="text-2xl font-bold text-base-content">Detail Guru</h1>
        </div>
    </x-slot>

    @if(session('success'))
        <div class="alert alert-success mb-4"><span>{{ session('success') }}</span></div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 max-w-4xl">
        <!-- Foto -->
        <div class="flex flex-col gap-4">
            <div class="card bg-base-100 shadow items-center text-center">
                <div class="card-body">
                    @if($guru->foto_profil)
                        <div class="avatar">
                            <div class="w-28 rounded-full ring ring-primary ring-offset-2">
                                <img src="{{ Storage::url($guru->foto_profil) }}" alt="{{ $guru->nama }}" />
                            </div>
                        </div>
                    @else
                        <div class="avatar placeholder">
                            <div class="bg-neutral text-neutral-content rounded-full w-28">
                                <span class="text-3xl">{{ strtoupper(substr($guru->nama, 0, 2)) }}</span>
                            </div>
                        </div>
                    @endif
                    <p class="font-bold mt-2">{{ $guru->nama }}</p>
                    @if($guru->is_wali_kelas)
                        <span class="badge badge-info">Wali Kelas</span>
                    @endif
                </div>
            </div>

            <div class="card bg-base-100 shadow">
                <div class="card-body">
                    <div class="flex gap-2">
                        <a href="{{ route('admin.guru.edit', $guru) }}" class="btn btn-primary btn-sm flex-1">Edit</a>
                        <form method="POST" action="{{ route('admin.guru.destroy', $guru) }}"
                              onsubmit="return confirm('Hapus guru {{ $guru->nama }}?')" class="flex-1">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-error btn-sm w-full">Hapus</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Detail Info -->
        <div class="lg:col-span-2">
            <div class="card bg-base-100 shadow">
                <div class="card-body">
                    <h2 class="card-title">Informasi Guru</h2>
                    <div class="divider my-1"></div>

                    <dl class="grid grid-cols-2 gap-4">
                        <div>
                            <dt class="text-sm text-base-content/50">Nama Lengkap</dt>
                            <dd class="font-medium">{{ $guru->nama }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm text-base-content/50">Email</dt>
                            <dd>{{ $guru->email }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm text-base-content/50">NIP</dt>
                            <dd>{{ $guru->nip ?? '-' }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm text-base-content/50">Status</dt>
                            <dd>
                                @if($guru->is_active)
                                    <span class="badge badge-success badge-sm">Aktif</span>
                                @else
                                    <span class="badge badge-error badge-sm">Nonaktif</span>
                                @endif
                            </dd>
                        </div>
                        <div>
                            <dt class="text-sm text-base-content/50">Wali Kelas</dt>
                            <dd>{{ $guru->is_wali_kelas ? 'Ya' : 'Tidak' }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm text-base-content/50">Google ID</dt>
                            <dd>{{ $guru->google_id ? 'Terhubung' : 'Belum terhubung' }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm text-base-content/50">Terdaftar</dt>
                            <dd>{{ $guru->created_at->format('d M Y') }}</dd>
                        </div>
                    </dl>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
