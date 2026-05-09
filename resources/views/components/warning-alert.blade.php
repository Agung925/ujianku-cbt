{{-- resources/views/components/warning-alert.blade.php --}}
@props(['message' => ''])

<div class="alert alert-warning shadow-lg">
    <div>
        <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current flex-shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4v2m7.18-11.08A9 9 0 005.82 2m0 0A9 9 0 000 9m0 0a9 9 0 0018 0" />
        </svg>
        <span>{{ $message ?? $slot }}</span>
    </div>
    <button class="btn btn-sm btn-ghost" onclick="this.parentElement.remove()">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
        </svg>
    </button>
</div>
