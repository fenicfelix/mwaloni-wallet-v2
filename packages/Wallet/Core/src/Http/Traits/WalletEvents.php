<?php

namespace Wallet\Core\Http\Traits;

trait WalletEvents
{
    public function notify($message, $type = 'success')
    {
        $this->dispatchBrowserEvent('alert', ['message' => $message, 'type' => $type]);
    }
}
