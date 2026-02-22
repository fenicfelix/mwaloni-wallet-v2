<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Wallet\Core\Models\PaymentChannel;
use Illuminate\Support\Str;
use Wallet\Core\Models\AccountType;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {

        $accountType = AccountType::updateOrCreate(
            [
                'slug' => 'mtn-momo',
            ],
            [
                'account_type' => 'MTN MoMo',
                'slug' => 'mtn-momo',
            ]
        );

        PaymentChannel::updateOrCreate(
            [
                'slug' => 'mtn-momo',
            ],
            [
                'name' => 'MTN MoMo',
                'slug' => 'mtn-momo',
                'description' => 'MTN Mobile Money',
                'active' => true,
                'account_type_id' => $accountType->id,
            ]
        );
    }
}
