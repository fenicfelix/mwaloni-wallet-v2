@props([
    'label',
    'name' => null,
    'type' => 'text',
    'required' => false,
    'disabled' => false,
    'help' => null,
])

@php
// Extract the wire:model path (formData.name)
$wireModel = collect($attributes->getAttributes())
->first(fn ($_, $key) => str_starts_with($key, 'wire:model'));

$errorKey = $wireModel ? str_replace(['wire:model', '.defer', '.lazy', '.live', '='], '', $wireModel) : null;
@endphp

<div class="md-form-group float-label">
    <label>{{ $label }}</label>
    
    <input type="{{ $type }}" name="{{ $name }}" {{ $attributes->merge(['class' => 'form-control']) }}
    @if($required) required @endif
    @if($disabled) disabled @endif
    >

    @if($help)
    <small class="float-left"><code>{{ $help }}</code></small>
    @endif

    @if($errorKey)
    @error($errorKey)
    <span class="text-danger text-sm">{{ $message }}</span>
    @enderror
    @endif
</div>