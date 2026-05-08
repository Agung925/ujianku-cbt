<x-app-layout>
    <x-slot name="header">
        <h1 class="text-2xl font-bold text-base-content">Manajemen Logo & Identitas</h1>
    </x-slot>

    <!-- Success Alert -->
    @if (session('success'))
        <div class="alert alert-success shadow-lg mb-6">
            <div>
                <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current flex-shrink-0 h-6 w-6"
                    fill="none" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span>{{ session('success') }}</span>
            </div>
        </div>
    @endif

    <!-- Error Alert -->
    @if (session('error'))
        <div class="alert alert-error shadow-lg mb-6">
            <div>
                <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current flex-shrink-0 h-6 w-6"
                    fill="none" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M10 14l-2-2m0 0l-2-2m2 2l2-2m-2 2l-2 2m0 0l2 2m-2-2l2 2"></path>
                </svg>
                <span>{{ session('error') }}</span>
            </div>
        </div>
    @endif

    <!-- Tenants List -->
    <div class="grid gap-6">
        @forelse($tenants as $tenant)
            <div class="card bg-white shadow-md border border-base-200">
                <div class="card-body">
                    <div class="flex items-center justify-between">
                        <div class="flex-1">
                            <h2 class="card-title text-lg">{{ $tenant->name }}</h2>
                            <p class="text-base-600 text-sm">ID: {{ $tenant->id }}</p>

                            @if ($tenant->logos->count() > 0)
                                @php
                                    $latestLogo = $tenant->logos->first();
                                @endphp
                                <div class="mt-3">
                                    <span class="badge badge-primary">Logo ada</span>
                                    <span
                                        class="text-xs text-base-600 ml-2">Diupload:
                                        {{ $latestLogo->created_at->format('d M Y H:i') }}</span>
                                </div>
                            @else
                                <div class="mt-3">
                                    <span class="badge badge-ghost">Tidak ada logo</span>
                                </div>
                            @endif
                        </div>

                        <div class="flex gap-2">
                            <a href="{{ route('admin.logo.show', $tenant->id) }}"
                                class="btn btn-sm btn-ghost">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                                    viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                                    class="w-4 h-4">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                Lihat
                            </a>
                            <a href="{{ route('admin.logo.edit', $tenant->id) }}"
                                class="btn btn-sm btn-primary">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                                    viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                                    class="w-4 h-4">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M12 16.5V9.75m0 0l3 3m-3-3l-3 3M6.75 19.5a4.5 4.5 0 01-1.41-8.775 5.25 5.25 0 0110.233-2.33A3 3 0 0116.5 19.5H6.75z" />
                                </svg>
                                Upload
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="alert alert-info shadow-lg">
                <div>
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                        class="stroke-current flex-shrink-0 w-6 h-6">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span>Tidak ada tenant</span>
                </div>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    <div class="mt-6">
        {{ $tenants->links() }}
    </div>
</x-app-layout>
