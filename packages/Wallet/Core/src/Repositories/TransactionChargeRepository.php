<?php

namespace Wallet\Core\Repositories;

use Illuminate\Database\Eloquent\Collection;
use Wallet\Core\Contracts\TransactionChargeRepositoryContract;
use Wallet\Core\Models\TransactionCharge;
use Illuminate\Support\Facades\DB;

class TransactionChargeRepository implements TransactionChargeRepositoryContract
{
    public function create(array $data): ?TransactionCharge
    {
        // Implementation for creating a TransactionCharge
        $TransactionCharge = DB::transaction(function () use ($data) {
            return TransactionCharge::create($data);
        });

        if (!$TransactionCharge) {
            return null;
        }

        return $TransactionCharge;
    }

    public function find(int $id): ?TransactionCharge
    {
        // Implementation for finding a TransactionCharge by ID
        return TransactionCharge::find($id);
    }

    public function update(int $id, array $data): ?TransactionCharge
    {
        // Implementation for updating a TransactionCharge
        $TransactionCharge = TransactionCharge::find($id);
        if ($TransactionCharge) {
            $TransactionCharge->update($data);
            return $TransactionCharge;
        }
        return null;
    }

    public function delete(int $id): bool
    {
        // Implementation for deleting a TransactionCharge
        $TransactionCharge = TransactionCharge::find($id);
        if ($TransactionCharge) {
            $TransactionCharge->delete();
            return true;
        }
        return false;
    }

    public function all(array $filters): Collection
    {
        // Implementation for listing all TransactionCharges with filters
        return TransactionCharge::where($filters)->get();
    }
}
