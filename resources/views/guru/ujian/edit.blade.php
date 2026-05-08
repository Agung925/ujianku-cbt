<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('guru.ujian.show', $ujian->id) }}" class="btn btn-ghost btn-sm">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" /></svg>
            </a>
            <h1 class="text-2xl font-bold text-base-content">Edit Ujian</h1>
        </div>
    </x-slot>

    <div class="card bg-base-100 shadow max-w-3xl">
        <div class="card-body">
            <form action="{{ route('guru.ujian.update', $ujian->id) }}" method="POST" class="space-y-4">
                @csrf
                @method('PATCH')

                <!-- Judul -->
                <div class="form-control">
                    <label class="label">
                        <span class="label-text font-semibold">Judul Ujian <span class="text-error">*</span></span>
                    </label>
                    <input
                        type="text"
                        name="judul"
                        value="{{ old('judul', $ujian->judul) }}"
                        placeholder="Contoh: Ulangan Harian Matematika Bab 3"
                        class="input input-bordered @error('judul') input-error @enderror"
                        required
                    />
                    @error('judul')
                        <label class="label"><span class="label-text-alt text-error">{{ $message }}</span></label>
                    @enderror
                </div>

                <!-- Deskripsi -->
                <div class="form-control">
                    <label class="label">
                        <span class="label-text font-semibold">Deskripsi</span>
                        <span class="label-text-alt">Opsional</span>
                    </label>
                    <textarea
                        name="deskripsi"
                        placeholder="Deskripsi atau petunjuk ujian..."
                        class="textarea textarea-bordered h-24 @error('deskripsi') textarea-error @enderror"
                    >{{ old('deskripsi', $ujian->deskripsi) }}</textarea>
                    @error('deskripsi')
                        <label class="label"><span class="label-text-alt text-error">{{ $message }}</span></label>
                    @enderror
                </div>

                <!-- Kategori Ujian -->
                <div class="form-control">
                    <label class="label">
                        <span class="label-text font-semibold">Kategori Ujian <span class="text-error">*</span></span>
                    </label>
                    <select
                        name="kategori_ujian_id"
                        class="select select-bordered @error('kategori_ujian_id') select-error @enderror"
                        required
                    >
                        <option value="">Pilih Kategori</option>
                        @foreach ($kategoriUjians as $kategori)
                            <option value="{{ $kategori->id }}" @selected(old('kategori_ujian_id', $ujian->kategori_ujian_id) == $kategori->id)>
                                {{ $kategori->nama }}
                            </option>
                        @endforeach
                    </select>
                    @error('kategori_ujian_id')
                        <label class="label"><span class="label-text-alt text-error">{{ $message }}</span></label>
                    @enderror
                </div>

                <!-- Tanggal Mulai & Selesai -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text font-semibold">Tanggal & Waktu Mulai <span class="text-error">*</span></span>
                        </label>
                        <input
                            type="datetime-local"
                            name="tgl_mulai"
                            value="{{ old('tgl_mulai', $ujian->tgl_mulai->format('Y-m-d\TH:i')) }}"
                            class="input input-bordered @error('tgl_mulai') input-error @enderror"
                            required
                        />
                        @error('tgl_mulai')
                            <label class="label"><span class="label-text-alt text-error">{{ $message }}</span></label>
                        @enderror
                    </div>

                    <div class="form-control">
                        <label class="label">
                            <span class="label-text font-semibold">Tanggal & Waktu Selesai <span class="text-error">*</span></span>
                        </label>
                        <input
                            type="datetime-local"
                            name="tgl_selesai"
                            value="{{ old('tgl_selesai', $ujian->tgl_selesai->format('Y-m-d\TH:i')) }}"
                            class="input input-bordered @error('tgl_selesai') input-error @enderror"
                            required
                        />
                        @error('tgl_selesai')
                            <label class="label"><span class="label-text-alt text-error">{{ $message }}</span></label>
                        @enderror
                    </div>
                </div>

                <!-- Durasi -->
                <div class="form-control">
                    <label class="label">
                        <span class="label-text font-semibold">Durasi Pengerjaan (menit) <span class="text-error">*</span></span>
                    </label>
                    <input
                        type="number"
                        name="waktu_durasi"
                        value="{{ old('waktu_durasi', $ujian->waktu_durasi) }}"
                        min="1"
                        max="480"
                        class="input input-bordered w-40 @error('waktu_durasi') input-error @enderror"
                        required
                    />
                    @error('waktu_durasi')
                        <label class="label"><span class="label-text-alt text-error">{{ $message }}</span></label>
                    @enderror
                </div>

                <!-- Pengaturan Acak -->
                <div class="form-control">
                    <label class="label">
                        <span class="label-text font-semibold">Pengaturan Soal</span>
                    </label>
                    <div class="flex flex-col gap-2">
                        <label class="cursor-pointer flex items-center gap-3">
                            <input
                                type="checkbox"
                                name="is_acak_soal"
                                value="1"
                                class="checkbox checkbox-primary"
                                @checked(old('is_acak_soal', $ujian->is_acak_soal))
                            />
                            <span class="label-text">Acak urutan soal untuk setiap siswa</span>
                        </label>
                        <label class="cursor-pointer flex items-center gap-3">
                            <input
                                type="checkbox"
                                name="is_acak_opsi"
                                value="1"
                                class="checkbox checkbox-primary"
                                @checked(old('is_acak_opsi', $ujian->is_acak_opsi))
                            />
                            <span class="label-text">Acak urutan pilihan jawaban (a, b, c, d)</span>
                        </label>
                    </div>
                </div>

                <!-- Actions -->
                <div class="flex gap-3 pt-2">
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                    <a href="{{ route('guru.ujian.show', $ujian->id) }}" class="btn btn-ghost">Batal</a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
