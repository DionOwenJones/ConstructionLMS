<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Business;
use Illuminate\Support\Facades\Hash;

class RoleSeeder extends Seeder
{
    public function run()
    {
        // Create admin user
        User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin User',
                'email' => 'admin@example.com',
                'password' => Hash::make('password'),
                'role' => 'admin'
            ]
        );

        // Create business user and associated business
        $businessUser = User::firstOrCreate(
            ['email' => 'business@example.com'],
            [
                'name' => 'Business User',
                'email' => 'business@example.com',
                'password' => Hash::make('password'),
                'role' => 'business'
            ]
        );

        // Create associated business record
        Business::firstOrCreate(
            ['user_id' => $businessUser->id],
            [
                'user_id' => $businessUser->id,
                'company_name' => 'Test Business'
            ]
        );

        // Create regular user
        User::firstOrCreate(
            ['email' => 'user@example.com'],
            [
                'name' => 'Regular User',
                'email' => 'user@example.com',
                'password' => Hash::make('password'),
                'role' => 'user'
            ]
        );
    }
}
