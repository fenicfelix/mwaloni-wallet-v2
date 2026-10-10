@props([
'label',
'name' => null,
'options' => [],
'placeholder' => null,
'required' => false,
'disabled' => false,
])

<div class="md-form-group float-label">
    <label class="form-label" for="{{ $name }}">{{ $label }}
        @if ($required)
        <span class="text-danger">*</span>
        @endif
    </label>
    
    <select name="{{ $name }}" {{ $attributes->merge(['class' => 'form-control']) }}
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

    @error($attributes->get('wire:model'))
    <span class="text-danger text-sm">{{ $message }}</span>
    @enderror
</div>