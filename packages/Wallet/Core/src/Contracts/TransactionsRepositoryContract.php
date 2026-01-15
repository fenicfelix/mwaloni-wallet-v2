<?php

namespace Wallet\Core\Contracts;

use Illuminate\Database\Eloquent\Collection;
use Wallet\Core\Models\Transaction;

interface TransactionsRepositoryContract
{
    public function create(array $transactionData, array $payloadData): ?Transaction;

    public function find(string $id): ?Transaction;

    public function update(string $id, array $data): ?Transaction;

    public function delete(string $id): bool;

    public function all(array $filters): Collection;
}
