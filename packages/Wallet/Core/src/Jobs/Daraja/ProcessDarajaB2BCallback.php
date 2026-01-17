<?php

namespace App\Jobs\Daraja;

use App\Http\Traits\MwaloniWallet;
use App\Jobs\PushTransactionCallback;
use Wallet\Core\Models\Transaction;
use Wallet\Core\Models\TransactionLog;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessDarajaB2BCallback implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    use MwaloniWallet;

    protected $transactionId;
    protected $json;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($transactionId, $json)
    {
        $this->transactionId = $transactionId;
        $this->json = $json;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $balance = NULL;
        $completed_at = date("Y-m-d H:i:s");
        $log_status = "";
        $log_status_description = "";
        $successMessage = "";
        $successMessageTo = getOption("DARAJA-ALERT-CONTACT");
        $transaction = Transaction::with(["account", "service", "payload"])->where("identifier", $this->transactionId)->first();

        if ($transaction && $transaction->status_id != Transaction::STATUS_SUCCESS) {
            $account = $transaction->account;
            $transaction->receipt_number = $this->json["Result"]["TransactionID"];
            $transaction->result_description = $this->json["Result"]["ResultDesc"];

            if ($this->json["Result"]["ResultCode"] == 0) {
                $transaction->status_id = Transaction::STATUS_SUCCESS;
                $log_status = "SUCCESS";
                $log_status_description = $this->json["Result"]["TransactionID"];

                $successMessage = getOption("SMS-B2B-SUCCESS-MESSAGE");
                $successMessage = str_replace("{service}", ($transaction->service) ? $transaction->service->name : "Payment", $successMessage);
                $successMessage = str_replace("{receipt_number}", $this->json["Result"]["TransactionID"], $successMessage);

                foreach ($this->json["Result"]["ResultParameters"]["ResultParameter"] as $parameter) {
                    if ($parameter["Key"] == "Amount") $successMessage = str_replace('{amount}', number_format($parameter["Value"], 2), $successMessage);
                    if ($parameter["Key"] == "ReceiverPartyPublicName") {
                        $array = explode(" - ", $parameter["Value"]);
                        $transaction->account_name = $array[1];
                        $successMessage = str_replace('{to}', ucwords(strtolower($parameter["Value"])), $successMessage);
                        $log_status_description .= " - " . $parameter["Value"];
                    }
                    if ($parameter["Key"] == "TransCompletedTime") {
                        $completed_at = date("Y-m-d H:i:s", strtotime($parameter["Value"]));
                        $transaction->completed_at = $completed_at;
                        $successMessage = str_replace('{datetime}', date("Y-m-d", strtotime($completed_at)) . " at " . date("H:i:s", strtotime($completed_at)), $successMessage);
                    }
                    if ($parameter["Key"] == "DebitPartyAffectedAccountBalance") {
                        $balance = get_balance($parameter["Value"], "Working Account");
                        $successMessage = str_replace('{balance}', number_format($balance), $successMessage);
                    }
                }
            } else {
                $transaction->status_id = Transaction::STATUS_FAILED;
                $transaction->completed_at = $completed_at;

                $log_status = "FAILED";
                $log_status_description = $this->json["Result"]["ResultDesc"];

                $successMessage = getOption("DARAJA-ERROR-MESSAGE");
                $successMessage = str_replace('{error}', $this->json["Result"]["ResultDesc"], $successMessage);
                $successMessage = str_replace('{transaction}', $transaction->order_number, $successMessage);
            }

            $transaction->save();

            $transaction->payload->update([
                "raw_callback" => json_encode($this->json)
            ]);

            //Save account balance
            if ($this->json["Result"]["ResultCode"] == 0 && $balance) {
                $account->utility_balance = $balance;
                $account->save();
            }

            $log = TransactionLog::where("status_description", "=", $this->json["Result"]["ConversationID"])->first();
            if ($log) {
                $log->status = $log_status;
                $log->status_description = $log_status_description;
                $log->save();
            }

            if (isset($transaction->service) && $transaction->service->callback_url != NULL) {
                $data = (object) $this->json["Result"];
                $data->orderNumber = $transaction->order_number;
                PushTransactionCallback::dispatch($data, $transaction->service->callback_url);
            }

            //Send SMS
            $this->sendSMS($successMessageTo, $successMessage);
        }
    }
}
