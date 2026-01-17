<?php

namespace Wallet\Core\Repositories;

use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Wallet\Core\Contracts\UserRepositoryContract;
use Illuminate\Support\Facades\DB;

class UserRepository implements UserRepositoryContract
{
    public function create(array $data): ?User
    {
        // Implementation for creating a User
        $User = DB::transaction(function () use ($data) {
            return User::create($data);
        });

        if (!$User) {
            return null;
        }

        return $User;
    }

    public function find(int $id): ?User
    {
        // Implementation for finding a User by ID
        return User::with('role')->find($id);
    }

    public function update(int $id, array $data): ?User
    {
        // Implementation for updating a User
        $User = User::find($id);
        if ($User) {
            $User->update($data);
            return $User;
        }
        return null;
    }

    public function resetPassword(int $id, string $newPassword): ?User
    {
        // Implementation for resetting a User's password
        $User = User::find($id);
        if ($User) {
            $User->password = bcrypt($newPassword);
            $User->save();
            return $User;
        }
        return null;
    }

    public function delete(int $id): bool
    {
        // Implementation for deleting a User
        if($this->update($id, ['active' => false])) {
            return true;
        }
        return false;
    }

    public function all(array $filters): Collection
    {
        // Implementation for listing all Users with filters
        return User::with('role')->where($filters)->get();
    }
}
