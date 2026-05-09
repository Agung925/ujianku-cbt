{{-- resources/views/components/form-select.blade.php --}}
@props(['name', 'label' => '', 'options' => [], 'value' => '', 'error' => '', 'required' => false, 'placeholder' => 'Pilih...'])

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
    <select
        name="{{ $name }}"
        id="{{ $name }}"
        @class([
            'select select-bordered w-full',
            'select-error' => $error || $errors->has($name)
        ])
        @if($required) required @endif
        {{ $attributes }}
    >
        <option value="" disabled selected>{{ $placeholder }}</option>
        @foreach($options as $optValue => $optLabel)
            <option value="{{ $optValue }}" @selected(old($name, $value) == $optValue)>
                {{ $optLabel }}
            </option>
        @endforeach
    </select>
    @if($error || $errors->has($name))
        <label class="label" for="{{ $name }}">
            <span class="label-text-alt text-error">
                {{ $error ?: $errors->first($name) }}
            </span>
        </label>
    @endif
</div>
