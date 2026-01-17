<?php

namespace Wallet\Core\Jobs;

use AfricasTalking\SDK\AfricasTalking;
use Wallet\Core\Models\Outbox;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessSMS implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $phone;
    protected $message;
    protected $messageId;

    public function __construct($phone, $message, $messageId)
    {
        $this->phone = $phone;
        $this->message = $message;
        $this->messageId = $messageId;
    }

    public function handle()
    {
        $username = getOption('AT-USERNAME');
        $apiKey = getOption('AT-KEY');
        $from = getOption('AT-FROM');

        $AT = new AfricasTalking($username, $apiKey);
        $sms = $AT->sms();
        $sms->send([
            'to'        =>  $this->phone,
            'message'   => $this->message
        ]);

        if ($from) $sms->send["from"] = $from;

        $msg = Outbox::query()->find($this->messageId);
        if ($msg) {
            $msg->sent = 1;
            $msg->sent_at = now();
            $msg->save();
        }
    }
}
