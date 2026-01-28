<?php

namespace Wallet\Core\Jobs\Daraja;

use Wallet\Core\Models\Transaction;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Wallet\Core\Http\Enums\TransactionStatus;
use Wallet\Core\Http\Traits\MwaloniWallet;
use Wallet\Core\Repositories\TransactionRepository;

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
        info('ProcessDarajaB2BCallback: ' . $this->transactionId . ' JSON: ' . json_encode($this->json));
        $balance = NULL;
        $completed_at = date("Y-m-d H:i:s");
        $transaction = Transaction::with(["account", "service", "payload"])->where("identifier", $this->transactionId)->first();

        if (! $transaction) {
            return;
        }

        if ($transaction->status == TransactionStatus::SUCCESS) {
            return;
        }

        $account = $transaction->account;
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

            $smsMessage = getOption("SMS-B2B-SUCCESS-MESSAGE");
            $smsMessage = str_replace("{service}", ($transaction->service) ? $transaction->service->name : "Payment", $smsMessage);
            $smsMessage = str_replace("{receipt_number}", $this->json["Result"]["TransactionID"], $smsMessage);

            foreach ($this->json["Result"]["ResultParameters"]["ResultParameter"] as $parameter) {
                if ($parameter["Key"] == "Amount") $smsMessage = str_replace('{amount}', number_format($parameter["Value"], 2), $smsMessage);
                if ($parameter["Key"] == "ReceiverPartyPublicName") {
                    $array = explode(" - ", $parameter["Value"]);
                    $updateData['account_name'] = $array[1];
                    $smsMessage = str_replace('{to}', ucwords(strtolower($parameter["Value"])), $smsMessage);
                }
                if ($parameter["Key"] == "TransCompletedTime") {
                    $completed_at = date("Y-m-d H:i:s", strtotime($parameter["Value"]));
                    $transaction->completed_at = $completed_at;
                    $smsMessage = str_replace('{datetime}', date("Y-m-d", strtotime($completed_at)) . " at " . date("H:i:s", strtotime($completed_at)), $smsMessage);
                }
                // if ($parameter["Key"] == "DebitAccountCurrentBalance") {
                //     $balance = getBalance($parameter["Value"], "BasicAmount");
                //     $smsMessage = str_replace('{balance}', number_format($balance), $smsMessage);
                // }
            }

            // Final SMS Message Formatting
            $smsMessage = str_replace('{datetime}', date("Y-m-d", strtotime($completed_at)) . " at " . date("H:i:s", strtotime($completed_at)), $smsMessage);
            $smsMessage = str_replace('{amount}', number_format($transaction->disbursed_amount), $smsMessage);
            $smsMessage = str_replace('{to}', $transaction->account_number, $smsMessage);
        } else {
            $updateData['status'] = TransactionStatus::FAILED;
            $updateData['completed_at'] = $completed_at;

            $smsMessage = getOption("DARAJA-ERROR-MESSAGE");
            $smsMessage = str_replace('{error}', $this->json["Result"]["ResultDesc"], $smsMessage);
            $smsMessage = str_replace('{transaction}', $transaction->order_number, $smsMessage);
        }

        info('6');

        info('UpdateData: ' . json_encode($updateData));
        info('PayloadData: ' . json_encode($payloadData));
        info('MESSAGE: ' . $smsMessage);
        die;

        app(TransactionRepository::class)->updateWithPayload($transaction->id, $updateData, $payloadData);

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
