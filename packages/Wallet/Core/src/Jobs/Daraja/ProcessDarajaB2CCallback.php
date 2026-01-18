<?php

namespace Wallet\Core\Jobs\Daraja;

use App\Http\Traits\MwaloniWallet;
use App\Jobs\PushTransactionCallback;
use Wallet\Core\Models\Transaction;
use Wallet\Core\Models\TransactionLog;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessDarajaB2CCallback implements ShouldQueue
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
        $log_status = "";
        $log_status_description = "";
        $completed_at = date("Y-m-d H:i:s");
        $successMessage = "";
        $successMessageTo = getOption("DARAJA-ALERT-CONTACT");
        $transaction = Transaction::with(["account", "service", "payload"])->where("identifier", $this->transactionId)->first();

        if ($transaction && $transaction->status_id != 2) {
            $account = $transaction->account;
            $transaction->payload->update([
                "raw_callback" => json_encode($this->json)
            ]);
            $transaction->receipt_number = $this->json["Result"]["TransactionID"];
            $transaction->result_description = $this->json["Result"]["ResultDesc"];

            if ($this->json["Result"]["ResultCode"] == 0) {
                $transaction->status_id = 2;
                $log_status = "SUCCESS";
                $log_status_description = $this->json["Result"]["TransactionID"];

                $successMessage = getOption("SMS-B2C-SUCCESS-MESSAGE");
                $successMessage = str_replace("{order_number}", $transaction->order_number, $successMessage);
                $successMessage = (isset($transaction->service)) ? str_replace("{service}", $transaction->service->name, $successMessage) : str_replace("{service}", $transaction->account->name, $successMessage);

                if (isset($this->json["Result"]["ResultParameters"])) {
                    foreach ($this->json["Result"]["ResultParameters"]["ResultParameter"] as $parameter) {
                        if ($parameter["Key"] == "TransactionReceipt") $successMessage = str_replace('{receipt_number}', $parameter["Value"], $successMessage);
                        if ($parameter["Key"] == "TransactionAmount") $successMessage = str_replace('{amount}', number_format($parameter["Value"], 2), $successMessage);
                        if ($parameter["Key"] == "ReceiverPartyPublicName") {
                            $array = explode(" - ", $parameter["Value"]);
                            $transaction->account_name = $array[1];
                            $successMessage = str_replace('{to}', ucwords(strtolower($parameter["Value"])), $successMessage);
                            $log_status_description .= " - " . $parameter["Value"];
                        }
                        if ($parameter["Key"] == "TransactionCompletedDateTime") {
                            $completed_at = date("Y-m-d H:i:s", strtotime($parameter["Value"]));
                            $transaction->completed_at = $completed_at;
                            $successMessage = str_replace('{datetime}', date("Y-m-d", strtotime($completed_at)) . " at " . date("H:i:s", strtotime($completed_at)), $successMessage);
                        }
                        if ($parameter["Key"] == "B2CUtilityAccountAvailableFunds") {
                            $account->utility_balance = $parameter["Value"];
                            $successMessage = str_replace('{balance}', number_format($parameter["Value"], 2), $successMessage);
                        }
                        if ($parameter["Key"] == "B2CWorkingAccountAvailableFunds") $account->working_balance = $parameter["Value"];
                    }
                }
            } else {
                $transaction->status_id = 3;
                $transaction->completed_at = $completed_at;

                $log_status = "FAILED";
                $log_status_description = $this->json["Result"]["ResultDesc"];

                $successMessage = getOption("DARAJA-ERROR-MESSAGE");
                $successMessage = str_replace('{account}', $account->name, $successMessage);
                $successMessage = str_replace('{error}', $this->json["Result"]["ResultDesc"], $successMessage);
                $successMessage = str_replace('{transaction}', $transaction->order_number, $successMessage);
            }

            $transaction->save();
            $account->save();

            if (isset($transaction->service) && $transaction->service->callback_url != NULL) {
                $data = (object) $this->json["Result"];
                $data->orderNumber = $transaction->order_number;
                PushTransactionCallback::dispatch($data, $transaction->service->callback_url)->onQueue("push-callback");
            }

            $log = TransactionLog::where("status_description", "=", $this->json["Result"]["ConversationID"])->first();
            if ($log) {
                $log->status = $log_status;
                $log->status_description = $log_status_description;
                $log->save();
            }

            //Send SMS
            $this->sendSMS($successMessageTo, $successMessage);
        }
    }
}
