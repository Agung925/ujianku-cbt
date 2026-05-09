{{-- resources/views/components/form-file.blade.php --}}
@props(['name', 'label' => '', 'accept' => '', 'error' => '', 'required' => false, 'preview_url' => ''])

<div class="form-control w-full">
    @if($label)
        <label class="label" for="{{ $name }}">
            <span class="label-text font-semibold">
                {{ $label }}
                @if($required)
                    <span class="text-error">*</span>
                @endif
            </span>
        </label>
    @endif
    
    @if($preview_url)
        <div class="mb-4">
            <img src="{{ $preview_url }}" alt="Preview" class="max-h-32 rounded border border-base-300">
        </div>
    @endif
    
    <input
        type="file"
        name="{{ $name }}"
        id="{{ $name }}"
        accept="{{ $accept }}"
        @class([
            'file-input file-input-bordered w-full',
            'file-input-error' => $error || $errors->has($name)
        ])
        @if($required) required @endif
        {{ $attributes }}
    >
    @if($error || $errors->has($name))
        <label class="label" for="{{ $name }}">
            <span class="label-text-alt text-error">
                {{ $error ?: $errors->first($name) }}
            </span>
        </label>
    @endif
</div>
