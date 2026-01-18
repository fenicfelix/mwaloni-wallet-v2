<?php

namespace Wallet\Core\Jobs\Daraja;

use Akika\LaravelMpesaMultivendor\Mpesa;
use Wallet\Core\Models\Transaction;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Wallet\Core\Http\Enums\TransactionStatus;
use Wallet\Core\Http\Enums\TransactionType;

class ProcessDarajaB2BPayment implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $transactionId;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($transactionId)
    {
        $this->transactionId = $transactionId;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $transaction = Transaction::with(["service", "account", "payload"])->where("id", "=", $this->transactionId)->first();
        if ($transaction) {
            $isTillNumber = false;
            if ($transaction->payment_channel_id == 3) {
                $isTillNumber = true;
            }

            $account_reference = ($transaction->account_reference) ? $transaction->account_reference : $transaction->order_number;
            $response = json_decode($this->performTransaction($transaction->identifier, $transaction->account_number, floor($transaction->disbursed_amount), $transaction->description, $account_reference, $transaction->account, $isTillNumber));
            if ($response) {
                $payment_results_status = "";
                $payment_results_desc = "";

                try {
                    $payment_results_status = "SUBMITTED";
                    $payment_results_desc = $response->ConversationID;

                    $transaction->status = TransactionStatus::SUBMITTED;
                    $transaction->result_description = $response->ResponseDescription;
                    $transaction->save();

                    $transaction->payload->update([
                        "conversation_id" => $response->ConversationID,
                        "original_conversation_id" => $response->OriginatorConversationID
                    ]);
                } catch (\Throwable $th) {
                    $payment_results_status = "FAILED";
                    $payment_results_desc = $response->ResultDesc;

                    $transaction->status = TransactionStatus::FAILED;
                    $transaction->result_description = $payment_results_desc;
                    $transaction->save();
                }
            } else {
                //Ignore the job
                $transaction->status_id = 3;
                $transaction->save();
            }
        } else {
            //Ignore the job
        }
    }

    private function performTransaction($transactionID, $destShortcode, $amount, $remarks, $accountRef, $account, $isTillNumber)
    {
        $mpesa = new Mpesa($account->account_number, $account->consumer_key, $account->consumer_secret, $account->api_username, $account->api_password);
        if ($isTillNumber) {
            return $mpesa->b2bBuyGoods($destShortcode, $amount, $remarks, $accountRef, route('b2b_result_url', $transactionID), route('b2b_timeout_url'));
        } else {
            return $mpesa->b2bPaybill($destShortcode, $amount, $remarks, $accountRef, route('b2b_result_url', $transactionID), route('b2b_timeout_url'));
        }
    }
}
