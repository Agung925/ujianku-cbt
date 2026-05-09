{{-- resources/views/components/breadcrumb.blade.php --}}
@props(['items' => []])

<nav class="flex mb-4" aria-label="Breadcrumb">
    <ol class="inline-flex items-center space-x-1 md:space-x-3">
        @foreach($items as $index => $item)
            <li class="inline-flex items-center">
                @if($index > 0)
                    <div class="flex items-center">
                        <svg class="w-4 h-4 text-base-400 mx-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 6 10">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 9 4-4-4-4"/>
                        </svg>
                    </div>
                @endif
                @if(isset($item['url']))
                    <a href="{{ $item['url'] }}" class="inline-flex items-center text-sm font-medium text-base-700 hover:text-primary">
                        @if(isset($item['icon']))
                            <svg class="w-4 h-4 mr-1" {{ $item['icon'] }}></svg>
                        @endif
                        {{ $item['label'] }}
                    </a>
                @else
                    <span class="inline-flex items-center text-sm font-medium text-base-500">
                        @if(isset($item['icon']))
                            <svg class="w-4 h-4 mr-1" {{ $item['icon'] }}></svg>
                        @endif
                        {{ $item['label'] }}
                    </span>
                @endif
            </li>
        @endforeach
    </ol>
</nav>
