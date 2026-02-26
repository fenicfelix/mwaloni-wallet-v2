<?php

namespace Wallet\Core\Services;

use Wallet\Core\Http\Enums\TransactionStatus;
use Wallet\Core\Models\Transaction;
use Wallet\Core\Repositories\TransactionRepository;

class ProcessMomoStatus
{
    protected $successfulStatusLabel = 'SUCCESSFUL';

    public function process($json)
    {
        $transactionId = $json['externalId'] ?? null;

        try {
            $transaction = Transaction::with(["account", "payload"])->where("order_number", $transactionId)->first();

            // Check if the transaction exists
            if (!$transaction) {
                return;
            }

            // Process the payment status
            if ($json['status'] === $this->successfulStatusLabel) {
                info("Momo Payment Status Query Successful");
                $updateData = [
                    "status" => TransactionStatus::SUCCESS,
                    "receipt_number" => $json["financialTransactionId"],
                    "result_description" => $json["status"],
                    "completed_at" => now()
                ];
            } else {
                info("Momo Payment Status Query Unsuccessful");
                $updateData = [
                    "status" => TransactionStatus::FAILED,
                    "result_description" => $json["reason"],
                    "completed_at" => now()
                ];
            }

            $payloadData = [
                "raw_callback" => json_encode($json)
            ];

            $transactionRepository = app(TransactionRepository::class);
            $transactionRepository->updateWithPayload($transaction->id, $updateData, $payloadData);
            if ($json['status'] === $this->successfulStatusLabel) {
                // Dispatch a job to handle successful payment
                // complete the payment process
                $transactionRepository->completeTransaction($transaction->id);
            }

            return true;
        } catch (\Throwable $th) {
            //throw $th;
            return false;
        }
    }
}
