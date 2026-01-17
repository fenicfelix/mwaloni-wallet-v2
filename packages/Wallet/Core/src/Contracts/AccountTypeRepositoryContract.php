<?php

namespace Wallet\Core\Contracts;

use Illuminate\Database\Eloquent\Collection;
use Wallet\Core\Models\AccountType;

interface AccountTypeRepositoryContract
{
    public function create(array $data): ?AccountType;

    public function find(int $id): ?AccountType;

    public function update(int $id, array $data): ?AccountType;

    public function delete(int $id): bool;

    public function all(array $filters): Collection;
}
