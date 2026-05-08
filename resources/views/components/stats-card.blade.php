{{-- resources/views/components/stats-card.blade.php --}}
{{-- 
    Props:
    - $title: judul kartu
    - $value: nilai utama
    - $desc: deskripsi tambahan (optional)
    - $icon: icon SVG path atau emoji (optional)
    - $color: warna DaisyUI (primary, secondary, accent, info, success, warning, error) - default: primary
    - $trend: 'up', 'down', atau '' (optional)
    - $trendValue: nilai perubahan (optional)
--}}
@props([
    'title' => '',
    'value' => '-',
    'desc' => '',
    'icon' => '',
    'color' => 'primary',
    'trend' => '',
    'trendValue' => '',
])

<div class="stat bg-base-100 rounded-box shadow">
    @if($icon)
    <div class="stat-figure text-{{ $color }}">
        {!! $icon !!}
    </div>
    @endif

    <div class="stat-title text-base-content/70">{{ $title }}</div>
    <div class="stat-value text-{{ $color }}">{{ $value }}</div>

    @if($desc || $trend)
    <div class="stat-desc flex items-center gap-1">
        @if($trend === 'up')
            <span class="text-success">↑ {{ $trendValue }}</span>
        @elseif($trend === 'down')
            <span class="text-error">↓ {{ $trendValue }}</span>
        @endif
        @if($desc)
            <span>{{ $desc }}</span>
        @endif
    </div>
    @endif
</div>
