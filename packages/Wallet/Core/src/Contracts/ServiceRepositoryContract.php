<?php

namespace Wallet\Core\Contracts;

use Illuminate\Database\Eloquent\Collection;
use Wallet\Core\Models\Service;

interface ServiceRepositoryContract
{
    public function create(array $data): ?Service;

    public function find(int $id): ?Service;

    public function findWithAccount(int $id): ?Service;

    public function update(int $id, array $data): ?Service;

    public function delete(int $id): bool;

    public function all(array $filters): Collection;
}
