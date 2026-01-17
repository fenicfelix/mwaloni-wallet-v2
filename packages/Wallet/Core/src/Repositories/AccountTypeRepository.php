<?php

namespace Wallet\Core\Repositories;

use Illuminate\Database\Eloquent\Collection;
use Wallet\Core\Contracts\AccountTypeRepositoryContract;
use Wallet\Core\Models\AccountType;
use Illuminate\Support\Facades\DB;

class AccountTypeRepository implements AccountTypeRepositoryContract
{
    public function create(array $data): ?AccountType
    {
        // Implementation for creating a AccountType
        $AccountType = DB::transaction(function () use ($data) {
            return AccountType::create($data);
        });

        if (!$AccountType) {
            return null;
        }

        return $AccountType;
    }

    public function find(int $id): ?AccountType
    {
        // Implementation for finding a AccountType by ID
        return AccountType::find($id);
    }

    public function update(int $id, array $data): ?AccountType
    {
        // Implementation for updating a AccountType
        $AccountType = AccountType::find($id);
        if ($AccountType) {
            $AccountType->update($data);
            return $AccountType;
        }
        return null;
    }

    public function delete(int $id): bool
    {
        // Implementation for deleting a AccountType
        $AccountType = AccountType::find($id);
        if ($AccountType) {
            $AccountType->delete();
            return true;
        }
        return false;
    }

    public function all(array $filters): Collection
    {
        // Implementation for listing all AccountTypes with filters
        return AccountType::where($filters)->get();
    }
}
