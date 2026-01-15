<?php

declare(strict_types=1);

namespace Wallet\Core\Http\Livewire\Components;

use Livewire\Component;

class MessagesComponent extends Component
{
    public ?string $content_title;

    public ?bool $add;

    public function mount()
    {
        $this->initializeValues();
    }

    private function initializeValues()
    {
        $this->content_title = "Messages Manager";
    }
    
    public function render()
    {
        return view('core::livewire.messages-component')
            ->layout('core::layouts.app');
    }
}