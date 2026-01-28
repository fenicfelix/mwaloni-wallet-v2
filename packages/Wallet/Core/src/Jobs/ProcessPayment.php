<?php

namespace Wallet\Core\Jobs;

use Wallet\Core\Models\Transaction;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Wallet\Core\Http\Enums\TransactionStatus;
use Wallet\Core\Jobs\Daraja\ProcessDarajaB2BPayment;
use Wallet\Core\Jobs\Daraja\ProcessDarajaB2CPayment;
use Wallet\Core\Jobs\Jenga\ProcessJengaPayments;
use Wallet\Core\Jobs\Ncba\ProcessNcbaPayments;
use Wallet\Core\Jobs\Stanbic\ProcessStanbicPayment;

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
        info('Process Payment: ' . $this->transactionId . " - Channel: " . $this->channel);
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
            $channelMap[$this->channel]::dispatch($this->transactionId)->onQueue('process-payments');
        } else {
            Log::warning("Transaction Channel not specified");
            $this->unknownChannel($this->transactionId);
        }
    }

    private function unknownChannel($transactionId)
    {
        Transaction::where("id", "=", $transactionId)->update([
            "status" => TransactionStatus::FAILED,
            "completed_at" => date('Y-m-d H:i:s'),
            "result_description" => "Transaction Channel not specified"
        ]);
    }
}
