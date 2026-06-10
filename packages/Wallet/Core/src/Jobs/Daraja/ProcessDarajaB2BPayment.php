<?php

namespace Wallet\Core\Jobs\Daraja;

use Akika\LaravelMpesaMultivendor\Mpesa;
use Wallet\Core\Models\Transaction;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Wallet\Core\Http\Enums\TransactionStatus;
use Wallet\Core\Http\Traits\MwaloniWallet;
use Wallet\Core\Repositories\TransactionRepository;


class ProcessDarajaB2BPayment implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    use MwaloniWallet;

    public int $tries = 1;

    public function __construct(
        protected int $transactionId
    ) {}

    public function handle(TransactionRepository $transactions): void
    {
        $transaction = Transaction::query()
            ->with(['account'])
            ->find($this->transactionId);

        if (! $transaction) {
            return;
        }

        if (! $transaction->account) {
            $transactions->update($transaction->id, [
                'status' => TransactionStatus::FAILED,
                'result_description' => 'Transaction account not found.',
            ]);

            return;
        }

        $response = $this->performTransaction($transaction);

        $updateData = [
            'status' => TransactionStatus::FAILED,
            'result_description' => 'No response received from Daraja.',
        ];

        $payloadData = [];

        if ($response) {
            $payloadData['raw_callback'] = json_encode($response);

            if (isset($response['ResponseCode']) && $response['ResponseCode'] === '0') {
                $updateData = [
                    'status' => TransactionStatus::SUBMITTED,
                    'result_description' => $response['ResponseDescription'] ?? 'Submitted successfully.',
                ];

                $payloadData['conversation_id'] = $response['ConversationID'] ?? null;
                $payloadData['original_conversation_id'] = $response['OriginatorConversationID'] ?? null;
            } else {
                $updateData = [
                    'status' => TransactionStatus::FAILED,
                    'result_description' => $response['ResponseDescription']
                        ?? $response['ResultDesc']
                        ?? 'Daraja request failed.',
                ];
            }
        }

        $transactions->updateWithPayload(
            $transaction->id,
            $updateData,
            array_filter($payloadData, fn ($value) => $value !== null)
        );
    }

    private function performTransaction(Transaction $transaction): ?array
    {
        try {
            $mpesa = Mpesa::using(
                $this->getDarajaCredentials($transaction->account)
            )->b2b();

            $commonPayload = [
                'receiverShortCode' => $transaction->account_number,
                'amount' => floor($transaction->disbursed_amount),
                'resultUrl' => route('b2b_result_url', $transaction->identifier),
                'queueTimeoutUrl' => route('b2b_timeout_url'),
                'remarks' => $transaction->description,
            ];

            if ((int) $transaction->payment_channel_id === 3) {
                return $mpesa->buyGoods(...$commonPayload);
            }

            return $mpesa->paybill(
                ...$commonPayload,
                accountReference: $transaction->account_reference ?: $transaction->order_number,
            );
        } catch (\Throwable $e) {
            Log::error('Daraja B2B payment request failed', [
                'transaction_id' => $transaction->id,
                'message' => $e->getMessage(),
            ]);

            return [
                'ResponseCode' => '1',
                'ResponseDescription' => $e->getMessage(),
            ];
        }
    }
}
