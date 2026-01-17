<?php

namespace Wallet\Core\Repositories;

use Illuminate\Database\Eloquent\Collection;
use Wallet\Core\Contracts\ServiceRepositoryContract;
use Wallet\Core\Models\Service;
use Illuminate\Support\Facades\DB;

class ServiceRepository implements ServiceRepositoryContract
{
    public function create(array $data): ?Service
    {
        // Implementation for creating a Service
        $service = DB::transaction(function () use ($data) {
            $service = DB::transaction(function () use ($data) {
                return Service::create($data);
            });

            if (!$service) {
                return null;
            }

            $service_id = str_pad($service->id, 5, "0", STR_PAD_LEFT);
            $service->service_id = "SRV-" . $service_id;
            $service->save();

            return $service;
        });

        return $service;
    }

    public function find(int $id): ?Service
    {
        // Implementation for finding a Service by ID
        return Service::find($id);
    }

    public function findWithAccount(int $id): ?Service
    {
        // Implementation for finding a Service by ID with Account
        return Service::with('account')->find($id);
    }

    public function update(int $id, array $data): ?Service
    {
        // Implementation for updating a Service
        $service = Service::find($id);
        if ($service) {
            $service->update($data);
            return $service;
        }
        return null;
    }

    public function delete(int $id): bool
    {
        // Implementation for deleting a Service
        $service = Service::find($id);
        if ($service) {
            $service->delete();
            return true;
        }
        return false;
    }

    public function all(array $filters): Collection
    {
        // Implementation for listing all Services with filters
        return Service::where($filters)->get();
    }
}
