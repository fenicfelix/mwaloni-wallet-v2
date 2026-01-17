<?php

namespace Wallet\Core\Http\Traits;

trait NotifyBrowser
{

    /**
     * Dispatch a browser notification.
     *
     * @param  string  $message
     * @param  string  $type
     */
    public function notify($message, $type = 'success')
    {
        $this->dispatch('alert', ['message' => $message, 'type' => $type]);
    }

    /**
     * Dispatch a confirmation dialog.
     *
     * @param  string  $title
     * @param  string  $message
     * @param  string  $type
     * @param  string  $confirmTitle
     * @param  callable|null  $callback
     */
    public function confirm($title, $message, $type = 'success', $confirmTitle = 'OK', $callback = null)
    {
        $this->dispatch('confirm', [
            'title' => $title,
            'message' => $message,
            'confirmTitle' => $confirmTitle,
            'type' => $type,
            'callback' => $callback,
        ]);
    }
}
