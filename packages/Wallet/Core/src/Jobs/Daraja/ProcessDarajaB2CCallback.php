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
        $completed_at = date("Y-m-d H:i:s");
        $smsMessage = "";
        $transaction = Transaction::with(["account", "service", "payload"])->where("identifier", $this->transactionId)->first();

        if (! $transaction) {
            return;
        }

        if ($transaction->status == TransactionStatus::SUCCESS) {
            return;
        }

        $account = $transaction->account;
        $balance = 0;
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
            $smsMessage = str_replace("{receipt_number}", $this->json["Result"]["TransactionID"], $smsMessage);
            $smsMessage = str_replace("{order_number}", $transaction->order_number, $smsMessage);
            $smsMessage = (isset($transaction->service)) ? str_replace("{service}", $transaction->service->name, $smsMessage) : str_replace("{service}", $transaction->account->name, $smsMessage);

            if (isset($this->json["Result"]["ResultParameters"])) {
                $balanceFound = false;
                foreach ($this->json["Result"]["ResultParameters"]["ResultParameter"] as $parameter) {
                    if ($parameter["Key"] == "TransactionReceipt") {
                        $smsMessage = str_replace('{receipt_number}', $parameter["Value"], $smsMessage);
                    }
                    if (in_array($parameter["Key"], ["TransactionAmount", "Amount"])) {
                        $smsMessage = str_replace('{amount}', number_format($parameter["Value"], 2), $smsMessage);
                    }
                    if ($parameter["Key"] == "ReceiverPartyPublicName") {
                        $array = explode(" - ", $parameter["Value"]);
                        $updateData['account_name'] = $array[1];
                        $smsMessage = str_replace('{to}', ucwords(strtolower($parameter["Value"])), $smsMessage);
                    }
                    if ($parameter["Key"] == "TransactionCompletedDateTime") {
                        $completed_at = date("Y-m-d H:i:s", strtotime($parameter["Value"]));
                        $updateData['completed_at'] = $completed_at;
                        $smsMessage = str_replace('{datetime}', date("Y-m-d", strtotime($completed_at)) . " at " . date("H:i:s", strtotime($completed_at)), $smsMessage);
                    }
                    if ($parameter["Key"] == "DebitAccountCurrentBalance") {
                        $balanceFound = true;
                        $balance = getBalance($parameter["Value"], "BasicAmount");
                        $smsMessage = str_replace('{balance}', number_format($balance, 2), $smsMessage);
                    }
                    if ($parameter["Key"] == "B2CUtilityAccountAvailableFunds") {
                        $account->utility_balance = $parameter["Value"];
                    }
                    if ($parameter["Key"] == "B2CWorkingAccountAvailableFunds") {
                        $account->working_balance = $parameter["Value"];
                    }
                }

                if (!$balanceFound) {
                    $balance = $account->utility_balance + $account->working_balance;
                    $smsMessage = str_replace('{balance}', number_format($balance, 2), $smsMessage);
                }
            }
        } else {
            $updateData['completed_at'] = $completed_at;
            $updateData['status'] = TransactionStatus::FAILED;

            $smsMessage = getOption("DARAJA-ERROR-MESSAGE");
            $smsMessage = str_replace('{account}', $account->name, $smsMessage);
            $smsMessage = str_replace('{error}', $this->json["Result"]["ResultDesc"], $smsMessage);
            $smsMessage = str_replace('{transaction}', $transaction->order_number, $smsMessage);
        }

        app(TransactionRepository::class)->updateWithPayload($transaction->id, $updateData, $payloadData);

        $account->save();

        //Save account balance
        if ($this->json["Result"]["ResultCode"] == 0) {
            app(TransactionRepository::class)->completeTransaction($transaction->id);
            if ($balance) {
                $account->update([
                    'utility_balance' => $balance
                ]);
            }
        }

        //Send SMS
        if ($smsMessage != "") {
            $smsMessageTo = getOption("DARAJA-ALERT-CONTACT");
            $this->sendSMS($smsMessageTo, $smsMessage);
        }
    }
}
