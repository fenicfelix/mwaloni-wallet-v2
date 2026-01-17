@props([
    'label',
    'name' => null,
    'value' => null,
    'color' => 'blue',
    'required' => false,
    'disabled' => false,
])

<label class="md-switch">
    <input type="checkbox" name="{{ $name }}" value="{{ $value }}" {{ $attributes }} @if($required) required @endif
        @if($disabled) disabled @endif>

    <i class="{{ $color }}"></i>

    {{ $label }}
</label>

@error($attributes->get('wire:model'))
<span class="text-danger text-sm">{{ $message }}</span>
@enderror