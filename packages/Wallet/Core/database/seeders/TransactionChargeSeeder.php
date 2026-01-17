<?php

namespace Wallet\Core\Database\Seeders;

use Illuminate\Database\Seeder;
use Wallet\Core\Models\TransactionCharge;

class TransactionChargeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $charges = [
            ["id" => 1, "payment_channel_id" => 1, "charge" => 0, "minimum" => 1, "maximum" => 100],
            ["id" => 2, "payment_channel_id" => 1, "charge" => 4, "minimum" => 101, "maximum" => 1500],
            ["id" => 3, "payment_channel_id" => 1, "charge" => 8, "minimum" => 1501, "maximum" => 5000],
            ["id" => 4, "payment_channel_id" => 1, "charge" => 10, "minimum" => 5001, "maximum" => 20000],
            ["id" => 5, "payment_channel_id" => 1, "charge" => 12, "minimum" => 20001, "maximum" => 150000],
            ["id" => 6, "payment_channel_id" => 2, "charge" => 20, "minimum" => 0, "maximum" => 99999],
            ["id" => 7, "payment_channel_id" => 2, "charge" => 30, "minimum" => 100000, "maximum" => 249999],
            ["id" => 8, "payment_channel_id" => 2, "charge" => 50, "minimum" => 250000, "maximum" => 499999],
            ["id" => 9, "payment_channel_id" => 2, "charge" => 100, "minimum" => 500000, "maximum" => 999999],
            ["id" => 10, "payment_channel_id" => 3, "charge" => 20, "minimum" => 0, "maximum" => 99999],
            ["id" => 11, "payment_channel_id" => 3, "charge" => 30, "minimum" => 100000, "maximum" => 249999],
            ["id" => 12, "payment_channel_id" => 3, "charge" => 50, "minimum" => 250000, "maximum" => 499999],
            ["id" => 13, "payment_channel_id" => 3, "charge" => 100, "minimum" => 500000, "maximum" => 999999],
        ];

        if ($charges) {
            foreach ($charges as $charge) {
                if (!TransactionCharge::where("id", "=", $charge["id"])->exists()) {
                    $data = [
                        "id" => $charge["id"],
                        "payment_channel_id" => $charge["payment_channel_id"],
                        "minimum" => $charge["minimum"],
                        "maximum" => $charge["maximum"],
                        "charge" => $charge["charge"],
                        "added_by" => 1,
                        "updated_by" => 1,
                    ];
                    TransactionCharge::query()->create($data);
                }
            }
        }
    }
}
