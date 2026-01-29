<?php

namespace Wallet\Core\Contracts;

use Illuminate\Database\Eloquent\Collection;
use Wallet\Core\Models\Transaction;

interface TransactionRepositoryContract
{
    public function create(array $transactionData, array $payloadData, ?float $revenue = 0): ?Transaction;

    public function find(int $id): ?Transaction;

    public function update(int $id, array $data): ?Transaction;

    public function updateWithPayload(int $id, array $data, array $payloadData): ?Transaction;

    public function retry(int $id): ?Transaction;

    public function delete(int $id): bool;

    public function all(array $filters): Collection;

    public function completeTransaction(int $id): ?Transaction;
}
