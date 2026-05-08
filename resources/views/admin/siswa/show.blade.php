<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.siswa.index') }}" class="btn btn-ghost btn-sm">← Kembali</a>
            <h1 class="text-2xl font-bold text-base-content">Detail Siswa</h1>
        </div>
    </x-slot>

    @if(session('success'))
        <div class="alert alert-success mb-4"><span>{{ session('success') }}</span></div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 max-w-4xl">
        <!-- Foto + Actions -->
        <div class="flex flex-col gap-4">
            <div class="card bg-base-100 shadow items-center text-center">
                <div class="card-body">
                    @if($siswa->foto)
                        <div class="avatar">
                            <div class="w-28 rounded-full ring ring-secondary ring-offset-2">
                                <img src="{{ Storage::url($siswa->foto) }}" alt="{{ $siswa->nama }}" />
                            </div>
                        </div>
                    @else
                        <div class="avatar placeholder">
                            <div class="bg-secondary text-secondary-content rounded-full w-28">
                                <span class="text-3xl">{{ strtoupper(substr($siswa->nama, 0, 2)) }}</span>
                            </div>
                        </div>
                    @endif
                    <p class="font-bold mt-2">{{ $siswa->nama }}</p>
                    <p class="font-mono text-sm text-base-content/60">{{ $siswa->nis }}</p>
                    @if($siswa->is_active)
                        <span class="badge badge-success">Aktif</span>
                    @else
                        <span class="badge badge-error">Nonaktif</span>
                    @endif
                </div>
            </div>

            <div class="card bg-base-100 shadow">
                <div class="card-body gap-2">
                    <a href="{{ route('admin.siswa.edit', $siswa) }}" class="btn btn-primary btn-sm">Edit Data</a>
                    @if($siswa->is_active)
                        <form method="POST" action="{{ route('admin.siswa.deactivate', $siswa) }}">
                            @csrf
                            <button type="submit" class="btn btn-warning btn-sm w-full">Nonaktifkan</button>
                        </form>
                    @else
                        <form method="POST" action="{{ route('admin.siswa.activate', $siswa) }}">
                            @csrf
                            <button type="submit" class="btn btn-success btn-sm w-full">Aktifkan</button>
                        </form>
                    @endif
                    <form method="POST" action="{{ route('admin.siswa.destroy', $siswa) }}"
                          onsubmit="return confirm('Hapus siswa {{ $siswa->nama }}?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-error btn-sm w-full">Hapus</button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Detail -->
        <div class="lg:col-span-2">
            <div class="card bg-base-100 shadow">
                <div class="card-body">
                    <h2 class="card-title">Informasi Siswa</h2>
                    <div class="divider my-1"></div>
                    <dl class="grid grid-cols-2 gap-4">
                        <div>
                            <dt class="text-sm text-base-content/50">NIS</dt>
                            <dd class="font-mono font-medium">{{ $siswa->nis }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm text-base-content/50">Nama Lengkap</dt>
                            <dd class="font-medium">{{ $siswa->nama }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm text-base-content/50">Kelas</dt>
                            <dd>{{ $siswa->kelas }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm text-base-content/50">Email</dt>
                            <dd>{{ $siswa->email ?? '-' }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm text-base-content/50">Status</dt>
                            <dd>
                                @if($siswa->is_active)
                                    <span class="badge badge-success badge-sm">Aktif</span>
                                @else
                                    <span class="badge badge-error badge-sm">Nonaktif</span>
                                @endif
                            </dd>
                        </div>
                        <div>
                            <dt class="text-sm text-base-content/50">Terdaftar</dt>
                            <dd>{{ $siswa->created_at->format('d M Y') }}</dd>
                        </div>
                    </dl>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
