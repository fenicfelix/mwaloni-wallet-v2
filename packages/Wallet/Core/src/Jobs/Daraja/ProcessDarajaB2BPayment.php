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
use Wallet\Core\Http\Traits\MwaloniWallet;
use Wallet\Core\Repositories\TransactionRepository;

class ProcessDarajaB2BPayment implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    use MwaloniWallet;

    public int $tries = 1;

    protected int $transactionId;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(int $transactionId)
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
        
        $response = $this->performTransaction($transaction);
        if ($response) {
            // Convert to object
            $response = (object) $response;

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
                    "result_description" => $response->ResultDesc
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
            $updateData ?? [],
            $payloadData ?? []
        );
    }

    private function performTransaction(Transaction $transaction): ?array
    {
        $account = $transaction->account;
        if (! $account) {
            return [];
        }

        $isTillNumber = false;
        if ($transaction->payment_channel_id == 3) {
            $isTillNumber = true;
        }

        $response = Mpesa::using($this->getDarajaCredentials($account))
            ->b2b()
            ->send(
                toPaybill: $isTillNumber ? false : true,
                receiverShortCode: $transaction->account_number,
                amount: floor($transaction->disbursed_amount),
                resultUrl: route('b2b_result_url', $transaction->identifier),
                queueTimeoutUrl: route('b2b_timeout_url'),
                remarks: $transaction->description,
                accountReference: ($transaction->account_reference) ? $transaction->account_reference : $transaction->order_number
            );

        return $response;
    }
}
