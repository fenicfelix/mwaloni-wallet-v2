@props([
    'label',
    'name' => null,
    'rows' => 3,
    'required' => false,
    'disabled' => false,
])

<div class="md-form-group float-label">
    <textarea name="{{ $name }}" rows="{{ $rows }}" {{ $attributes->merge(['class' => 'md-input']) }}
        @if($required) required @endif
        @if($disabled) disabled @endif
    ></textarea>

    <label>{{ $label }}</label>

    @error($attributes->get('wire:model'))
    <span class="text-danger text-sm">{{ $message }}</span>
    @enderror
</div>