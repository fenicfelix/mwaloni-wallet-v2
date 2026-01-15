<?php

namespace Wallet\Core\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Wallet\Core\Models\AccountType;

class AccountTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $accountTypes = ["Daraja", "Jenga", "NCBA"];
        foreach ($accountTypes as $accountType) {
            if (!AccountType::where("account_type", "=", $accountType)->exists()) {
                AccountType::query()->create([
                    "identifier" => generate_identifier(),
                    "account_type" => $accountType,
                    "slug" => Str::slug($accountType)
                ]);
            }
        }
    }
}
