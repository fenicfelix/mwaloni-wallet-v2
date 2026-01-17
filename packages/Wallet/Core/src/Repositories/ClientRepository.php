<?php

namespace Wallet\Core\Repositories;

use Illuminate\Database\Eloquent\Collection;
use Wallet\Core\Contracts\ClientRepositoryContract;
use Wallet\Core\Models\Client;
use Illuminate\Support\Facades\DB;

class ClientRepository implements ClientRepositoryContract
{
    public function create(array $data): ?Client
    {
        // Implementation for creating a Client
        $Client = DB::transaction(function () use ($data) {
            return Client::create($data);
        });

        if (!$Client) {
            return null;
        }

        return $Client;
    }

    public function find(int $id): ?Client
    {
        // Implementation for finding a Client by ID
        return Client::find($id);
    }

    public function update(int $id, array $data): ?Client
    {
        // Implementation for updating a Client
        $Client = Client::find($id);
        if ($Client) {
            $Client->update($data);
            return $Client;
        }
        return null;
    }

    public function delete(int $id): bool
    {
        // Implementation for deleting a Client
        $Client = Client::find($id);
        if ($Client) {
            $Client->delete();
            return true;
        }
        return false;
    }

    public function all(array $filters): Collection
    {
        // Implementation for listing all Clients with filters
        return Client::where($filters)->get();
    }
}
