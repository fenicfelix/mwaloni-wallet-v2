<?php

namespace Wallet\Core\Jobs\Daraja;

use Akika\LaravelMpesaMultivendor\Mpesa;
use Wallet\Core\Models\Transaction;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Auth;
use Wallet\Core\Http\Enums\TransactionStatus;
use Wallet\Core\Http\Enums\TransactionType;

class ProcessReverseRequest implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $id;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($id)
    {
        $this->id = $id;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if (!$this->id) return false;

        $transaction = Transaction::with(["service", "account", "payload"])->where("id", "=", $this->id)->first();

        if ($transaction) {
            $response = json_decode($this->performTransaction($transaction));
            if ($response) {
                $payment_results_status = "";
                $payment_results_desc = "";
                $initiatedBy = Auth::id();

                try {
                    $payment_results_status = "SUBMITTED";
                    $payment_results_desc = $response->ConversationID;

                    $transaction->status = TransactionStatus::REVERSING;
                    $transaction->result_description = $response->ResponseDescription;
                    $transaction->reversed_by = $initiatedBy;
                    $transaction->save();

                    $transaction->payload->update([
                        "conversation_id" => $response->ConversationID,
                        "original_conversation_id" => $response->OriginatorConversationID
                    ]);
                } catch (\Throwable $th) {
                    $payment_results_status = "FAILED";
                    $payment_results_desc = isset($response->ResultDesc) ? $response->ResultDesc : $response->errorMessage;
                }

                $client_id = ($transaction->service_id) ? $transaction->service->client_id : NULL;
                log_transaction($transaction->account->id, TransactionType::REVERSAL, $client_id, $transaction->service_id, floor($transaction->disbursed_amount), $payment_results_status, $payment_results_desc, $transaction->order_number . ' - ' . $transaction->account_number, $initiatedBy);
            }
        }
    }

    private function performTransaction($transaction)
    {
        $account = $transaction->account;
        $mpesa = new Mpesa($account->account_number, $account->consumer_key, $account->consumer_secret, $account->api_username, $account->api_password);
        return $mpesa->reverse($transaction->receipt_number, $transaction->disbursed_amount, $transaction->service->account->account_number, 'Please', route('trx_reversal_result_url'), route('trx_reversal_timeout_url'), "Work");
    }
}
