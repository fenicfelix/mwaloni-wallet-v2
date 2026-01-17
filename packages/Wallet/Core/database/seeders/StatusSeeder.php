<?php

namespace Wallet\Core\Database\Seeders;

use Illuminate\Database\Seeder;
use Wallet\Core\Models\Status;

class StatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $statuses = ["Submitted", "Success", "Failed", "Cancelled", "Reversing", "Pending", "Reversed", "Quering Status"];
        foreach ($statuses as $status) {
            if (!Status::where("name", "=", $status)->exists()) {
                Status::query()->create([
                    "name" => $status
                ]);
            }
        }
    }
}
