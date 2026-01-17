@props([
    'type' => 'button',
    'variant' => 'primary', // primary, danger, secondary, etc.
    'icon' => null,
])

<button type="{{ $type }}" {{ $attributes->merge([
    'class' => "btn btn-{$variant} btn-rounded",
    ]) }}
    >
    @if($icon)
    <i class="{{ $icon }}"></i>
    @endif

    {{ $slot }}
</button>