<?php

namespace Wallet\Core\Jobs;

use Wallet\Core\Models\Transaction;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class ProcessOfflinePayments implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $identifier;
    protected $receipt_number;
    protected $recepient_name;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($identifier, $receipt_number, $recepient_name)
    {
        $this->identifier = $identifier;
        $this->receipt_number = $receipt_number;
        $this->recepient_name = $recepient_name;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $transaction = Transaction::with("service", "payload")->where("identifier", "=", $this->identifier)->first();
        if ($transaction) {
            $description = "The service request was processed Offline";
            $callback = [
                "Result" => [
                    "ResultType" => 0,
                    "ResultCode" => 0,
                    "ResultDesc" => $description,
                    "OriginatorConversationID" => $transaction->payload?->original_conversation_id,
                    "ConversationID" => $transaction->payload?->conversation_id,
                    "TransactionID" => $this->receipt_number,
                    "ResultParameters" => [
                        [
                            "Key" => "TransactionAmount",
                            "Value" => $transaction->disbursed_amount
                        ], [
                            "Key" => "TransactionReceipt",
                            "Value" => $this->receipt_number
                        ], [
                            "Key" => "ReceiverPartyPublicName",
                            "Value" => $transaction->phone_number . " - " . $this->recepient_name
                        ], [
                            "Key" => "TransactionCompletedDateTime",
                            "Value" => date('d.m.Y H:i:s')
                        ], [
                            "Key" => "B2CUtilityAccountAvailableFunds",
                            "Value" => 0
                        ], [
                            "Key" => "2CWorkingAccountAvailableFunds",
                            "Value" => 0
                        ], [
                            "Key" => "B2CRecipientIsRegisteredCustomer",
                            "Value" => 'Y'
                        ], [
                            "Key" => "B2CChargesPaidAccountAvailableFunds",
                            "Value" => "0"
                        ]
                    ],
                    "ReferenceData" => [
                        "ReferenceItem" => [
                            "Key" => "QueueTimeoutURL",
                            "Value" => "http://internalapi.safaricom.co.ke\/mpesa\/b2cresults\/v1\/submit"
                        ]
                    ]
                ]
            ];

            $process = DB::transaction(function () use ($transaction, $description, $callback) {
                $transaction->receipt_number = $this->receipt_number;
                $transaction->account_name = $this->recepient_name;
                $transaction->status_id = 2;
                $transaction->result_description = $description;
                $transaction->completed_at = date('Y-m-d H:i:s');

                if (!$transaction->save()) return false;

                $transaction->payload->update([
                    "raw_callback" => json_encode($callback)
                ]);

                return true;
            }, 2);

            if ($process) {
                if ($transaction->service->callback_url != NULL) {
                    $data = (object) $callback["Result"];
                    $data->orderNumber = $transaction->order_number;
                    PushTransactionCallback::dispatch($data, $transaction->service->callback_url);
                }
            }
        }
    }
}
