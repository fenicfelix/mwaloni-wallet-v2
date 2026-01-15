<?php

namespace Wallet\Core\Repositories;

use Illuminate\Database\Eloquent\Collection;
use Wallet\Core\Contracts\TransactionsRepositoryContract;
use Wallet\Core\Models\Transaction;
use Illuminate\Support\Facades\DB;

class TransactionsRepository implements TransactionsRepositoryContract
{
    public function create(array $transactionData, array $payloadData): ?Transaction
    {
        // Implementation for creating a transaction
        $transaction = DB::transaction(function () use ($transactionData, $payloadData) {
            $transaction = Transaction::create($transactionData);
            $transaction->payload()->create($payloadData);
            return $transaction;
        });

        if (!$transaction) {
            return null;
        }

        return $transaction;
    }

    public function find(string $id): ?Transaction
    {
        // Implementation for finding a transaction by ID
        return Transaction::find($id);
    }

    public function update(string $id, array $data): ?Transaction
    {
        // Implementation for updating a transaction
        $transaction = Transaction::find($id);
        if ($transaction) {
            $transaction->update($data);
            return $transaction;
        }
        return null;
    }

    public function delete(string $id): bool
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
}
