<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-bold text-base-content">Edit Soal</h1>
            <a href="{{ route('guru.soal.index') }}" class="btn btn-ghost btn-sm">← Kembali</a>
        </div>
    </x-slot>

    <div class="card bg-base-100 shadow max-w-4xl">
        <div class="card-body">
            <form action="{{ route('guru.soal.update', $soal) }}" method="POST" class="space-y-4">
                @csrf
                @method('PATCH')

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
                            <option value="{{ $kategori->id }}" @selected(old('kategori_ujian_id', $soal->kategori_ujian_id) == $kategori->id)>
                                {{ $kategori->nama }}
                            </option>
                        @endforeach
                    </select>
                    @error('kategori_ujian_id')
                        <label class="label">
                            <span class="label-text-alt text-error">{{ $message }}</span>
                        </label>
                    @enderror
                </div>

                <!-- Pertanyaan -->
                <div class="form-control">
                    <label class="label">
                        <span class="label-text font-semibold">Pertanyaan Soal <span class="text-error">*</span></span>
                    </label>
                    <textarea 
                        name="pertanyaan" 
                        placeholder="Masukkan pertanyaan soal..."
                        class="textarea textarea-bordered h-32 @error('pertanyaan') textarea-error @enderror"
                        required
                    >{{ old('pertanyaan', $soal->pertanyaan) }}</textarea>
                    @error('pertanyaan')
                        <label class="label">
                            <span class="label-text-alt text-error">{{ $message }}</span>
                        </label>
                    @enderror
                </div>

                <!-- Tipe Soal -->
                <div class="form-control">
                    <label class="label">
                        <span class="label-text font-semibold">Tipe Soal <span class="text-error">*</span></span>
                    </label>
                    <div class="space-y-2">
                        <label class="label cursor-pointer">
                            <span class="label-text">Pilihan Ganda</span>
                            <input 
                                type="radio" 
                                name="tipe_soal" 
                                value="pilihan_ganda" 
                                class="radio radio-primary"
                                @checked(old('tipe_soal', $soal->tipe_soal) === 'pilihan_ganda')
                                onchange="updateSoalType(this.value)"
                            />
                        </label>
                        <label class="label cursor-pointer">
                            <span class="label-text">Essay</span>
                            <input 
                                type="radio" 
                                name="tipe_soal" 
                                value="essay" 
                                class="radio radio-primary"
                                @checked(old('tipe_soal', $soal->tipe_soal) === 'essay')
                                onchange="updateSoalType(this.value)"
                            />
                        </label>
                    </div>
                    @error('tipe_soal')
                        <label class="label">
                            <span class="label-text-alt text-error">{{ $message }}</span>
                        </label>
                    @enderror
                </div>

                <!-- Container untuk Pilihan Ganda -->
                <div id="pg-container" class="space-y-4" @if(old('tipe_soal', $soal->tipe_soal) === 'essay') style="display: none;" @endif>
                    <p class="font-semibold text-base-content/70">Opsi Jawaban</p>
                    
                    @foreach(['a', 'b', 'c', 'd'] as $opsi)
                        <div class="form-control">
                            <label class="label">
                                <span class="label-text">Opsi {{ strtoupper($opsi) }} <span class="text-error">*</span></span>
                            </label>
                            <input 
                                type="text" 
                                name="opsi_{{ $opsi }}" 
                                placeholder="Masukkan opsi {{ strtoupper($opsi) }}"
                                value="{{ old('opsi_' . $opsi, $soal->{'opsi_' . $opsi}) }}"
                                class="input input-bordered @error('opsi_' . $opsi) input-error @enderror"
                            />
                            @error('opsi_' . $opsi)
                                <label class="label">
                                    <span class="label-text-alt text-error">{{ $message }}</span>
                                </label>
                            @enderror
                        </div>
                    @endforeach

                    <div class="form-control">
                        <label class="label">
                            <span class="label-text font-semibold">Kunci Jawaban <span class="text-error">*</span></span>
                        </label>
                        <select 
                            name="kunci_jawaban" 
                            class="select select-bordered @error('kunci_jawaban') select-error @enderror"
                        >
                            <option value="">Pilih Kunci Jawaban</option>
                            @foreach(['a' => 'A', 'b' => 'B', 'c' => 'C', 'd' => 'D'] as $value => $label)
                                <option value="{{ $value }}" @selected(old('kunci_jawaban', $soal->kunci_jawaban) === $value)>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                        @error('kunci_jawaban')
                            <label class="label">
                                <span class="label-text-alt text-error">{{ $message }}</span>
                            </label>
                        @enderror
                    </div>
                </div>

                <!-- Container untuk Essay -->
                <div id="essay-container" @if(old('tipe_soal', $soal->tipe_soal) !== 'essay') style="display: none;" @endif>
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text font-semibold">Kunci Jawaban / Pedoman Jawaban <span class="text-error">*</span></span>
                        </label>
                        <textarea 
                            name="kunci_jawaban" 
                            placeholder="Masukkan pedoman atau contoh jawaban yang diharapkan..."
                            class="textarea textarea-bordered h-24 @error('kunci_jawaban') textarea-error @enderror"
                        >{{ old('tipe_soal', $soal->tipe_soal) === 'essay' ? old('kunci_jawaban', $soal->kunci_jawaban) : '' }}</textarea>
                        @error('kunci_jawaban')
                            <label class="label">
                                <span class="label-text-alt text-error">{{ $message }}</span>
                            </label>
                        @enderror
                    </div>
                </div>

                <!-- Bobot -->
                <div class="form-control">
                    <label class="label">
                        <span class="label-text font-semibold">Bobot Soal (0.1 - 100) <span class="text-error">*</span></span>
                    </label>
                    <input 
                        type="number" 
                        name="bobot" 
                        value="{{ old('bobot', $soal->bobot) }}"
                        placeholder="Contoh: 1, 2, 5"
                        step="0.1"
                        min="0.1"
                        max="100"
                        class="input input-bordered @error('bobot') input-error @enderror"
                        required
                    />
                    @error('bobot')
                        <label class="label">
                            <span class="label-text-alt text-error">{{ $message }}</span>
                        </label>
                    @enderror
                </div>

                <!-- Status Aktif -->
                <div class="form-control">
                    <label class="label cursor-pointer">
                        <span class="label-text font-semibold">Aktifkan Soal</span>
                        <input 
                            type="checkbox" 
                            name="is_active" 
                            value="1"
                            class="checkbox checkbox-primary"
                            @checked(old('is_active', $soal->is_active))
                        />
                    </label>
                </div>

                <!-- Action Buttons -->
                <div class="flex gap-2 mt-6">
                    <button type="submit" class="btn btn-primary">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                        Update Soal
                    </button>
                    <a href="{{ route('guru.soal.index') }}" class="btn btn-ghost">Batal</a>
                </div>
            </form>
        </div>
    </div>

    <script>
        function updateSoalType(type) {
            const pgContainer = document.getElementById('pg-container');
            const essayContainer = document.getElementById('essay-container');
            
            if (type === 'pilihan_ganda') {
                pgContainer.style.display = 'block';
                essayContainer.style.display = 'none';
            } else {
                pgContainer.style.display = 'none';
                essayContainer.style.display = 'block';
            }
        }
    </script>
</x-app-layout>
