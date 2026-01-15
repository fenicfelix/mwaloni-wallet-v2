<div>
    @if ($add)
    @include('core::livewire.components.accounts.add')
    @elseif($cashout)
    @include('core::livewire.components.accounts.cashout')
    @elseif($editWithheldAmount)
    @include('core::livewire.components.accounts.update_withheld_amount')
    @else
    @include('core::livewire.components.accounts.list')
    @endif
</div>