<?php

namespace Wallet\Core\Jobs\Momo;

use Akika\MoMo\Enums\Currency;
use Akika\MoMo\Facades\MoMo;
use Wallet\Core\Models\Transaction;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Wallet\Core\Http\Enums\TransactionStatus;
use Wallet\Core\Repositories\TransactionRepository;

class ProcessMomoPayment implements ShouldQueue
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
        // Handle the MoMo payment processing logic here
        $transaction = Transaction::with(["account", "payload"])->where("id", "=", $this->transactionId)->first();

        // Check if the transaction exists
        if (!$transaction) {
            return;
        }

        try {

            /// Check if the transaction has been submitted
            $result = json_decode($this->submitTransaction($transaction), true);
            if (!$result) {
                return;
            }

            $updateData = [
                "status" => TransactionStatus::SUBMITTED,
                "result_description" => "Payment submitted at " . now(),
            ];

            $payloadData = [
                "raw_callback" => $result,
                "conversation_id" => $result,
            ];
        } catch (\Throwable $th) {
            info("MoMo Payment Exception: " . $th->getMessage());
            $updateData = [
                "status" => TransactionStatus::FAILED,
                "result_description" => $th->getMessage(),
                "completed_at" => now()
            ];

            $payloadData = [
                "raw_callback" => json_encode($th)
            ];
        }

        app(TransactionRepository::class)->updateWithPayload($transaction->id, $updateData, $payloadData);
    }

    private function submitTransaction($transaction): ?string
    {
        // Prepare payload
        $account = $transaction->account;
        $payload = json_decode($transaction->payload?->trx_payload, true);

        /*
        ?string $secondaryKey = null,
        ?string $userReferenceId = null,
        ?string $apiKey = null,
        */

        /*
        API user: 7aa983ae-7910-4ead-be74-94d62225e33d
        API key: ab3da92ccee44bac851cf4cd8dafa638

        Primary key
        dc865af95890410d85efeed378e5e570

        Secondary key
        9dc1dfaa42cb402cb29a785226405f62
        */

        $referenceId = MoMo::with(
            secondaryKey: "9dc1dfaa42cb402cb29a785226405f62",
            userReferenceId: "7aa983ae-7910-4ead-be74-94d62225e33d",
            apiKey: "ab3da92ccee44bac851cf4cd8dafa638"
        )->disbursement()->transfer(
            $payload['amount'],
            Currency::UgandaShilling,
            externalId: $payload['externalId'] ?? $transaction->order_number,
            payeeMsisdn: $payload['payeeMsisdn'],
            payerMessage: $payload['payerMessage'],
            payeeNote: $payload['payeeNote'],
        );

        return $referenceId;
    }
}
