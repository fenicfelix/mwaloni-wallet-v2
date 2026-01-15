<?php

namespace Wallet\Core\Database\Seeders;

use Illuminate\Database\Seeder;
use Wallet\Core\Models\Account;

class AccountSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (!Account::where("id", "=", "1")->exists()) {
            Account::query()->create([
                "id" => 1,
                "identifier" => generate_identifier(),
                "name" => "WWT Test",
                "account_number" => "000001",
                "added_by" => 1,
                "updated_by" => 1,
            ]);
        }
    }
}
