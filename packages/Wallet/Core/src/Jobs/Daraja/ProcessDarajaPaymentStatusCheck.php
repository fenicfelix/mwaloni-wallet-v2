<?php

namespace App\Jobs\Daraja;

use Akika\LaravelMpesaMultivendor\Mpesa;
use Wallet\Core\Models\Transaction;
use Wallet\Core\Models\TransactionLog;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessDarajaPaymentStatusCheck implements ShouldQueue
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
        $transaction = Transaction::with(["account", "payload"])->where("identifier", "=", $this->id)->first();
        $response = json_decode($this->performTransaction($transaction));
        $originalConversationID = $transaction->payload?->conversation_id;

        /// Only update the transaction if status has been queried successfully
        if ($response) {
            if (isset($response->ResponseCode) && $response->ResponseCode == 0) {
                $transaction->status_id = Transaction::STATUS_QUERING_STATUS;

                $log = TransactionLog::where("status_description", "=", $originalConversationID)->first();
                if ($log) {
                    $log->status_description = $response->ConversationID;
                    $log->save();
                }

                $transaction->save();

                $transaction->payload->update([
                    "conversation_id" => $response->ConversationID,
                    "original_conversation_id" => $response->OriginatorConversationID
                ]);
            }
        }
    }

    private function performTransaction($transaction)
    {
        $account = $transaction->account;
        $mpesa = new Mpesa($account->account_number, $account->consumer_key, $account->consumer_secret, $account->api_username, $account->api_password);
        return $mpesa->getTransactionStatus($transaction->receipt_number, "shortcode", $transaction->description, route('trx_status_result_url'), route('trx_status_timeout_url'), $transaction->original_conversation_id);
    }
}
