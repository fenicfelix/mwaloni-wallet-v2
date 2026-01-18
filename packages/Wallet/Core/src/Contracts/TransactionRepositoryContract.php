<?php

namespace Wallet\Core\Contracts;

use Illuminate\Database\Eloquent\Collection;
use Wallet\Core\Models\Transaction;

interface TransactionRepositoryContract
{
    public function create(array $transactionData, array $payloadData): ?Transaction;

    public function find(int $id): ?Transaction;

    public function update(int $id, array $data): ?Transaction;

    public function delete(int $id): bool;

    public function all(array $filters): Collection;
}
