<?php

namespace Wallet\Core\Repositories;

use Illuminate\Database\Eloquent\Collection;
use Wallet\Core\Contracts\AccountRepositoryContract;
use Wallet\Core\Models\Account;
use Illuminate\Support\Facades\DB;

class AccountRepository implements AccountRepositoryContract
{
    public function create(array $data): ?Account
    {
        // Implementation for creating a Account
        $Account = DB::transaction(function () use ($data) {
            return Account::create($data);
        });

        if (!$Account) {
            return null;
        }

        return $Account;
    }

    public function find(int $id): ?Account
    {
        // Implementation for finding a Account by ID
        return Account::find($id);
    }

    public function update(int $id, array $data): ?Account
    {
        // Implementation for updating a Account
        $Account = Account::find($id);
        if ($Account) {
            $Account->update($data);
            return $Account;
        }
        return null;
    }

    public function delete(int $id): bool
    {
        // Implementation for deleting a Account
        $Account = Account::find($id);
        if ($Account) {
            $Account->delete();
            return true;
        }
        return false;
    }

    public function all(array $filters): Collection
    {
        // Implementation for listing all Accounts with filters
        return Account::where($filters)->get();
    }
}
