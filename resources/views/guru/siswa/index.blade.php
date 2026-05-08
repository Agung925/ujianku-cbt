<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-bold text-base-content">Manajemen Siswa</h1>
            @if($guru->is_wali_kelas)
                <a href="{{ route('guru.siswa.create') }}" class="btn btn-primary btn-sm">+ Tambah Siswa</a>
            @endif
        </div>
    </x-slot>

    @if(session('success'))
        <div class="alert alert-success mb-4"><span>{{ session('success') }}</span></div>
    @endif

    @unless($guru->is_wali_kelas)
        <div class="alert alert-info mb-4">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="stroke-current shrink-0 w-6 h-6"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            <span>Hanya wali kelas yang dapat menambah siswa.</span>
        </div>
    @endunless

    <div class="card bg-base-100 shadow">
        <div class="card-body p-0">
            <div class="overflow-x-auto">
                <table class="table table-zebra w-full">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Foto</th>
                            <th>NIS</th>
                            <th>Nama</th>
                            <th>Kelas</th>
                            <th>Status</th>
                            @if($guru->is_wali_kelas)
                                <th>Aksi</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($siswas as $siswa)
                            <tr>
                                <td>{{ $loop->iteration + ($siswas->currentPage() - 1) * $siswas->perPage() }}</td>
                                <td>
                                    @if($siswa->foto)
                                        <div class="avatar">
                                            <div class="w-9 rounded-full">
                                                <img src="{{ Storage::url($siswa->foto) }}" alt="{{ $siswa->nama }}" />
                                            </div>
                                        </div>
                                    @else
                                        <div class="avatar placeholder">
                                            <div class="bg-secondary text-secondary-content rounded-full w-9">
                                                <span class="text-xs">{{ strtoupper(substr($siswa->nama, 0, 2)) }}</span>
                                            </div>
                                        </div>
                                    @endif
                                </td>
                                <td class="font-mono text-sm">{{ $siswa->nis }}</td>
                                <td>{{ $siswa->nama }}</td>
                                <td>{{ $siswa->kelas }}</td>
                                <td>
                                    @if($siswa->is_active)
                                        <span class="badge badge-success badge-sm">Aktif</span>
                                    @else
                                        <span class="badge badge-error badge-sm">Nonaktif</span>
                                    @endif
                                </td>
                                @if($guru->is_wali_kelas)
                                    <td>
                                        <!-- Upload foto siswa -->
                                        <label for="foto-modal-{{ $siswa->id }}" class="btn btn-ghost btn-xs">Upload Foto</label>
                                        <input type="checkbox" id="foto-modal-{{ $siswa->id }}" class="modal-toggle" />
                                        <div class="modal" role="dialog">
                                            <div class="modal-box">
                                                <h3 class="font-bold text-lg mb-3">Upload Foto — {{ $siswa->nama }}</h3>
                                                <form method="POST" action="{{ route('guru.siswa.upload-photo', $siswa) }}" enctype="multipart/form-data">
                                                    @csrf
                                                    <input type="file" name="foto" accept="image/jpeg,image/png"
                                                           class="file-input file-input-bordered w-full mb-4" required />
                                                    <div class="modal-action">
                                                        <button type="submit" class="btn btn-primary btn-sm">Upload</button>
                                                        <label for="foto-modal-{{ $siswa->id }}" class="btn btn-ghost btn-sm">Batal</label>
                                                    </div>
                                                </form>
                                            </div>
                                            <label class="modal-backdrop" for="foto-modal-{{ $siswa->id }}">Close</label>
                                        </div>
                                    </td>
                                @endif
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ $guru->is_wali_kelas ? 7 : 6 }}" class="text-center text-base-content/40 py-8">
                                    Belum ada data siswa.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($siswas->hasPages())
                <div class="p-4">{{ $siswas->links() }}</div>
            @endif
        </div>
    </div>
</x-app-layout>
