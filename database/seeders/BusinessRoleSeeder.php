<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use App\Models\User;

class BusinessRoleSeeder extends Seeder
{
    public function run()
    {
        // Create business role if it doesn't exist
        if (!Role::where('name', User::ROLE_BUSINESS)->exists()) {
            Role::create([
                'name' => User::ROLE_BUSINESS,
                'guard_name' => 'web'
            ]);
        }
    }
}
