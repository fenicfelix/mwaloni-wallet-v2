<?php

namespace App\Jobs\Jenga;

use App\Jobs\PushTransactionCallback;
use Wallet\Core\Models\Transaction;
use Wallet\Core\Models\TransactionType;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessEquityToEquityPayment implements ShouldQueue
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
        try {
            $transaction = Transaction::with(["service", "account.currency", "payload"])->where("id", "=", $this->transactionId)->first();
            if ($transaction) {
                $result = json_decode(jenga_send_to_equity($transaction->account, json_decode($transaction->payload?->trx_payload)));
                if ($result->status) {
                    $transaction->receipt_number = $result->data->transactionId;
                    $transaction->status_id = 2;
                } else {
                    $transaction->status_id = 3;
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
                    throw new \Exception("EQUIYTRANSFER Payment Failed to save " . $transaction->id);
                } else {
                    log_transaction($transaction->account->id, TransactionType::TYPE_PAYMENTS, $transaction->service->client_id, $transaction->service_id, floor($transaction->disbursed_amount), $result->message, $result->message, $transaction->order_number . ' - ' . $transaction->account_number, $transaction->requested_by);
                    if ($transaction->service->callback_url != NULL) {
                        PushTransactionCallback::dispatch($data, $transaction->service->callback_url);
                    }
                }
            }
        } catch (\Throwable $th) {
            //throw $th;
            Log::error($th->getMessage());
        }
    }
}
