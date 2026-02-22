<?php

namespace Wallet\Core\Services;

use Wallet\Core\Http\Enums\TransactionStatus;
use Wallet\Core\Models\Transaction;
use Wallet\Core\Repositories\TransactionRepository;

class ProcessMomoStatus
{
    protected $successfulStatusLabel = 'SUCCESSFUL';

    public function process($transactionId, $json)
    {
        try {
            $successfulStatusLabel = 'SUCCESSFUL';
            $transaction = Transaction::with(["account", "payload"])->where("id", "=", $transactionId)->first();

            // Check if the transaction exists
            if (!$transaction) {
                return;
            }

            /*
            $status = [
                "amount" => "6.00",
                "currency" => "EUR",
                "financialTransactionId" => "554662921",
                "externalId" => "01KHXC2B1JQTKN16T8XHHTZX6A",
                "payee" => [
                    "partyIdType" => "MSISDN",
                    "partyId" => "0296631315"
                ],
                "payeeNote" => "Reprehenderit sequi fugiat ipsam sed.",
                "status" => "SUCCESSFUL"
            ];
            */

            // Process the payment status
            if ($json['status'] === $successfulStatusLabel) {
                info("Momo Payment Status Query Successful");
                $updateData = [
                    "status" => TransactionStatus::SUCCESS,
                    "receipt_number" => $json["externalId"],
                    "result_description" => $json["status"],
                    "completed_at" => date('Y-m-d H:i:s')
                ];
            } else {
                info("Momo Payment Status Query Unsuccessful");
                $updateData = [
                    "status" => TransactionStatus::FAILED,
                    /// ....Any other fields to update
                    "completed_at" => date('Y-m-d H:i:s')
                ];
            }

            $payloadData = [
                "raw_callback" => json_encode($json)
            ];

            $transactionRepository = app(TransactionRepository::class);
            $transactionRepository->updateWithPayload($transaction->id, $updateData, $payloadData);
            if ($json['status'] === $successfulStatusLabel) {
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
