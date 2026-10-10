@props([
    'type' => 'button',
    'variant' => 'primary', // primary, danger, secondary, etc.
    'icon' => null,
])

<button type="{{ $type }}" {{ $attributes->merge([
    'class' => "btn btn-{$variant} btn-rounded me-1",
    ]) }}
    >
    @if($icon)
    <i class="{{ $icon }}"></i>
    @endif

    {{ $slot }}
</button>