<x-app-layout>
    <x-slot name="header">
        <h1 class="text-2xl font-bold text-base-content">Upload Logo</h1>
    </x-slot>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Upload Form -->
        <div class="lg:col-span-2">
            <form action="{{ route('admin.logo.update', $tenant->id) }}" method="POST" enctype="multipart/form-data"
                class="card bg-base-100 shadow-md">
                @csrf
                @method('PUT')

                <div class="card-body space-y-6">
                    <!-- Tenant Info -->
                    <div>
                        <p class="text-sm text-base-600">Tenant</p>
                        <p class="font-semibold text-lg">{{ $tenant->name }}</p>
                    </div>

                    <!-- File Input -->
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text font-semibold">Pilih File Logo</span>
                            <span class="label-text-alt text-error">*</span>
                        </label>
                        <div class="border-2 border-dashed border-base-300 rounded-lg p-6 text-center hover:border-primary transition"
                            id="dropZone">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                stroke-width="1.5" stroke="currentColor"
                                class="w-12 h-12 mx-auto text-base-400 mb-3">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M12 16.5V9.75m0 0l3 3m-3-3l-3 3M6.75 19.5a4.5 4.5 0 01-1.41-8.775 5.25 5.25 0 0110.233-2.33A3 3 0 0116.5 19.5H6.75z" />
                            </svg>
                            <p class="text-base-600 mb-2">Drag & drop logo di sini atau klik untuk memilih</p>
                            <input type="file" name="logo" id="logoFile" class="hidden" accept="image/*"
                                required>
                            <button type="button" class="btn btn-sm btn-outline" onclick="document.getElementById('logoFile').click()">
                                Pilih File
                            </button>
                            <p class="text-xs text-base-500 mt-2">JPG, PNG, SVG (Max 1MB)</p>
                        </div>
                        @error('logo')
                            <label class="label">
                                <span class="label-text-alt text-error">{{ $message }}</span>
                            </label>
                        @enderror
                    </div>

                    <!-- Preview -->
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text font-semibold">Preview</span>
                        </label>
                        <div class="border border-base-300 rounded-lg p-4 bg-base-50 text-center min-h-[200px] flex items-center justify-center"
                            id="preview">
                            <p class="text-base-500">Preview akan muncul di sini</p>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="flex gap-3 justify-end pt-4 border-t border-base-200">
                        <a href="{{ route('admin.logo.index') }}" class="btn btn-ghost">
                            Batal
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M12 16.5V9.75m0 0l3 3m-3-3l-3 3M6.75 19.5a4.5 4.5 0 01-1.41-8.775 5.25 5.25 0 0110.233-2.33A3 3 0 0116.5 19.5H6.75z" />
                            </svg>
                            Upload Logo
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Info & History -->
        <div class="space-y-6">
            <!-- Info Box -->
            <div class="card bg-base-100 border border-base-200 shadow-sm">
                <div class="card-body">
                    <h3 class="card-title text-lg">Informasi</h3>
                    <div class="space-y-2 text-sm">
                        <p><strong>Format:</strong> JPG, PNG, SVG</p>
                        <p><strong>Ukuran Max:</strong> 1MB</p>
                        <p><strong>Resolusi Rekomendasi:</strong> 300x150px</p>
                        <p class="text-base-600 pt-2 border-t border-base-200">
                            Logo akan digunakan di navbar dan berbagai tempat di aplikasi. Pastikan logo memiliki
                            kontras yang baik.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Logo History -->
            @if ($currentLogos->count() > 0)
                <div class="card bg-base-100 border border-base-200 shadow-sm">
                    <div class="card-body">
                        <h3 class="card-title text-base">Riwayat Logo</h3>
                        <div class="space-y-3">
                            @foreach ($currentLogos as $logo)
                                <div class="border border-base-300 rounded p-2 bg-base-50">
                                    <div class="flex items-start justify-between">
                                        <div class="flex-1">
                                            <p class="text-xs font-semibold text-base-900">{{ $logo->nama_file }}</p>
                                            <p class="text-xs text-base-600">
                                                {{ $logo->uploaded_at->format('d M Y H:i') }}
                                            </p>
                                            <p class="text-xs text-base-500 mt-1">
                                                Size: {{ \App\Helpers\LogoHelper::formatFileSize($logo->size) }}
                                            </p>
                                        </div>
                                    </div>

                                    @if ($logo === $currentLogos->first())
                                        <span class="badge badge-success mt-2 text-xs">Aktif</span>
                                    @else
                                        <form
                                            action="{{ route('admin.logo.restore', $logo->id) }}"
                                            method="POST" class="mt-2">
                                            @csrf
                                            <button type="submit"
                                                class="btn btn-xs btn-ghost"
                                                onclick="return confirm('Restore logo ini?')">
                                                Aktifkan
                                            </button>
                                        </form>
                                    @endif

                                    <form action="{{ route('admin.logo.destroy', $logo->id) }}"
                                        method="POST" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-xs btn-ghost text-error mt-2"
                                            onclick="return confirm('Hapus logo ini?')">
                                            Hapus
                                        </button>
                                    </form>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <script>
        // Drag & Drop
        const dropZone = document.getElementById('dropZone');
        const logoFile = document.getElementById('logoFile');
        const preview = document.getElementById('preview');

        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
            dropZone.addEventListener(eventName, preventDefaults, false);
        });

        function preventDefaults(e) {
            e.preventDefault();
            e.stopPropagation();
        }

        ['dragenter', 'dragover'].forEach(eventName => {
            dropZone.addEventListener(eventName, highlight, false);
        });

        ['dragleave', 'drop'].forEach(eventName => {
            dropZone.addEventListener(eventName, unhighlight, false);
        });

        function highlight(e) {
            dropZone.classList.add('border-primary', 'bg-primary', 'bg-opacity-5');
        }

        function unhighlight(e) {
            dropZone.classList.remove('border-primary', 'bg-primary', 'bg-opacity-5');
        }

        dropZone.addEventListener('drop', handleDrop, false);

        function handleDrop(e) {
            const dt = e.dataTransfer;
            const files = dt.files;
            logoFile.files = files;
            updatePreview();
        }

        // File input change
        logoFile.addEventListener('change', updatePreview);

        function updatePreview() {
            const file = logoFile.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = (e) => {
                    preview.innerHTML = `<img src="${e.target.result}" alt="preview" class="max-h-48 max-w-full mx-auto">`;
                };
                reader.readAsDataURL(file);
            }
        }
    </script>
</x-app-layout>
