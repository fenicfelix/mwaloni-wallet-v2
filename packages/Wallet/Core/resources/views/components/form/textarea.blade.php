@props([
    'label',
    'name' => null,
    'rows' => 3,
    'required' => false,
    'disabled' => false,
])

<div class="md-form-group float-label">
    <label class="form-label" for="{{ $name }}">{{ $label }}
        @if ($required)
        <span class="text-danger">*</span>
        @endif
    </label>

    <textarea name="{{ $name }}" rows="{{ $rows }}" {{ $attributes->merge(['class' => 'form-control']) }}
        @if($required) required @endif
        @if($disabled) disabled @endif
        placeholder="{{ $label }}"
    ></textarea>

    @error($attributes->get('wire:model'))
    <span class="text-danger text-sm">{{ $message }}</span>
    @enderror
</div>