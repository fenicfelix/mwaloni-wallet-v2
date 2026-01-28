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

        info('Channel: ' . $this->channel);

        // convert the above cases to use a mapping
        switch ($this->channel) {
            case 'daraja-mobile':
                ProcessDarajaB2CPayment::dispatch($this->transactionId);
                break;
            case 'daraja-paybill':
                ProcessDarajaB2BPayment::dispatch($this->transactionId);
                break;
            case 'daraja-till':
                ProcessDarajaB2BPayment::dispatch($this->transactionId);
                break;
            case 'daraja-transfer':
                ProcessDarajaB2BPayment::dispatch($this->transactionId);
                break;
            case 'jenga-pesalink-bank':
                ProcessJengaPayments::dispatch($this->transactionId);
                break;
            case 'jenga-ift':
                ProcessJengaPayments::dispatch($this->transactionId);
                break;
            case 'stanbic':
                ProcessStanbicPayment::dispatch($this->transactionId);
                break;

            default:
                Log::warning("Transaction Channel not specified");
                $this->unknownChannel($this->transactionId);
                break;
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
