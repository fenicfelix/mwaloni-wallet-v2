<?php

namespace Wallet\Core\Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class SpartieSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //Permissions
        $path = 'database/seeders/dumps/spartie.sql';
        DB::unprepared(file_get_contents($path));

        $role = Role::where('id', 1)->first();
        $permissions = Permission::get();
        $role->syncPermissions($permissions);

        $this->command->info('Permissions Seeding Complete!');
    }
}
