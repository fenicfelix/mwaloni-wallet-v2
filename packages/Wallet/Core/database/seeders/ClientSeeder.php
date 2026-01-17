<?php

namespace Wallet\Core\Database\Seeders;

use Illuminate\Database\Seeder;
use Wallet\Core\Models\Client;

class ClientSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (!Client::where("id", "=", "1")->exists()) {
            Client::query()->create([
                "id" => 1,
                "name" => config("app.name"),
                "client_id" => "CLI-00001",
                "account_manager" => 1,
                "balance" => 0,
                "active" => 1,
                "added_by" => "1",
                "updated_by" => "1",
            ]);
        }
    }
}
