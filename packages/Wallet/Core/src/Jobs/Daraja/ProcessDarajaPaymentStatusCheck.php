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
        info('ProcessDarajaPaymentStatusCheck started');
        $transaction = Transaction::with(["account", "payload"])->find($this->id);
        if (!$transaction) {
            info("Transaction not found: {$this->id}");
            return;
        }

        $response = json_decode($this->performTransaction($transaction));
        info("ProcessDarajaPaymentStatusCheck: " . json_encode($response));

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

            app(TransactionRepository::class)->updateWithPayload(
                $transaction->id,
                $updateData,
                $payloadData
            );
        }
    }

    private function performTransaction(Transaction $transaction): ?string
    {
        info("ProcessDarajaPaymentStatusCheck: Performing transaction status check for transaction: {$transaction->order_number}");
        $account = $transaction->account;
        $receiptNumber = $transaction->receipt_number;
        $remarks = $transaction->description;
        $originalConversationId = $transaction->payload?->original_conversation_id;

        $mpesa = new Mpesa(
            $account->account_number,
            $account->consumer_key,
            $account->consumer_secret,
            $account->api_username,
            $account->api_password
        );
        $response = $mpesa->getTransactionStatus($receiptNumber, "shortcode", $remarks, route('trx_status_result_url', ['id' => $transaction->identifier]), route('trx_status_timeout_url'), $originalConversationId);

        info('PAYMENT_RESPONSE: ' . $response);

        return $response;
    }
}
