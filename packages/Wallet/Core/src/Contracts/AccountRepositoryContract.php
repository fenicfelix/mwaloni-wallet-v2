<?php

namespace Wallet\Core\Contracts;

use Illuminate\Database\Eloquent\Collection;
use Wallet\Core\Models\Account;

interface AccountRepositoryContract
{
    public function create(array $data): ?Account;

    public function find(int $id): ?Account;

    public function update(int $id, array $data): ?Account;
    
    public function updateWithheldAmount(int $id, float $amount): ?Account;

    public function activateDeactivate(int $id): bool;

    public function delete(int $id): bool;

    public function all(array $filters): Collection;

    public function deductRevenue(int $id, float $amount): ?Account;

    public function addRevenue(int $id, float $amount): ?Account;
}
