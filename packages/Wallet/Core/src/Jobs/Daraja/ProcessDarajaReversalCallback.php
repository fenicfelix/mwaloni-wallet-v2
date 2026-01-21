<?php

namespace Wallet\Core\Jobs\Daraja;

use Wallet\Core\Http\Traits\MwaloniWallet;
use Wallet\Core\Models\Transaction;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Wallet\Core\Http\Enums\TransactionStatus;
use Wallet\Core\Repositories\TransactionRepository;

class ProcessDarajaReversalCallback implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    use MwaloniWallet;

    protected $json;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($json)
    {
        $this->json = $json;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $completed_at = date("Y-m-d H:i:s");
        $successMessage = "";

        // get transaction where payload->conversation_id is $this->json["Result"]["ConversationID"])
        $transaction = Transaction::with(["service.account", "payload"])
            ->whereHas("payload", function ($query) {
                $query->where("conversation_id", $this->json["Result"]["ConversationID"]);
            })
            ->first();

        if ($transaction) {
            $payloadData = [
                "raw_callback" => json_encode($this->json)
            ];
            $updateData = [
                'result_description' => $this->json["Result"]["ResultDesc"]
            ];

            if ($this->json["Result"]["ResultCode"] == 21) {
                $updateData['status'] = TransactionStatus::REVERSED;

                $successMessage = getOption("SMS-B2C-REVERSAL-MESSAGE");
                $successMessage = str_replace("{trxid}", $this->json["Result"]["TransactionID"], $successMessage);
                $successMessage = str_replace("{receipt_number}", $transaction->receipt_number, $successMessage);

                foreach ($this->json["Result"]["ResultParameters"]["ResultParameter"] as $parameter) {
                    if ($parameter["Key"] == "TransCompletedTime") {
                        $completed_at = date("Y-m-d H:i:s", strtotime($parameter["Value"]));
                        $updateData['reversed_on'] = $completed_at;
                        $successMessage = str_replace('{datetime}', date("Y-m-d", strtotime($completed_at)) . " at " . date("H:i:s", strtotime($completed_at)), $successMessage);
                    }
                    if ($parameter["Key"] == "Amount") {
                        $successMessage = str_replace('{amount}', number_format($parameter["Value"], 2), $successMessage);
                    }
                }
            } else {
                $updateData['status'] = TransactionStatus::REVERSING_FAILED;
                $successMessage = getOption("DARAJA-ERROR-MESSAGE");
                $successMessage = str_replace('{error}', $this->json["Result"]["ResultDesc"], $successMessage);
            }

            $updateData['completed_at'] = $completed_at;

            app(TransactionRepository::class)->updateTransactionAndPayload($transaction->id, $updateData, $payloadData);

            //Send SMS
            if ($successMessage != "") {
                $successMessageTo = getOption("DARAJA-ALERT-CONTACT");
                $this->sendSMS($successMessageTo, $successMessage);
            }
        }
    }
}
