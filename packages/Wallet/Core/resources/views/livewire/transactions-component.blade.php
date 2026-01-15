<div>
    @if ($list)
    @include('core::livewire.components.transactions.list')
    @elseif($view)
    @include('core::livewire.components.transactions.view')
    @elseif($edit)
    @include('core::livewire.components.transactions.edit')
    @elseif($pay_offline)
    @include('core::livewire.components.transactions.edit')
    @endif
</div>