{{-- resources/views/components/table-responsive.blade.php --}}
@props(['headers' => [], 'rows' => []])

<div class="overflow-x-auto">
    <table class="table table-compact w-full">
        <thead class="bg-base-200">
            <tr>
                @foreach($headers as $header)
                    <th class="bg-base-200 text-base-content font-semibold">{{ $header }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            {{ $slot }}
        </tbody>
    </table>
</div>
