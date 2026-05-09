{{-- resources/views/components/form-textarea.blade.php --}}
@props(['name', 'label' => '', 'value' => '', 'error' => '', 'required' => false, 'placeholder' => '', 'rows' => 4])

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
    <textarea
        name="{{ $name }}"
        id="{{ $name }}"
        placeholder="{{ $placeholder }}"
        rows="{{ $rows }}"
        @class([
            'textarea textarea-bordered w-full font-mono',
            'textarea-error' => $error || $errors->has($name)
        ])
        @if($required) required @endif
        {{ $attributes }}
    >{{ old($name, $value) }}</textarea>
    @if($error || $errors->has($name))
        <label class="label" for="{{ $name }}">
            <span class="label-text-alt text-error">
                {{ $error ?: $errors->first($name) }}
            </span>
        </label>
    @endif
</div>
