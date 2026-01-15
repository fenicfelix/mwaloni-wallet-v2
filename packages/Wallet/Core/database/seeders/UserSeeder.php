<?php

namespace Wallet\Core\Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // if (!User::where("id", "=", "1")->exists()) {
        //     User::query()->create([
        //         "id" => 1,
        //         "identifier" => generate_identifier(),
        //         "first_name" => "Akika",
        //         "last_name" => "Digital",
        //         "email" => "akika.digital@gmail.com",
        //         "phone_number" => "0788991605",
        //         "password" => Hash::make('password'),
        //     ]);
        // }

        User::where('email', 'akika.digital@gmail.com')->update([
            'password' => Hash::make('password')
        ]);
    }
}
