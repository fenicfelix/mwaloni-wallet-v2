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

class ProcessDarajaB2CPayment implements ShouldQueue
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
        $transaction = Transaction::with(["service", "account", "payload"])->where("id", "=", $this->transactionId)->first();

        if (! $transaction) {
            // Ignore the job
            return;
        }

        try {
            $this->performTransaction($transaction->identifier, "BusinessPayment", $transaction->account_number, floor($transaction->disbursed_amount), $transaction->description, NULL, $transaction->account);

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

        // info('PAYMENT_RESPONSE: ' . json_encode($response));
        /*if ($response) {
            $updateData = [];
            $payloadData = [
                'raw_callback' => json_encode($response)
            ];

            try {
                $updateData = [
                    "status" => TransactionStatus::SUBMITTED,
                    "result_description" => $response->ResponseDescription
                ];

                info('PAYMENT_UPDATE: ' . json_encode($updateData));

                $payloadData = [
                    "conversation_id" => $response->ConversationID,
                    "original_conversation_id" => $response->OriginatorConversationID
                ];

                info('PAYMENT_PAYLOAD: ' . json_encode($payloadData));
            } catch (\Throwable $th) {
                $updateData = [
                    "status" => TransactionStatus::FAILED,
                    "result_description" => isset($response->ResultDesc) ? $response->ResultDesc : $th->getMessage()
                ];
            }
        } else {
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

    private function performTransaction($transactionID, $commandID, $msisdn, $amount, $remarks, $ocassion, $account): void
    {
        $mpesa = new Mpesa($account->account_number, $account->consumer_key, $account->consumer_secret, $account->api_username, $account->api_password);

        $mpesa->b2cTransaction($commandID, $msisdn, $amount, $remarks, route('b2c_result_url', $transactionID), route('b2c_timeout_url'), $ocassion);

        // info('PAYMENT_RESPONSE: ' . json_encode($response));

        // return $response;
    }
}
