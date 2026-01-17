<?php

namespace Wallet\Core\Contracts;

use Illuminate\Database\Eloquent\Collection;
use Wallet\Core\Models\SystemPreference;

interface PreferenceRepositoryContract
{
    public function create(array $data): ?SystemPreference;

    public function find(int $id): ?SystemPreference;

    public function update(int $id, array $data): ?SystemPreference;

    public function delete(int $id): bool;

    public function all(array $filters): Collection;
}
