@props([
    'label',
    'name' => null,
    'type' => 'text',
    'required' => false,
    'disabled' => false,
])

<div class="md-form-group float-label">
    <input type="{{ $type }}" name="{{ $name }}" class="md-input" {{ $attributes->whereStartsWith('wire:model') }}
    @if($required) required @endif
    @if($disabled) disabled @endif
    >

    <label>{{ $label }}</label>

    @error($attributes->whereStartsWith('wire:model')->first())
    <span class="text-danger text-sm">{{ $message }}</span>
    @enderror
</div>