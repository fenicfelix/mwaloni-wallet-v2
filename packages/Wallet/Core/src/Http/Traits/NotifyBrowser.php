<?php

namespace Wallet\Core\Http\Traits;

trait NotifyBrowser
{
    public function notify($message, $type = 'success')
    {
        $this->dispatch('alert', ['message' => $message, 'type' => $type]);
    }
}
