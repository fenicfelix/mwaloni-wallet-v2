<?php

namespace Wallet\Core\Http\Traits;

use Wallet\Core\Models\Outbox;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Wallet\Core\Http\Enums\TransactionType;
use Wallet\Core\Jobs\ProcessSMS;
use Wallet\Core\Models\PaymentChannel;
use Wallet\Core\Models\Transaction;

trait MwaloniWallet
{
    private function sendSMS($to, $message)
    {
        if (config('app.env') == "production") {
            $outbox = Outbox::where("message", "=", $message)->where("to", "=", $to)->first();
            if (!$outbox) {
                $data = [
                    "message" => $message,
                    "to" => $to,
                    "sent" => "0",
                    "cost" => getOption('actual-sms-cost')
                ];

                $outbox = Outbox::query()->create($data);
                ProcessSMS::dispatch($to, $message, $outbox->id)->onQueue("outbox");
            }
        } else {
            Log::warning($to . " | " . $message);
        }
    }

    private function generateRandomString($strl_type = "", $length = 10)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        if ($strl_type == "pin") $characters = '0123456789';
        if ($strl_type == "password") $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ@%^&()';
        $randomString = '';
        $charactersLength = strlen($characters);
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    private function getTransactionCharges($amount, $paymentChannelId)
    {
        $sql = "select * from transaction_charges where payment_channel_id = '" . $paymentChannelId . "' and " . ceil($amount) . " between minimum and maximum";
        $charge = DB::select($sql);
        if ($charge) return $charge[0]->charge;
        return 0;
    }

    private function generateOrderNumber(TransactionType $transactionType)
    {
        $transaction = Transaction::where('transaction_type', $transactionType->value)->orderBy('id', 'desc')->first();
        if(!$transaction) {
            return "TRX0001";
        }

        $order_number = $transaction->order_number;
        if ($order_number) {
            $order_number++;
        } else {
            if ($transactionType == TransactionType::CASHOUT) $order_number = "CSHT0001";
            else if ($transactionType == TransactionType::REVENUE_TRANSFER) $order_number = "REV0001";
            else if ($transactionType == TransactionType::SERVICE_CHARGE) $order_number = "SRVC0001";
            else $order_number = "TRX0001";
        }

        return $order_number;
    }

    function getPaymentChannel($account, $channel)
    {
        $channel_slug = "";
        if (in_array($channel, ["ift", "eft", "rtgs", "pesalink"])) {
            $channel_slug = $account->accountType->slug . "-" . $channel;
        } else {
            $channel_slug = $channel;
        }

        if ($channel_slug) {
            return PaymentChannel::where("slug", $channel_slug)
                ->where('account_type_id', $account->account_type_id)
                ->first();
        } else {
            return false;
        }
    }
}
