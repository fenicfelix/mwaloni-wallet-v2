<?php

namespace Wallet\Core\Contracts;

use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

interface UserRepositoryContract
{
    public function create(array $data): ?User;

    public function find(int $id): ?User;

    public function update(int $id, array $data): ?User;

    public function resetPassword(int $id, string $newPassword): ?User;

    public function delete(int $id): bool;

    public function all(array $filters): Collection;
}
