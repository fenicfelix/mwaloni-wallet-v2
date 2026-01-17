<?php

namespace Wallet\Core\Contracts;

use Illuminate\Database\Eloquent\Collection;
use Wallet\Core\Models\Role;

interface RoleRepositoryContract
{
    public function create(array $data): ?Role;

    public function find(int $id): ?Role;

    public function update(int $id, array $data): ?Role;

    public function delete(int $id): bool;

    public function all(array $filters): Collection;
}
