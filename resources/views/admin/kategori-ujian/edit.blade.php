<x-app-layout>
    <x-slot name="header">
        <h1 class="text-2xl font-bold text-base-content">Edit Kategori Ujian</h1>
    </x-slot>

    <div class="card bg-base-100 shadow max-w-2xl">
        <div class="card-body">
            <form action="{{ route('admin.kategori-ujian.update', $kategoriUjian) }}" method="POST" class="space-y-4">
                @csrf
                @method('PATCH')

                <!-- Nama Kategori -->
                <div class="form-control">
                    <label class="label">
                        <span class="label-text font-semibold">Nama Kategori Ujian <span class="text-error">*</span></span>
                    </label>
                    <input 
                        type="text" 
                        name="nama" 
                        value="{{ old('nama', $kategoriUjian->nama) }}"
                        placeholder="Contoh: ASTS Semester 1"
                        class="input input-bordered @error('nama') input-error @enderror"
                        required
                    />
                    @error('nama')
                        <label class="label">
                            <span class="label-text-alt text-error">{{ $message }}</span>
                        </label>
                    @enderror
                </div>

                <!-- Deskripsi -->
                <div class="form-control">
                    <label class="label">
                        <span class="label-text font-semibold">Deskripsi (Opsional)</span>
                    </label>
                    <textarea 
                        name="deskripsi" 
                        placeholder="Masukkan deskripsi singkat untuk kategori ini"
                        class="textarea textarea-bordered h-24 @error('deskripsi') textarea-error @enderror"
                    >{{ old('deskripsi', $kategoriUjian->deskripsi) }}</textarea>
                    @error('deskripsi')
                        <label class="label">
                            <span class="label-text-alt text-error">{{ $message }}</span>
                        </label>
                    @enderror
                </div>

                <!-- Urutan -->
                <div class="form-control">
                    <label class="label">
                        <span class="label-text font-semibold">Urutan Tampilan (Opsional)</span>
                    </label>
                    <input 
                        type="number" 
                        name="urutan" 
                        value="{{ old('urutan', $kategoriUjian->urutan) }}"
                        placeholder="1, 2, 3, dst"
                        class="input input-bordered @error('urutan') input-error @enderror"
                        min="1"
                    />
                    @error('urutan')
                        <label class="label">
                            <span class="label-text-alt text-error">{{ $message }}</span>
                        </label>
                    @enderror
                </div>

                <!-- Status Aktif -->
                <div class="form-control">
                    <label class="label cursor-pointer">
                        <span class="label-text font-semibold">Aktifkan Kategori</span>
                        <input 
                            type="checkbox" 
                            name="is_active" 
                            value="1"
                            class="checkbox checkbox-primary"
                            @checked(old('is_active', $kategoriUjian->is_active))
                        />
                    </label>
                </div>

                <!-- Action Buttons -->
                <div class="flex gap-2 mt-6">
                    <button type="submit" class="btn btn-primary">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                        Update Kategori
                    </button>
                    <a href="{{ route('admin.kategori-ujian.index') }}" class="btn btn-ghost">Batal</a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
