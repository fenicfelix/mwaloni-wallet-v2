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

class ProcessDarajaPaymentStatusCheck implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    use MwaloniWallet;

    protected int $id;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(int $id)
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
        $transaction = Transaction::with(["account", "payload"])->find($this->id);
        if (!$transaction) {
            return;
        }

        $response = (object) $this->performTransaction($transaction);

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
                $updateData ?? [],
                $payloadData ?? []
            );
        }
    }

    private function performTransaction(Transaction $transaction): ?array
    {
        $account = $transaction->account;
        if (! $account) {
            return [];
        }

        $response = Mpesa::using($this->getDarajaCredentials($account))
            ->status()
            ->query(
                transactionId: $transaction->receipt_number,
                originalConversationId: $transaction->payload?->original_conversation_id,
                resultUrl: route('trx_status_result_url', ['id' => $transaction->identifier]),
                queueTimeoutUrl: route('trx_status_timeout_url'),
            );

        return $response;
    }
}
