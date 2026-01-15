<?php

namespace Wallet\Core\Database\Seeders;

use Illuminate\Database\Seeder;
use Wallet\Core\Models\Currency;

class CurrencySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $currencies = [
            [
                "name" => "Kenya Shilling",
                "code" => "KES",
            ],
            [
                "name" => "US Dollar",
                "code" => "USD",
            ]
        ];

        foreach ($currencies as $currency) {
            if (!Currency::where("code", $currency["code"])->exists()) {
                Currency::query()->create([
                    "identifier" => generate_identifier(),
                    "name" => $currency["name"],
                    "code" => $currency["code"],
                    "active" => "1"
                ]);
            }
        }
    }
}
