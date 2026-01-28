<?php

namespace Wallet\Core\Services;

use Illuminate\Support\Facades\Auth;
use Wallet\Core\Models\PaymentChannel;
use Illuminate\Support\Facades\DB;
use Wallet\Core\Http\Enums\TransactionStatus;
use Wallet\Core\Http\Enums\TransactionType;
use Wallet\Core\Http\Traits\MwaloniWallet;
use Illuminate\Support\Str;
use Wallet\Core\Models\Account;
use Wallet\Core\Models\Service;
use Wallet\Core\Models\Transaction;
use Wallet\Core\Repositories\TransactionRepository;

class CashoutService
{
    use MwaloniWallet;

    // Cashout service methods go here
    public function processCashout(array $cashout_form, int $accountId)
    {
        $account = Account::find($accountId);
        // Validate and process cashout request
        $paymentChannel = PaymentChannel::where('slug', $cashout_form['channel_id'])->first();
        $transaction_charges = $this->getTransactionCharges($cashout_form['amount'], $paymentChannel->id);
        $key_block = sha1($cashout_form['amount'] . $cashout_form['account_number'] . $account->id . $cashout_form['channel_id'] . date('Ymd'));

        $transaction = DB::transaction(
            function () use ($key_block, $paymentChannel, $transaction_charges, $cashout_form, $account) {

                $request = [
                    "type" => "revenue",
                    "id" => $account->id,
                    "account_number" => $cashout_form['account_number'],
                    "account_name" => $cashout_form['account_name'],
                    "channel_id" => $paymentChannel->id,
                    "account_reference" => $cashout_form['account_reference'] ?? NULL,
                    "amount" => $cashout_form['amount'],
                ];

                $request['order_number'] = $this->generateOrderNumber(TransactionType::REVENUE_TRANSFER);
                $payload =  app(PayloadGeneratorService::class)->generatePayload($paymentChannel, $request, $cashout_form['amount']);
                $transactionData = [
                    "identifier" => Str::uuid(),
                    "account_name" => $cashout_form['account_name'],
                    "account_number" => $cashout_form['account_number'],
                    'account_reference' => $cashout_form['account_reference'] ?? null,
                    "requested_amount" => $cashout_form['amount'],
                    "disbursed_amount" => $cashout_form['amount'],
                    "key_block" => $key_block,
                    "reference" => $payload["reference"] ?? date('ymdhis'),
                    "description" => "Revenue Cashout",
                    "account_id" => $account->id,
                    "channel_id" => $paymentChannel->id,
                    "service_id" => NULL,
                    "transaction_type" => TransactionType::REVENUE_TRANSFER,
                    "order_number" => $request['order_number'],
                    "status" => TransactionStatus::PENDING,
                    "system_charges" => 0,
                    "sms_charges" => 0,
                    "revenue" => 0,
                    "transaction_charges" => $transaction_charges,
                    "requested_by" => Auth::id(),
                    "requested_on" => date('Y-m-d H:i:s'),
                    "transaction_date" => date('Y-m-d H:i:s'),
                    "payment_channel_id" => $paymentChannel->id,
                ];

                $payloadData = [
                    "raw_request" => json_encode($request),
                    "trx_payload" => json_encode($payload)
                ];

                $transaction = app(TransactionRepository::class)->create($transactionData, $payloadData);
                if (!$transaction) return false;

                //Add to revenue
                $account->revenue -= $cashout_form['amount'];
                if (!$account->save()) return false;

                return $transaction;
            },
            2
        );

        return $transaction;
    }
}
