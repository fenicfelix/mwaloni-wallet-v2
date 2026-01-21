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
        info('Processing Daraja B2C Callback...');
        info('JSON: ' . json_encode($this->json));
        $completed_at = date("Y-m-d H:i:s");
        $smsMessage = "";
        $transaction = Transaction::with(["account", "service", "payload"])->where("identifier", $this->transactionId)->first();

        if (! $transaction) {
            info('1');
            return;
        }

        if ($transaction->status == TransactionStatus::SUCCESS) {
            info('2');
            return;
        }

        info('3');
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
            info('4');
            $updateData['status'] = TransactionStatus::SUCCESS;

            $smsMessage = getOption("SMS-B2C-SUCCESS-MESSAGE");
            $smsMessage = str_replace("{order_number}", $transaction->order_number, $smsMessage);
            $smsMessage = (isset($transaction->service)) ? str_replace("{service}", $transaction->service->name, $smsMessage) : str_replace("{service}", $transaction->account->name, $smsMessage);

            if (isset($this->json["Result"]["ResultParameters"])) {
                foreach ($this->json["Result"]["ResultParameters"]["ResultParameter"] as $parameter) {
                    if ($parameter["Key"] == "TransactionReceipt") {
                        $smsMessage = str_replace('{receipt_number}', $parameter["Value"], $smsMessage);
                    }
                    if ($parameter["Key"] == "TransactionAmount") {
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
                        $balance = getBalance($parameter["Value"], "BasicAmount");
                        $smsMessage = str_replace('{balance}', number_format($balance, 2), $smsMessage);
                    }
                    if ($parameter["Key"] == "B2CWorkingAccountAvailableFunds") {
                        $account->working_balance = $parameter["Value"];
                    }
                }
            }
        } else {
            info('5');
            $updateData['completed_at'] = $completed_at;
            $updateData['status'] = TransactionStatus::FAILED;

            $smsMessage = getOption("DARAJA-ERROR-MESSAGE");
            $smsMessage = str_replace('{account}', $account->name, $smsMessage);
            $smsMessage = str_replace('{error}', $this->json["Result"]["ResultDesc"], $smsMessage);
            $smsMessage = str_replace('{transaction}', $transaction->order_number, $smsMessage);
        }

        info('6');

        info('UpdateData: ' . json_encode($updateData));
        info('PayloadData: ' . json_encode($payloadData));
        info('MESSAGE: ' . $smsMessage);
        die;

        app(TransactionRepository::class)->updateTransactionAndPayload($transaction->id, $updateData, $payloadData);

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
