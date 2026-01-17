<?php

namespace Wallet\Core\Repositories;

use Illuminate\Database\Eloquent\Collection;
use Wallet\Core\Contracts\RoleRepositoryContract;
use Wallet\Core\Models\Role;
use Illuminate\Support\Facades\DB;

class RoleRepository implements RoleRepositoryContract
{
    public function create(array $data): ?Role
    {
        // Implementation for creating a role
        $role = DB::transaction(function () use ($data) {
            return Role::create($data);
        });

        if (!$role) {
            return null;
        }

        return $role;
    }

    public function find(int $id): ?Role
    {
        // Implementation for finding a role by ID
        return Role::find($id);
    }

    public function update(int $id, array $data): ?Role
    {
        // Implementation for updating a role
        $role = Role::find($id);
        if ($role) {
            $role->update($data);
            return $role;
        }
        return null;
    }

    public function delete(int $id): bool
    {
        // Implementation for deleting a role
        $role = Role::find($id);
        if ($role) {
            $role->delete();
            return true;
        }
        return false;
    }

    public function all(array $filters): Collection
    {
        // Implementation for listing all roles with filters
        return Role::where($filters)->get();
    }
}
