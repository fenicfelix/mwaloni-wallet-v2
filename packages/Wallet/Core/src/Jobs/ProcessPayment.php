<?php

namespace Wallet\Core\Jobs;

use App\Jobs\Daraja\ProcessDarajaB2BPayment;
use App\Jobs\Daraja\ProcessDarajaB2CPayment;
use App\Jobs\Jenga\ProcessJengaPayments;
use Wallet\Core\Models\Transaction;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Wallet\Core\Jobs\Ncba\ProcessNcbaPayments;

class ProcessPayment implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $channel;
    protected $transactionId;

    public function __construct($transactionId, $channel)
    {
        $this->transactionId = $transactionId;
        $this->channel = $channel;
    }

    public function handle()
    {

        /*
        5	jenga-pesalink-mobile
        9	ncba-eft
        8	ncba-ift
        12	ncba-mobile
        11	ncba-pesalink
        10	ncba-rtgs
        */

        // if $this->channel like ncba-*, then ProcessNcbaPayments::dispatch($this->transactionId);
        if (strpos($this->channel, 'ncba-') !== false) {
            ProcessNcbaPayments::dispatch($this->transactionId);
            return;
        }

        // convert the above cases to use a mapping
        $channelMap = [
            'daraja-mobile' => ProcessDarajaB2CPayment::class,
            'daraja-paybill' => ProcessDarajaB2BPayment::class,
            'daraja-till' => ProcessDarajaB2BPayment::class,
            'daraja-transfer' => ProcessDarajaB2BPayment::class,
            'jenga-pesalink-bank' => ProcessJengaPayments::class,
            'jenga-ift' => ProcessJengaPayments::class,
            'stanbic' => ProcessStanbicPayment::class,
        ];

        if (array_key_exists($this->channel, $channelMap)) {
            $channelMap[$this->channel]::dispatch($this->transactionId);
        } else {
            Log::warning("Transaction Channel not specified");
            $this->unknownChannel($this->transactionId);
        }
    }

    private function unknownChannel($transactionId)
    {
        Transaction::where("id", "=", $transactionId)->update([
            "status_id" => 3,
            "completed_at" => date('Y-m-d H:i:s'),
            "result_description" => "Transaction Channel not specified"
        ]);
    }
}
