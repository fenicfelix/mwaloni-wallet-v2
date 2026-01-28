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
use Wallet\Core\Http\Enums\TransactionType;
use Wallet\Core\Repositories\TransactionRepository;

class ProcessDarajaB2BPayment implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 1;

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
        info('ProcessDarajaB2BPayment: ' . $this->transactionId);
        $transaction = Transaction::with(["service", "account", "payload"])->where("id", "=", $this->transactionId)->first();
        if (! $transaction) {
            // Ignore the job
            return;
        }

        $isTillNumber = false;
        if ($transaction->payment_channel_id == 3) {
            $isTillNumber = true;
        }

        try {
            $account_reference = ($transaction->account_reference) ? $transaction->account_reference : $transaction->order_number;
            $this->performTransaction($transaction->identifier, $transaction->account_number, floor($transaction->disbursed_amount), $transaction->description, $account_reference, $transaction->account, $isTillNumber);
            $updateData = [
                "status" => TransactionStatus::SUBMITTED,
                "result_description" => "Transaction submitted successfully"
            ];

            app(TransactionRepository::class)->update(
                $transaction->id,
                $updateData
            );
        } catch (\Throwable $th) {
            //throw $th;
        }
        
        /*info('ProcessDarajaB2BPayment: ' . $this->transactionId . ' RESPONSE' . json_encode($response));
        if ($response) {
            $updateData = [];
            $payloadData = [
                'raw_callback' => json_encode($response)
            ];

            try {
                info('ProcessDarajaB2BPayment: ' . $this->transactionId . ' SUBMITTED');
                $updateData = [
                    "status" => TransactionStatus::SUBMITTED,
                    "result_description" => $response->ResponseDescription
                ];
                $payloadData = [
                    "conversation_id" => $response->ConversationID,
                    "original_conversation_id" => $response->OriginatorConversationID
                ];
            } catch (\Throwable $th) {
                info('ProcessDarajaB2BPayment: ' . $this->transactionId . ' ERROR: ' . $th->getMessage());
                $updateData = [
                    "status" => TransactionStatus::FAILED,
                    "result_description" => $response->ResultDesc
                ];
            }
        } else {
            info('ProcessDarajaB2BPayment: ' . $this->transactionId . ' FAILED');
            //Ignore the job
            $updateData = [
                "status" => TransactionStatus::FAILED
            ];
        }

        app(TransactionRepository::class)->updateWithPayload(
            $transaction->id,
            $updateData,
            $payloadData
        );*/
    }

    private function performTransaction($transactionID, $destShortcode, $amount, $remarks, $accountRef, $account, $isTillNumber): void
    {
        $mpesa = new Mpesa($account->account_number, $account->consumer_key, $account->consumer_secret, $account->api_username, $account->api_password);
        if ($isTillNumber) {
            $mpesa->b2bBuyGoods($destShortcode, $amount, $remarks, $accountRef, route('b2b_result_url', $transactionID), route('b2b_timeout_url'));
        } else {
            $mpesa->b2bPaybill($destShortcode, $amount, $remarks, $accountRef, route('b2b_result_url', $transactionID), route('b2b_timeout_url'));
        }

        // info('PAYMENT_RESPONSE: ' . json_encode($response));

        // return $response;
    }
}
