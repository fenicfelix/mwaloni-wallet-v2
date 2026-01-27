<?php

namespace Wallet\Core\Jobs\Daraja;

use Akika\LaravelMpesaMultivendor\Mpesa;
use Wallet\Core\Models\Transaction;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Wallet\Core\Http\Enums\TransactionStatus;
use Wallet\Core\Repositories\TransactionRepository;

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

        /// Only update the transaction if status has been queried successfully
        if ($response && isset($response->ResponseCode) && $response->ResponseCode == 0) {
            $updateData = [
                "status" => TransactionStatus::QUERYING_STATUS,
                "result_description" => $response->ResponseDescription
            ];

            $payloadData = [
                'raw_callback' => json_encode($response),
                "conversation_id" => $response->ConversationID,
                "original_conversation_id" => $response->OriginatorConversationID
            ];

            app(TransactionRepository::class)->updateTransactionAndPayload(
                $transaction->id,
                $updateData,
                $payloadData
            );
        }
    }

    private function performTransaction($transaction)
    {
        $account = $transaction->account;
        $mpesa = new Mpesa($account->account_number, $account->consumer_key, $account->consumer_secret, $account->api_username, $account->api_password);
        $promise = $mpesa->getTransactionStatus($transaction->receipt_number, "shortcode", $transaction->description, route('trx_status_result_url', $transaction->identifier), route('trx_status_timeout_url'), $transaction->original_conversation_id);

        $response = $promise->wait(); // 🔑 IMPORTANT
        return $response;
    }
}
