<?php

declare(strict_types=1);

namespace Wallet\Core\Http\Livewire\Components;

use Livewire\Component;

class RolesComponent extends Component
{
    public function render()
    {
        return view('core::livewire.roles-component')
            ->layout('core::layouts.app');
    }
}