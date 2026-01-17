<?php

namespace Wallet\Core\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Wallet\Core\Models\Service;

class ServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (!Service::where("id", "=", "1")->exists()) {
            Service::query()->create([
                "id" => 1,
                "name" => str_replace(" ", "-", config("app.name")),
                "service_id" => "SRV-00001",
                "description" => "Default Payment Service",
                "balance" => 0,
                "active" => 1,
                "client_id" => 1,
                "system_charges" => 0,
                "sms_charges" => 0,
                "username" => "srv00001",
                "password" => Hash::make("password"),
                "added_by" => "1",
                "updated_by" => "1",
            ]);
        }
    }
}
