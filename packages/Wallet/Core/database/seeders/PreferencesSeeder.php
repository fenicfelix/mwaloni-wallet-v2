<?php

namespace Wallet\Core\Database\Seeders;

use Illuminate\Database\Seeder;
use Wallet\Core\Models\SystemPreference;

class PreferencesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $preferences = [
            [
                "title" => "SMS Welcome",
                "slug" => "SMS-WELCOME",
                "value" => "Hello {name}. Welcome to Mwaloni WWT Wallet. Your password is {password}. Your password is your SECRET."
            ],
            [
                "title" => "SMS Password Reset",
                "slug" => "SMS-PASSWORD-RESET",
                "value" => "Hi {name}. Your new password for Mwaloni WWT Wallet is {password}. Your password is your SECRET."
            ],
            [
                "title" => "Daraja Error Message",
                "slug" => "DARAJA-ERROR-MESSAGE",
                "value" => "Payment Error for {transaction} on {account}: {error}"
            ],
            [
                "title" => "SMS B2C Success Message",
                "slug" => "SMS-B2C-SUCCESS-MESSAGE",
                "value" => "{service} {receipt_number} Confirmed. KSh. {amount} sent to {to} for order no. {order_number} on {datetime}. Utility bal. {balance}"
            ],
            [
                "title" => "SMS B2B Success Message",
                "slug" => "SMS-B2B-SUCCESS-MESSAGE",
                "value" => "{service}, {receipt_number} Confirmed. KSh. {amount} transafered to {to} on {datetime}. Utility bal. {balance}."
            ],
            [
                "title" => "Daraja Alert Contact",
                "slug" => "DARAJA-ALERT-CONTACT",
                "value" => "254720322615"
            ],
            [
                "title" => "SMS Cost",
                "slug" => "SMS-COST",
                "value" => "1"
            ],
            [
                "title" => "AT USERNAME",
                "slug" => "AT-USERNAME",
                "value" => "rkamun1"
            ],
            [
                "title" => "AT KEY",
                "slug" => "AT-KEY",
                "value" => "b1db404857c3c8849f5dcaa416052a6f7112ecf40e36c4ad44d99c814276d962"
            ],
            [
                "title" => "AT FROM",
                "slug" => "AT-FROM",
                "value" => NULL
            ],
            [
                "title" => "SMS B2C REVERSAL MESSAGE",
                "slug" => "SMS-B2C-REVERSAL-MESSAGE",
                "value" => "{trxid} Confirmed. KSh. {amount} for order no. {order_number} has been reversaed successfully on {datetime}"
            ],
            [
                "title" => "Actual SMS Cost",
                "slug" => "ACTUAL-SMS-COST",
                "value" => "0.8"
            ],
        ];

        foreach ($preferences as $preference) {
            if (!SystemPreference::where("slug", $preference["slug"])->exists()) {
                SystemPreference::query()->create([
                    "identifier" => generate_identifier(),
                    "title" => $preference["title"],
                    "slug" => $preference["slug"],
                    "value" => $preference["value"],
                    "updated_at" => date('Y-m-d H:i:s'),
                    "updated_by" => 1
                ]);
            }
        }
    }
}
