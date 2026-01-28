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

        $response = json_decode($this->performTransaction($transaction->identifier, "BusinessPayment", $transaction->account_number, floor($transaction->disbursed_amount), $transaction->description, NULL, $transaction->account));
        if ($response) {
            $updateData = [];
            $payloadData = [
                'raw_callback' => json_encode($response)
            ];

            try {
                $updateData = [
                    "status" => TransactionStatus::SUBMITTED,
                    "result_description" => $response->ResponseDescription
                ];

                $payloadData = [
                    "conversation_id" => $response->ConversationID,
                    "original_conversation_id" => $response->OriginatorConversationID
                ];
            } catch (\Throwable $th) {
                $updateData = [
                    "status" => TransactionStatus::FAILED,
                    "result_description" => isset($response->ResultDesc) ? $response->ResultDesc : $response->errorMessage
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
        );
    }



    private function performTransaction($transactionID, $commandID, $msisdn, $amount, $remarks, $ocassion, $account): ?string
    {
        $mpesa = new Mpesa($account->account_number, $account->consumer_key, $account->consumer_secret, $account->api_username, $account->api_password);
        $response = $mpesa->b2cTransaction($commandID, $msisdn, $amount, $remarks, route('b2c_result_url', $transactionID), route('b2c_timeout_url'), $ocassion);

        /**
         * At this point $response is already an HTTP response,
         * NOT a Promise
         */
        if (is_string($response)) {
            return $response;
        }

        if ($response instanceof \Illuminate\Http\Client\Response) {
            return $response->object(); // Laravel HTTP client
        }

        if ($response instanceof \Psr\Http\Message\ResponseInterface) {
            return json_decode((string) $response->getBody());
        }

        return null;
    }
}
