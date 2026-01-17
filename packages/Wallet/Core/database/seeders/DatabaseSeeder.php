<?php

namespace Wallet\Core\Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            UserSeeder::class,
            // SpartieSeeder::class,
            // AccountTypeSeeder::class,
            // PaymentChannelSeeder::class,
            // CountrySeeder::class,
            // CurrencySeeder::class,
            // StatusSeeder::class,
            // TransactionChargeSeeder::class,
            // PreferencesSeeder::class,
        ]);
    }
}
