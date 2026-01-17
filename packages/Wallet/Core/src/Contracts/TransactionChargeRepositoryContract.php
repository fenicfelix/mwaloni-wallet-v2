<?php

namespace Wallet\Core\Contracts;

use Illuminate\Database\Eloquent\Collection;
use Wallet\Core\Models\TransactionCharge;

interface TransactionChargeRepositoryContract
{
    public function create(array $data): ?TransactionCharge;

    public function find(int $id): ?TransactionCharge;

    public function update(int $id, array $data): ?TransactionCharge;

    public function delete(int $id): bool;

    public function all(array $filters): Collection;
}
