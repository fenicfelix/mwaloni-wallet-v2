<?php

namespace Wallet\Core\Database\Seeders;

use Illuminate\Database\Seeder;
use Wallet\Core\Models\Country;

class CountrySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $countries = [
            [
                "name" => "Kenya",
                "code" => "KE",
            ]
        ];

        foreach ($countries as $country) {
            if (!Country::where("code", $country["code"])->exists()) {
                Country::query()->create([
                    "identifier" => generate_identifier(),
                    "name" => $country["name"],
                    "code" => $country["code"],
                    "active" => "1"
                ]);
            }
        }
    }
}
