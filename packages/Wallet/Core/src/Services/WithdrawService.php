<?php

namespace Wallet\Core\Services;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Wallet\Core\Models\PaymentChannel;
use Wallet\Core\Models\Service;
use Wallet\Core\Http\Enums\TransactionType;
use Wallet\Core\Models\Transaction;
use Illuminate\Support\Str;
use Wallet\Core\Http\Enums\TransactionStatus;
use Wallet\Core\Http\Traits\MwaloniWallet;

class WithdrawService
{
    use MwaloniWallet;

    public function processWithdrawal(int $serviceId, $withdrawFrom): ?Transaction
    {
        $service = Service::find($serviceId);
        // Validate and process the withdrawal request
        $key_block = sha1($withdrawFrom['amount'] . $withdrawFrom['account_number'] . $service->id . $withdrawFrom['channel_id'] . date('Ymd'));
        $paymentChannel = PaymentChannel::where("slug", $withdrawFrom['channel_id'])->first();
        $transaction_charges = $this->getTransactionCharges($withdrawFrom['amount'], $paymentChannel->id);

        $request = [
            "type" => "withdraw",
            "id" => $service->id,
            "account_number" => $withdrawFrom['account_number'],
            "account_name" => $withdrawFrom['account_name'],
            "channel_id" => $withdrawFrom['channel_id'],
            "account_reference" => $withdrawFrom['account_reference'] ?? null,
            "amount" => $withdrawFrom['amount'],
        ];

        return $transaction = DB::transaction(
            function () use ($request, $key_block, $paymentChannel, $transaction_charges, $service, $withdrawFrom) {
                $payload = app(PayloadGeneratorService::class)->generatePayload($paymentChannel, $request, $withdrawFrom['amount']);
                if (!$payload) return false;

                $transaction = Transaction::query()->create([
                    "identifier" => Str::uuid(),
                    "account_name" => $withdrawFrom['account_name'],
                    "account_number" => $withdrawFrom['account_number'],
                    'account_reference' => $withdrawFrom['account_reference'] ?? null,
                    "requested_amount" => $withdrawFrom['amount'],
                    "disbursed_amount" => $withdrawFrom['amount'],
                    "key_block" => $key_block,
                    "reference" => $payload["reference"],
                    "description" => "Service Charge",
                    "account_id" => $service->account_id,
                    "channel_id" => $paymentChannel->id,
                    "service_id" => $service->id,
                    "transaction_type" => TransactionType::SERVICE_CHARGE,
                    "order_number" => $this->generateOrderNumber(TransactionType::SERVICE_CHARGE),
                    "status_id" => TransactionStatus::PENDING,
                    "system_charges" => 0,
                    "sms_charges" => 0,
                    "revenue" => 0,
                    "transaction_charges" => $transaction_charges,
                    "requested_by" => Auth::id(),
                    "requested_on" => date('Y-m-d H:i:s'),
                    "transaction_date" => date('Y-m-d H:i:s'),
                    "payment_channel_id" => $paymentChannel->id,
                ]);

                $transaction->payload()->create([
                    "raw_request" => json_encode($request),
                    "trx_payload" => json_encode($payload)
                ]);

                if (!$transaction) return false;

                return $transaction;
            },
            2
        );

        // Process the withdrawal
        return $transaction;
    }
}
