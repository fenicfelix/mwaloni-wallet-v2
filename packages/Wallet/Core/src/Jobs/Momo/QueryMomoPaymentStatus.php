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

class QueryMomoPaymentStatus implements ShouldQueue
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
        $successfulStatusLabel = 'SUCCESSFUL';
        $transaction = Transaction::with(["account"])->where("id", "=", $this->transactionId)->first();
        if (!$transaction) {
            return;
        }

        $account = $transaction->account;

        $status = MoMo::with(
            'your_secondary_key',       // overrides momo.<env>.secondary_key
            $account->api_username,   // overrides momo.<env>.user_reference_id
            'your_api_key',
        )->disbursement()->getTransferStatus($this->transactionId);

        /*
        $status = [
            "amount" => "6.00",
            "currency" => "EUR",
            "financialTransactionId" => "554662921",
            "externalId" => "01KHXC2B1JQTKN16T8XHHTZX6A",
            "payee" => [
                "partyIdType" => "MSISDN",
                "partyId" => "0296631315"
            ],
            "payeeNote" => "Reprehenderit sequi fugiat ipsam sed.",
            "status" => "SUCCESSFUL"
        ];
        */
        if (!$status) {
            info("Momo Payment Status Query Failed");
            return;
        }

        // Process the payment status
        if ($status['status'] === $successfulStatusLabel) {
            info("Momo Payment Status Query Successful");
            $updateData = [
                "status" => TransactionStatus::SUCCESS,
                "receipt_number" => $status["externalId"],
                "result_description" => $status["status"],
                "completed_at" => date('Y-m-d H:i:s', strtotime($status["Transaction"]["Date"]))
            ];
        } else {
            info("Momo Payment Status Query Unsuccessful");
            $updateData = [
                "status" => TransactionStatus::FAILED,
                /// ....Any other fields to update
                "completed_at" => date('Y-m-d H:i:s')
            ];
        }

        $payloadData = [
            "raw_callback" => json_encode($status)
        ];

        $transactionRepository = app(TransactionRepository::class);
        $transactionRepository->updateWithPayload($transaction->id, $updateData, $payloadData);
        if ($status['status'] === $successfulStatusLabel) {
            // Dispatch a job to handle successful payment
            // complete the payment process
            $transactionRepository->completeTransaction($transaction->id);
        }
    }
}
