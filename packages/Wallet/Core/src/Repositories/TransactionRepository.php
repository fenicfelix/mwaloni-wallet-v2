<?php

namespace Wallet\Core\Repositories;

use Illuminate\Database\Eloquent\Collection;
use Wallet\Core\Models\Transaction;
use Illuminate\Support\Facades\DB;
use Wallet\Core\Contracts\TransactionRepositoryContract;
use Wallet\Core\Jobs\PushTransactionCallback;

class TransactionRepository implements TransactionRepositoryContract
{
    public function create(array $transactionData, array $payloadData, ?float $revenue = 0): ?Transaction
    {
        // Implementation for creating a transaction
        $transaction = DB::transaction(function () use ($transactionData, $payloadData, $revenue) {
            $transaction = Transaction::create($transactionData);
            $transaction->payload()->create($payloadData);
            $transaction->balanceReservations()->create(
                [
                    'account_id' => $transaction->account_id,
                    'transaction_id' => $transaction->id,
                    'amount' => $transaction->disbursed_amount
                ]
            );

            if ($revenue > 0) {
                $transaction->account->increment('revenue', $revenue);
            }
            return $transaction;
        });

        if (!$transaction) {
            return null;
        }

        return $transaction;
    }

    public function find(int $id): ?Transaction
    {
        // Implementation for finding a transaction by ID
        return Transaction::with(["payload", "account"])->find($id);
    }

    public function update(int $id, array $data): ?Transaction
    {
        // Implementation for updating a transaction
        $transaction = Transaction::find($id);
        if ($transaction) {
            $transaction->update($data);
            return $transaction;
        }
        return null;
    }

    public function updateWithPayload(int $id, array $data, array $payloadData): ?Transaction
    {
        // Implementation for updating a transaction with payload
        $transaction = Transaction::find($id);
        if ($transaction) {
            $transaction->update($data);
            $transaction->payload()->update($payloadData);
            return $transaction;
        }
        return null;
    }

    public function delete(int $id): bool
    {
        // Implementation for deleting a transaction
        $transaction = Transaction::find($id);
        if ($transaction) {
            $transaction->delete();
            return true;
        }
        return false;
    }

    public function all(array $filters): Collection
    {
        // Implementation for listing all transactions with filters
        return Transaction::where($filters)->get();
    }

    public function completeTransaction(int $id): ?Transaction
    {
        // Implementation for completing a transaction
        $transaction = Transaction::with(['payload', 'service'])->find($id);
        if ($transaction) {
            // Release any reserved amounts
            $transaction->releaseReservedAmount();

            // Refresh the transaction to get the latest data
            $transaction->refresh();

            // Send callback if the service has a callback URL
            if (isset($transaction->service) && $transaction->service->callback_url != NULL) {
                $data = (object) $transaction->payload->raw_callback;
                $data->orderNumber = $transaction->order_number;
                PushTransactionCallback::dispatch($data, $transaction->service->callback_url);
            }

            return $transaction;
        }
        return null;
    }
}
