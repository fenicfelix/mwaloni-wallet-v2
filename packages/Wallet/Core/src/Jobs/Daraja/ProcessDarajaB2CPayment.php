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

        $response = $this->performTransaction($transaction->identifier, "BusinessPayment", $transaction->account_number, floor($transaction->disbursed_amount), $transaction->description, NULL, $transaction->account);
        info('PAYMENT_RESPONSE: ' . json_encode($response));
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
        );
    }

    private function performTransaction(
        $transactionID,
        $commandID,
        $msisdn,
        $amount,
        $remarks,
        $ocassion,
        $account
    ): ?object {
        $mpesa = new Mpesa(
            $account->account_number,
            $account->consumer_key,
            $account->consumer_secret,
            $account->api_username,
            $account->api_password
        );

        $response = $mpesa->b2cTransaction(
            $commandID,
            $msisdn,
            $amount,
            $remarks,
            route('b2c_result_url', $transactionID),
            route('b2c_timeout_url'),
            $ocassion
        );

        // Case 1: SDK already returns decoded object
        if (is_object($response)) {
            info('PAYMENT_RESPONSE_1: ' . json_encode($response));
            return $response;
        }

        // Case 2: Laravel HTTP client response
        if ($response instanceof \Illuminate\Http\Client\Response) {
            info('PAYMENT_RESPONSE_2: ' . json_encode($response->object()));
            return $response->object(); // stdClass
        }

        // Case 3: PSR-7 response
        if ($response instanceof \Psr\Http\Message\ResponseInterface) {
            info('PAYMENT_RESPONSE_3: ' . json_encode(json_decode((string) $response->getBody())));
            return json_decode((string) $response->getBody());
        }

        // Case 4: Raw JSON string
        if (is_string($response)) {
            info('PAYMENT_RESPONSE_4: ' . $response);
            return json_decode($response);
        }

        return null;
    }
}
