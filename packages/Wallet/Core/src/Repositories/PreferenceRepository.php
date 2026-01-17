<?php

namespace Wallet\Core\Repositories;

use Illuminate\Database\Eloquent\Collection;
use Wallet\Core\Models\SystemPreference;
use Illuminate\Support\Facades\DB;
use Wallet\Core\Contracts\PreferenceRepositoryContract;

class PreferenceRepository implements PreferenceRepositoryContract
{
    public function create(array $data): ?SystemPreference
    {
        // Implementation for creating a SystemPreference
        $SystemPreference = DB::transaction(function () use ($data) {
            return SystemPreference::create($data);
        });

        if (!$SystemPreference) {
            return null;
        }

        return $SystemPreference;
    }

    public function find(int $id): ?SystemPreference
    {
        // Implementation for finding a SystemPreference by ID
        return SystemPreference::find($id);
    }

    public function update(int $id, array $data): ?SystemPreference
    {
        // Implementation for updating a SystemPreference
        $SystemPreference = SystemPreference::find($id);
        if ($SystemPreference) {
            $SystemPreference->update($data);
            return $SystemPreference;
        }
        return null;
    }

    public function delete(int $id): bool
    {
        // Implementation for deleting a SystemPreference
        $SystemPreference = SystemPreference::find($id);
        if ($SystemPreference) {
            $SystemPreference->delete();
            return true;
        }
        return false;
    }

    public function all(array $filters): Collection
    {
        // Implementation for listing all SystemPreferences with filters
        return SystemPreference::where($filters)->get();
    }
}
