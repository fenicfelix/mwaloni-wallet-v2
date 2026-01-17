@props([
'label',
'name' => null,
'options' => [],
'placeholder' => null,
'required' => false,
'disabled' => false,
])

<div class="md-form-group float-label">
    <select name="{{ $name }}" {{ $attributes->merge(['class' => 'md-input']) }}
        @if($required) required @endif
        @if($disabled) disabled @endif
        >
        @if($placeholder)
        <option value="">{{ $placeholder }}</option>
        @endif

        @foreach($options as $value => $text)
        <option value="{{ $value }}">{{ $text }}</option>
        @endforeach
    </select>

    <label>{{ $label }}</label>

    @error($attributes->get('wire:model'))
    <span class="text-danger text-sm">{{ $message }}</span>
    @enderror
</div>