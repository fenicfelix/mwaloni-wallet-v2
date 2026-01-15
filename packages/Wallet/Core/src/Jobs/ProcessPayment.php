<?php

namespace Wallet\Core\Jobs;

use App\Jobs\Daraja\ProcessDarajaB2BPayment;
use App\Jobs\Daraja\ProcessDarajaB2CPayment;
use App\Jobs\Jenga\ProcessJengaPayments;
use App\Jobs\Ncba\ProcessNcbaPayments;
use Wallet\Core\Models\Transaction;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

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

        info('Channel: ' . $this->channel);

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
            "status_id" => 3,
            "completed_at" => date('Y-m-d H:i:s'),
            "result_description" => "Transaction Channel not specified"
        ]);
    }
}
