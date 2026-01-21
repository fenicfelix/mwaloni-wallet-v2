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
use Wallet\Core\Repositories\TransactionRepository;

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
        $successMessage = "";

        $transaction = Transaction::with(["service", "payload"])->where("identifier", $this->transactionId)->first();

        if (! $transaction) {
            return;
        }

        if ($transaction->status == TransactionStatus::SUCCESS) {
            return;
        }
        
        $smsMessage = "";
        $payloadData = [
            "raw_callback" => json_encode($this->json)
        ];
        $updateData = [
            'receipt_number' => $this->json["Result"]["TransactionID"],
            'result_description' => $this->json["Result"]["ResultDesc"]
        ];

        if ($this->json["Result"]["ResultCode"] == 0) {
            $updateData['status'] = TransactionStatus::SUCCESS;

            $smsMessage = getOption("SMS-B2C-SUCCESS-MESSAGE");
            $smsMessage = str_replace("{order_number}", $transaction->order_number, $smsMessage);
            $smsMessage = (isset($transaction->service)) ? str_replace("{service}", $transaction->service->name, $smsMessage) : str_replace("{service}", $transaction->account->name, $smsMessage);
            $smsMessage = str_replace(" Utility bal. {balance}", "", $smsMessage);

            foreach ($this->json["Result"]["ResultParameters"]["ResultParameter"] as $parameter) {
                if ($parameter["Key"] == "CreditPartyName") {
                    $array = explode(" - ", $parameter["Value"]);
                    $updateData['account_name'] = $array[1];

                    $smsMessage = str_replace('{to}', ucwords(strtolower($parameter["Value"])), $smsMessage);
                }
                if ($parameter["Key"] == "FinalisedTime") {
                    $date = date("Y-m-d H:i:s", strtotime($parameter["Value"]));
                    $updateData['completed_at'] = $date;
                    $smsMessage = str_replace('{datetime}', date("Y-m-d", strtotime($date)) . " at " . date("H:i:s", strtotime($date)), $smsMessage);
                }

                if ($parameter["Key"] == "ReceiptNo") {
                    $updateData['receipt_number'] = $parameter["Value"];
                    $smsMessage = str_replace('{receipt_number}', $parameter["Value"], $smsMessage);
                    $successMessage = str_replace('{trxid}', $parameter["Value"], $successMessage);
                }
                if ($parameter["Key"] == "Amount") {
                    $smsMessage = str_replace('{amount}', number_format($parameter["Value"], 2), $smsMessage);
                }
            }
        } else {
            $updateData['status'] = TransactionStatus::FAILED;

            $successMessage = getOption("DARAJA-ERROR-MESSAGE");
            $successMessage = str_replace('{error}', $this->json["Result"]["ResultDesc"], $successMessage);
            $successMessage = str_replace('{transaction}', $transaction->order_number, $successMessage);
        }

        app(TransactionRepository::class)->updateTransactionAndPayload($transaction->id, $updateData, $payloadData);
        if ($this->json["Result"]["ResultCode"] == 0) {
            app(TransactionRepository::class)->completeTransaction($transaction->id);
        }

        //Send SMS
        if ($successMessage != "") {
            $successMessageTo = getOption("DARAJA-ALERT-CONTACT");
            $this->sendSMS($successMessageTo, $successMessage);
        }
    }
}
