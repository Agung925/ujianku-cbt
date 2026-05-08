<div class="card bg-base-100 shadow">
    <div class="card-body">
        <h2 class="card-title flex justify-between items-center">
            📰 Berita Pendidikan Terbaru
            <span class="text-xs badge badge-ghost">{{ count($news) ?? 0 }}</span>
        </h2>
        <div class="divider my-2"></div>
        @if($news && count($news) > 0)
            <div class="space-y-3 max-h-96 overflow-y-auto">
                @foreach($news as $item)
                <div class="p-3 bg-base-200 rounded hover:bg-base-300 transition">
                    <a href="{{ $item['link'] }}" target="_blank" rel="noopener noreferrer" class="text-sm font-semibold link link-primary">
                        {{ Str::limit($item['title'], 60) }}
                    </a>
                    <p class="text-xs text-base-content/70 mt-1">{{ $item['description'] }}</p>
                    <div class="flex justify-between items-center mt-2">
                        <span class="text-xs badge badge-ghost">{{ $item['source'] }}</span>
                        <span class="text-xs text-base-content/50">{{ $item['date'] }}</span>
                    </div>
                </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-6 text-base-content/50">
                <p class="text-sm">Tidak ada berita terbaru. Cek kembali nanti.</p>
            </div>
        @endif
        <div class="divider my-2"></div>
        <p class="text-xs text-base-content/50 text-center">Diperbarui setiap jam</p>
    </div>
</div>
