<div>
    @if ($add)
    @include('core::livewire.components.services.add')
    @elseif($withdraw)
        @include('core::livewire.components.services.withdraw')
    @else
        @include('core::livewire.components.services.list')
    @endif
</div>