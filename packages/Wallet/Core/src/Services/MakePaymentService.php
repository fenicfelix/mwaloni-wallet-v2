<?php

namespace Wallet\Core\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Wallet\Core\Models\Service;
use Wallet\Core\Models\Transaction;
use Illuminate\Support\Str;
use Wallet\Core\Http\Traits\MwaloniWallet;

class MakePaymentService
{
    use MwaloniWallet;

    /**
     * Resolve service and ensure manager owns it
     */
    public function resolveService(string $serviceId, $manager): ?Service
    {
        return Service::with('account', 'client')
            ->where('service_id', $serviceId)
            ->whereHas(
                'account',
                fn($q) =>
                $q->where('managed_by', $manager->id)
            )
            ->first();
    }

    /**
     * Execute a send-money transaction
     */
    public function sendMoney(Request $request, Service $service): Transaction
    {
        $amountRequested = (float) $request->post('amount');

        /* -------------------- VALIDATION -------------------- */

        if ($amountRequested < 1) {
            throw new \DomainException('Amount is below the minimum transaction amount.');
        }

        $limit = (int) str_replace(',', '', $service->max_trx_amount);
        if ($amountRequested >= $limit) {
            throw new \DomainException(
                "Amount exceeds the maximum transaction limit of KSh. " .
                    number_format($service->max_trx_amount)
            );
        }

        $paymentChannel = $this->getPaymentChannel($service->account, $request->post('channel'));

        if (! $paymentChannel) {
            throw new \RuntimeException('Invalid payment channel.');
        }

        /* -------------------- CALCULATIONS -------------------- */

        $amountToSend        = (int) ceil($amountRequested);
        $transactionCharges  = $this->getTransactionCharges($amountToSend, $paymentChannel->id);
        $expenses            = (float) ($service->system_charges + $service->sms_charges);
        $revenue             = max(0, $expenses - $transactionCharges);

        /* -------------------- DUPLICATE CHECKS -------------------- */

        $duplicate = Transaction::where('order_number', $request->post('order_number'))
            ->where('service_id', $service->id)
            ->exists();

        if ($duplicate) {
            throw new \DomainException('A similar transaction has been processed.');
        }

        $keyBlock = sha1($amountToSend . $request->post('account_number') . $service->id . now()->format('Ymd'));

        if (Transaction::where('key_block', $keyBlock)->exists()) {
            Log::warning("Duplicate transaction blocked via key_block {$keyBlock}");
            throw new \DomainException('A similar transaction has been processed.');
        }

        /* -------------------- BALANCE CHECK -------------------- */

        $balance = $service->account->operational_balance
            - $service->account->revenue
            - $amountToSend
            - $expenses
            - $transactionCharges;

        if ($balance < 0) {
            throw new \DomainException('Insufficient balance.');
        }

        /* -------------------- PAYMENT PAYLOAD -------------------- */

        $payload = app(PayloadGeneratorService::class)->generatePayload($paymentChannel, $request->post(), $amountToSend);

        if (! $payload) {
            throw new \RuntimeException('Failed to generate payment payload.');
        }

        /* -------------------- TRANSACTION -------------------- */

        return DB::transaction(function () use (
            $request,
            $service,
            $paymentChannel,
            $payload,
            $amountToSend,
            $transactionCharges,
            $revenue,
            $keyBlock
        ) {
            $transaction = Transaction::create([
                'identifier'          => Str::uuid(),
                'account_name'        => $request->post('account_name'),
                'account_number'      => $request->post('account_number'),
                'account_reference'   => $request->post('account_reference'),
                'requested_amount'    => $request->post('amount'),
                'disbursed_amount'    => $amountToSend,
                'key_block'           => $keyBlock,
                'reference'           => $payload['reference'] ?? now()->format('ymdHis') . rand(0, 99),
                'description'         => $request->post('description'),
                'account_id'          => $service->account_id,
                'service_id'          => $service->id,
                'type_id'             => 1,
                'order_number'        => $request->post('order_number'),
                'message_id'          => $payload['messageId'] ?? null,
                'status_id'           => 6,
                'system_charges'      => $service->system_charges,
                'sms_charges'         => $service->sms_charges,
                'revenue'             => $revenue,
                'transaction_charges' => $transactionCharges,
                'requested_by'        => $service->client->account_manager,
                'requested_on'        => now(),
                'transaction_date'    => now(),
                'payment_channel_id'  => $paymentChannel->id,
            ]);

            $transaction->payload()->create([
                'raw_request' => json_encode($request->all()),
                'trx_payload' => json_encode($payload),
            ]);

            // Update revenue
            $service->account->increment('revenue', $revenue);

            return $transaction;
        }, 2);
    }
}
