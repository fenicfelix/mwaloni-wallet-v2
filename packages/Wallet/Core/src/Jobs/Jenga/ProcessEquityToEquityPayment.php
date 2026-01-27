<?php

namespace Wallet\Core\Jobs\Jenga;

use Wallet\Core\Models\Transaction;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Wallet\Core\Http\Enums\TransactionStatus;
use Wallet\Core\Http\Traits\MwaloniWallet;
use Wallet\Core\Jobs\Jenga;
use Wallet\Core\Jobs\PushTransactionCallback;
use Wallet\Core\Repositories\TransactionRepository;

class ProcessEquityToEquityPayment implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    use Jenga;
    use MwaloniWallet;

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
        try {
            $transaction = Transaction::with(["service", "account.currency", "payload"])->where("id", "=", $this->transactionId)->first();
            if ($transaction) {
                $result = json_decode($this->jengaSendToEquity($transaction->account, json_decode($transaction->payload?->trx_payload)));

                if ($result->status) {
                    $updateData = [
                        'receipt_number' => $result->data->transactionId,
                        'status' => TransactionStatus::SUCCESS,
                    ];
                } else {
                    $updateData['status'] = TransactionStatus::FAILED;
                }

                $updateData['result_description'] = $result->message;

                $payloadData = [
                    "ResultType" => 0,
                    "ResultCode" => 0,
                    "ResultDesc" => $result->data->status,
                    "TransactionID" => $result->data->transactionId,
                    "ResultParameters" => [],
                    "orderNumber" => $transaction->order_number,
                ];

                if (!app(TransactionRepository::class)->updateWithPayload($transaction->id, $updateData, $payloadData)) {
                    throw new \Exception("EQUIYTRANSFER Payment Failed to save " . $transaction->id);
                }

                if ($updateData['status'] == TransactionStatus::SUCCESS) {
                    app(TransactionRepository::class)->completeTransaction($transaction->id);
                }
            }
        } catch (\Throwable $th) {
            //throw $th;
            Log::error($th->getMessage());
        }
    }
}
