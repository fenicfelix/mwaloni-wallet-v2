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
use Wallet\Core\Jobs\Jenga;
use Wallet\Core\Jobs\PushTransactionCallback;

class ProcessPesalinkToBankPayment implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    use Jenga;

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
                $result = json_decode($this->jengaPesalinkBank($transaction->account, json_decode($transaction->payload?->trx_payload)));
                if ($result->status) {
                    $transaction->receipt_number = $result->data->transactionId;
                    $transaction->status = TransactionStatus::SUCCESS;
                } else {
                    $transaction->status = TransactionStatus::FAILED;
                }

                $transaction->result_description = $result->message;

                $data = [
                    "ResultType" => 0,
                    "ResultCode" => 0,
                    "ResultDesc" => $result->data->status,
                    "TransactionID" => $result->data->transactionId,
                    "ResultParameters" => [],
                    "orderNumber" => $transaction->order_number,
                ];

                $transaction->payload->update([
                    "raw_callback" => json_encode($data)
                ]);

                if (!$transaction->save()) {
                    throw new \Exception("PESALINK Payment Failed to save " . $transaction->id);
                } else {
                    throw new \Exception("PESALINK Payment Was Successful " . $transaction->id);
                    if ($transaction->service->callback_url != NULL) {
                        PushTransactionCallback::dispatch($data, $transaction->service->callback_url);
                    }
                }
            }
        } catch (\Throwable $th) {
            //throw $th;
            Log::warning($th->getMessage());
        }
    }
}
