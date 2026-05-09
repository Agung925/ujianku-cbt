{{-- resources/views/components/alert.blade.php --}}
@props(['type' => 'info', 'message' => '', 'dismissible' => true])

@php
    $typeConfig = [
        'success' => ['bg' => 'alert-success', 'icon' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z'],
        'error' => ['bg' => 'alert-error', 'icon' => 'M10 14l-2-2m0 0l-2-2m2 2l2-2m-2 2l-2 2m8-2a9 9 0 11-18 0 9 9 0 0118 0z'],
        'warning' => ['bg' => 'alert-warning', 'icon' => 'M12 9v2m0 4v2m7.18-11.08A9 9 0 005.82 2m0 0A9 9 0 000 9m0 0a9 9 0 0018 0'],
        'info' => ['bg' => 'alert-info', 'icon' => 'M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z'],
    ];
    $config = $typeConfig[$type] ?? $typeConfig['info'];
@endphp

<div class="alert {{ $config['bg'] }} shadow-lg">
    <div>
        <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current flex-shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $config['icon'] }}" />
        </svg>
        <span>{{ $message ?? $slot }}</span>
    </div>
    @if($dismissible)
    <button class="btn btn-sm btn-ghost" onclick="this.parentElement.remove()">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
        </svg>
    </button>
    @endif
</div>
