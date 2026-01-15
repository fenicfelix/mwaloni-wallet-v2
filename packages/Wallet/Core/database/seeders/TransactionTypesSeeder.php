<?php

namespace Wallet\Core\Database\Seeders;

use Illuminate\Database\Seeder;
use Wallet\Core\Models\TransactionType;

class TransactionTypesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $types = [
            ["name" => "Payments", "description" => "Used to send money from business to customer e.g. refunds."],
            ["name" => "AccountBalance", "description" => "Used to check the balance in a paybill/buy goods account (includes utility, MMF, Merchant, Charges paid account)."],
            ["name" => "Reversal", "description" => "Reversal for an erroneous C2B transaction."],
            ["name" => "Cashout", "description" => "Cashing out generated revenue."],
            ["name" => "Service Charge", "description" => "Monthly service charges"],
            ["name" => "Distribute", "description" => "Distribute from client account to service account"],
            ["name" => "Withdraw", "description" => "Withdraw cash from a service"],
            ["name" => "Revenue Transfer", "description" => "Revenue transfer from  a service to an account"],
        ];

        if ($types) {
            foreach ($types as $type) {
                if (!TransactionType::where("name", "=", $type["name"])->exists()) {
                    $data = [
                        "identifier" => generate_identifier(),
                        "name" => $type["name"],
                        "description" => $type["description"]
                    ];
                    TransactionType::query()->create($data);
                }
            }
        }
    }
}
