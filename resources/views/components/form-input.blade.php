{{-- resources/views/components/form-input.blade.php --}}
@props(['name', 'label' => '', 'value' => '', 'type' => 'text', 'error' => '', 'required' => false, 'placeholder' => ''])

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
    <input
        type="{{ $type }}"
        name="{{ $name }}"
        id="{{ $name }}"
        value="{{ old($name, $value) }}"
        placeholder="{{ $placeholder }}"
        @class([
            'input input-bordered w-full',
            'input-error' => $error || $errors->has($name)
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
