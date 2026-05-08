<x-app-layout>
    <x-slot name="header">
        <h1 class="text-2xl font-bold text-base-content">Detail Logo - {{ $tenant->name }}</h1>
    </x-slot>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Current Logo -->
        <div class="lg:col-span-2">
            <div class="card bg-base-100 shadow-md">
                <div class="card-body">
                    <h2 class="card-title mb-4">Logo Saat Ini</h2>

                    @if ($currentLogo)
                        <div class="border border-base-300 rounded-lg p-8 bg-base-50 text-center">
                            <img src="{{ $logoUrl }}" alt="Current Logo"
                                class="max-h-64 max-w-full mx-auto object-contain">
                        </div>

                        <div class="grid grid-cols-2 gap-4 mt-6 pt-6 border-t border-base-200">
                            <div>
                                <p class="text-sm text-base-600">Nama File</p>
                                <p class="font-semibold">{{ $currentLogo->nama_file }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-base-600">Tipe File</p>
                                <p class="font-semibold">{{ strtoupper($currentLogo->file_type) }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-base-600">Ukuran</p>
                                <p class="font-semibold">{{ \App\Helpers\LogoHelper::formatFileSize($currentLogo->size) }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-base-600">MIME Type</p>
                                <p class="font-semibold">{{ $currentLogo->mime_type }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-base-600">Diupload Oleh</p>
                                <p class="font-semibold">{{ $currentLogo->uploadedBy?->name ?? 'Admin' }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-base-600">Tanggal Upload</p>
                                <p class="font-semibold">{{ $currentLogo->uploaded_at->format('d M Y H:i') }}</p>
                            </div>
                        </div>

                        <div class="flex gap-3 mt-6 pt-6 border-t border-base-200">
                            <a href="{{ route('admin.logo.edit', $tenant->id) }}" class="btn btn-primary flex-1">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M12 16.5V9.75m0 0l3 3m-3-3l-3 3M6.75 19.5a4.5 4.5 0 01-1.41-8.775 5.25 5.25 0 0110.233-2.33A3 3 0 0116.5 19.5H6.75z" />
                                </svg>
                                Upload Logo Baru
                            </a>
                            <form action="{{ route('admin.logo.destroy', $currentLogo->id) }}" method="POST"
                                class="flex-1">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-error w-full"
                                    onclick="return confirm('Hapus logo ini?')">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 2.991a48.114 48.114 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                                    </svg>
                                    Hapus
                                </button>
                            </form>
                        </div>
                    @else
                        <div class="alert alert-warning shadow-lg">
                            <div>
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    class="stroke-current flex-shrink-0 w-6 h-6">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <span>Tenant ini belum memiliki logo. Upload logo terlebih dahulu.</span>
                            </div>
                        </div>

                        <a href="{{ route('admin.logo.edit', $tenant->id) }}"
                            class="btn btn-primary w-full mt-4">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M12 16.5V9.75m0 0l3 3m-3-3l-3 3M6.75 19.5a4.5 4.5 0 01-1.41-8.775 5.25 5.25 0 0110.233-2.33A3 3 0 0116.5 19.5H6.75z" />
                            </svg>
                            Upload Logo Pertama
                        </a>
                    @endif
                </div>
            </div>
        </div>

        <!-- Preview Locations -->
        <div class="space-y-6">
            <div class="card bg-base-100 border border-base-200 shadow-sm">
                <div class="card-body">
                    <h3 class="card-title text-lg">Lokasi Penggunaan</h3>
                    <ul class="list-disc list-inside space-y-2 text-sm">
                        <li>Navbar (top left)</li>
                        <li>Sidebar (top)</li>
                        <li>Login page</li>
                        <li>Email templates</li>
                        <li>PDF exports</li>
                    </ul>
                </div>
            </div>

            <!-- Logo History -->
            @if ($logos->count() > 1)
                <div class="card bg-base-100 border border-base-200 shadow-sm">
                    <div class="card-body">
                        <h3 class="card-title text-base">Riwayat Logo</h3>
                        <div class="space-y-2">
                            @foreach ($logos->skip(1) as $logo)
                                <div class="border border-base-300 rounded p-2 bg-base-50">
                                    <div class="flex items-start justify-between">
                                        <div>
                                            <p class="text-xs font-semibold">{{ $logo->nama_file }}</p>
                                            <p class="text-xs text-base-600">
                                                {{ $logo->uploaded_at->format('d M Y') }}
                                            </p>
                                        </div>
                                    </div>
                                    <form action="{{ route('admin.logo.restore', $logo->id) }}"
                                        method="POST" class="mt-2">
                                        @csrf
                                        <button type="submit" class="btn btn-xs btn-ghost w-full"
                                            onclick="return confirm('Restore logo ini?')">
                                            Aktifkan
                                        </button>
                                    </form>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

            <!-- Tips -->
            <div class="card bg-info bg-opacity-10 border border-info shadow-sm">
                <div class="card-body">
                    <h3 class="card-title text-sm">💡 Tips</h3>
                    <ul class="list-disc list-inside space-y-1 text-xs">
                        <li>Gunakan format PNG dengan background transparan</li>
                        <li>Ukuran ideal: 300x150px</li>
                        <li>Pastikan kontras bagus dengan background putih</li>
                        <li>Simpan ukuran file &lt; 1MB</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
