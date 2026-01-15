<?php

namespace Wallet\Core\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Wallet\Core\Http\Traits\MwaloniWallet;

class ProcessComposedMessages implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    use MwaloniWallet;

    protected $message;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($message)
    {
        $this->message = $message;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $contents = "";
        try {
            $contents = array_map('str_getcsv', explode(PHP_EOL, Storage::get($this->message->contacts)));
        } catch (\Throwable $th) {
            //throw $th;
        }

        if (!empty($contents)) {
            $keys = $contents[0];
            if ($this->message->has_placeholders) {
                $key_size = sizeof((array) $keys);
                for ($j = 1; $j < sizeof($contents); $j++) {
                    if (!empty($contents[$j][0]) && $contents[$j][0] != "") {
                        $phone = "";
                        $messageBody = $this->message->body;
                        for ($i = 0; $i < $key_size; $i++) {
                            if ($keys[$i] == "phone") $phone = clean_phone_number($contents[$j][$i]);
                            else {
                                $messageBody = str_replace("{" . $keys[$i] . "}", $contents[$j][$i], $messageBody);
                            }
                        }
                        $this->send_sms($phone, $messageBody);
                    }
                }
            } else {
                $messageBody = $this->message->body;
                for ($j = 1; $j < sizeof($contents); $j++) {
                    if (!empty($contents[$j][0])) {
                        $phone = clean_phone_number($contents[$j][0]);
                        $this->send_sms($phone, $messageBody);
                    }
                }
            }
        }
    }
}
