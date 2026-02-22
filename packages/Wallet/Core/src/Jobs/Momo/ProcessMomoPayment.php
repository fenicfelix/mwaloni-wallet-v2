<?php

namespace Wallet\Core\Jobs\Momo;

use Akika\MoMo\Enums\Currency;
use Akika\MoMo\Facades\MoMo;
use Wallet\Core\Models\Transaction;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Wallet\Core\Http\Enums\TransactionStatus;
use Wallet\Core\Repositories\TransactionRepository;

class ProcessMomoPayment implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $transactionId;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($transactionId)
    {
        $this->transactionId = $transactionId;
    }

    /**
     * Execute the job.
     *
     * @return void
     */

    public function handle()
    {
        // Handle the MoMo payment processing logic here
        $transaction = Transaction::with(["account", "payload"])->where("id", "=", $this->transactionId)->first();

        // Check if the transaction exists
        if (!$transaction) {
            return;
        }

        try {

            /// Check if the transaction has been submitted
            $result = json_decode($this->submitTransaction($transaction), true);
            if (!$result) {
                return;
            }

            $updateData = [
                "status" => TransactionStatus::SUBMITTED,
                "receipt_number" => $result,
                "result_description" => "Payment submitted at " . now(),
            ];

            $payloadData = [
                "raw_callback" => $result
            ];

        } catch (\Throwable $th) {
            info("MoMo Payment Exception: " . $th->getMessage());
            $updateData = [
                "status" => TransactionStatus::FAILED,
                "result_description" => $th->getMessage(),
                "completed_at" => now()
            ];
            
            $payloadData = [
                "raw_callback" => json_encode($th)
            ];
        }

        app(TransactionRepository::class)->updateWithPayload($transaction->id, $updateData, $payloadData);
    }

    private function submitTransaction($transaction): ?string
    {
        // Prepare payload
        $account = $transaction->account;
        $payload = json_decode($transaction->payload?->trx_payload, true);

        $referenceId = MoMo::with(
            'your_secondary_key',       // overrides momo.<env>.secondary_key
            $account->api_username,   // overrides momo.<env>.user_reference_id
            'your_api_key',
        )->disbursement()->transfer(
            $payload['amount'],
            Currency::UgandaShilling,
            externalId: $payload['externalId'] ?? $transaction->order_number,
            payeeMsisdn: $payload['payeeMsisdn'],
            payerMessage: $payload['payerMessage'],
            payeeNote: $payload['payeeNote'],
        );

        return $referenceId;
    }
}
