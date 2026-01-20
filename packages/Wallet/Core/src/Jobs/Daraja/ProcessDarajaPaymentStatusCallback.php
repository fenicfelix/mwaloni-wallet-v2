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
use Wallet\Core\Jobs\PushTransactionCallback;

class ProcessDarajaPaymentStatusCallback implements ShouldQueue
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
        $successMessage = "";
        $successMessageTo = getOption("DARAJA-ALERT-CONTACT");

        $transaction = Transaction::with(["account", "service", "payload"])->where("identifier", $this->transactionId)->first();

        if ($transaction && $transaction->status != TransactionStatus::SUCCESS) {
            $transaction->payload->update([
                "raw_callback" => json_encode($this->json)
            ]);
            $transaction->result_description = $this->json["Result"]["ResultDesc"];

            $account = $transaction->account;

            if ($this->json["Result"]["ResultCode"] == 0) {
                $transaction->status = TransactionStatus::SUCCESS;
                $log_status = "SUCCESS";
                $log_status_description = $this->json["Result"]["TransactionID"];

                $successMessage = getOption("SMS-B2C-SUCCESS-MESSAGE");
                $successMessage = str_replace("{order_number}", $transaction->order_number, $successMessage);
                $successMessage = (isset($transaction->service)) ? str_replace("{service}", $transaction->service->name, $successMessage) : str_replace("{service}", $transaction->account->name, $successMessage);
                $successMessage = str_replace(" Utility bal. {balance}", "", $successMessage);

                foreach ($this->json["Result"]["ResultParameters"]["ResultParameter"] as $parameter) {
                    if ($parameter["Key"] == "CreditPartyName") {
                        $array = explode(" - ", $parameter["Value"]);
                        $transaction->account_name = $array[1];
                        $successMessage = str_replace('{to}', ucwords(strtolower($parameter["Value"])), $successMessage);
                        $log_status_description .= " - " . $parameter["Value"];
                    }
                    if ($parameter["Key"] == "FinalisedTime") {
                        $date = date("Y-m-d H:i:s", strtotime($parameter["Value"]));
                        $transaction->completed_at = $date;
                        $successMessage = str_replace('{datetime}', date("Y-m-d", strtotime($date)) . " at " . date("H:i:s", strtotime($date)), $successMessage);
                    }

                    if ($parameter["Key"] == "ReceiptNo") {
                        $transaction->receipt_number = $parameter["Value"];
                        $successMessage = str_replace('{receipt_number}', $parameter["Value"], $successMessage);
                        $successMessage = str_replace('{trxid}', $parameter["Value"], $successMessage);
                    }
                    if ($parameter["Key"] == "Amount") $successMessage = str_replace('{amount}', number_format($parameter["Value"], 2), $successMessage);
                }
            } else {
                $transaction->status = TransactionStatus::FAILED;
                $log_status = "FAILED";
                $log_status_description = $this->json["Result"]["ResultDesc"];

                $successMessage = getOption("DARAJA-ERROR-MESSAGE");
                $successMessage = str_replace('{error}', $this->json["Result"]["ResultDesc"], $successMessage);
                $successMessage = str_replace('{transaction}', $transaction->order_number, $successMessage);
            }

            $transaction->save();
            $account->save();

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
