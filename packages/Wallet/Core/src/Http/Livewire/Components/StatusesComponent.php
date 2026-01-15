<?php

declare(strict_types=1);

namespace Wallet\Core\Http\Livewire\Components;

use Livewire\Component;

class StatusesComponent extends Component
{
    public function render()
    {
        return view('core::livewire.statuses-component')
            ->layout('core::layouts.app');
    }
}