@props([
    'label',
    'name' => null,
    'type' => 'text',
    'required' => false,
    'disabled' => false,
    'help' => null,
])

<div class="md-form-group float-label">
    <input type="{{ $type }}" name="{{ $name }}" class="md-input" {{ $attributes->whereStartsWith('wire:model') }}
    @if($required) required @endif
    @if($disabled) disabled @endif
    >

    <label>{{ $label }}</label>
    @if($help)
        <small class="float-left"><code>{{ $help }}</code></small>
    @endif

    @error($attributes->whereStartsWith('wire:model')->first())
    <span class="text-danger text-sm">{{ $message }}</span>
    @enderror
</div>