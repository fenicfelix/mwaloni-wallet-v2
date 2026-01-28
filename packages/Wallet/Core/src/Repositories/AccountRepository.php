<?php

namespace Wallet\Core\Repositories;

use Illuminate\Database\Eloquent\Collection;
use Wallet\Core\Contracts\AccountRepositoryContract;
use Wallet\Core\Models\Account;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AccountRepository implements AccountRepositoryContract
{
    public function create(array $data): ?Account
    {
        // Implementation for creating a Account
        $Account = DB::transaction(function () use ($data) {
            $data['identifier'] = Str::uuid()->toString();
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

    public function updateWithheldAmount(int $id, float $amount): ?Account
    {
        // Implementation for updating the withheld amount of a Account
        $Account = Account::find($id);
        if ($Account) {
            $Account->withheld_amount = $amount;
            $Account->save();
            return $Account;
        }
        return null;
    }

    public function activateDeactivate(int $id): bool
    {
        // Implementation for activating or deactivating a Account
        $Account = Account::find($id);
        if ($Account) {
            $Account->active = !$Account->active;
            if ($Account->save()) return true;
        }
        return false;
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

    public function deductRevenue(int $id, float $amount): ?Account
    {
        // Implementation for deducting revenue from a Account
        $Account = Account::find($id);
        if ($Account) {
            $Account->revenue -= $amount;
            $Account->save();
            return $Account;
        }
        return null;
    }

    public function addRevenue(int $id, float $amount): ?Account
    {
        // Implementation for adding revenue to a Account
        $Account = Account::find($id);
        if ($Account) {
            $Account->revenue += $amount;
            $Account->save();
            return $Account;
        }
        return null;
    }
}
