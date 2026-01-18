<?php

namespace Wallet\Core\Jobs\Jenga;

use App\Jobs\PushTransactionCallback;
use Wallet\Core\Models\Transaction;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Wallet\Core\Http\Enums\TransactionType;

class ProcessJengaPayments implements ShouldQueue
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
            $transaction = Transaction::with(["service", "paymentChannel", "account.currency", "payload"])->where("id", "=", $this->transactionId)->first();
            if ($transaction) {
                $result = [];
                if ($transaction->paymentChannel->slug == "jenga-pesalink-bank") $result = json_decode(jenga_pesalink_bank($transaction->account, json_decode($transaction->payload?->trx_payload)));
                else if ($transaction->paymentChannel->slug == "jenga-ift") $result = json_decode(jenga_send_to_equity($transaction->account, json_decode($transaction->payload?->trx_payload)));
                if ($result) {
                    if ($result->status) {
                        $transaction->status_id = 2;
                        $result_status = "SUCCESS";
                        $transactionId = $result->data->transactionId;
                        $transaction->receipt_number = $transactionId;
                    } else {
                        $transaction->status_id = 3;
                        $result_status = "FAILED";
                        $transactionId = $result->code;
                    }

                    $transaction->completed_at = date('Y-m-d H:i:s');
                    $transaction->result_description = $result->message;

                    $data = [
                        "ResultType" => 0,
                        "ResultCode" => 0,
                        "ResultDesc" => $result_status,
                        "TransactionID" => $transactionId,
                        "ResultParameters" => [],
                        "orderNumber" => $transaction->order_number,
                    ];

                    $transaction->payload->update([
                        "raw_callback" => json_encode($data)
                    ]);

                    if (!$transaction->save()) {
                        throw new \Exception("JENGA Payment Failed to save " . $transaction->id);
                    } else {
                        log_transaction($transaction->account->id, TransactionType::PAYMENTS, $transaction->service->client_id, $transaction->service_id, floor($transaction->disbursed_amount), $result_status, $result->message, $transaction->order_number . ' - ' . $transaction->account_number, $transaction->requested_by);
                        if ($transaction->service->callback_url != NULL) {
                            PushTransactionCallback::dispatch($data, $transaction->service->callback_url);
                        }
                    }
                } else {
                    throw new \Exception("Jenga Payment Failed");
                }
            } else {
                throw new \Exception("Transaction not found");
            }
        } catch (\Throwable $th) {
            Log::warning($th->getMessage());
        }
    }
}
