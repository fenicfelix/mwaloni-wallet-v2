<?php

namespace Wallet\Core\Contracts;

use Illuminate\Database\Eloquent\Collection;
use Wallet\Core\Models\Client;

interface ClientRepositoryContract
{
    public function create(array $data): ?Client;

    public function find(int $id): ?Client;

    public function update(int $id, array $data): ?Client;

    public function delete(int $id): bool;

    public function all(array $filters): Collection;
}
