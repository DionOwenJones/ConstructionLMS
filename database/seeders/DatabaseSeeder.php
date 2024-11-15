<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Business;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        // Create Admin Account
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
            'email_verified_at' => now()
        ]);

        // Create Business Account & Their Business
        $businessOwner = User::create([
            'name' => 'Business Owner',
            'email' => 'business@example.com',
            'password' => bcrypt('password'),
            'role' => 'business',
            'email_verified_at' => now()
        ]);

        // Create the Business Entity
        Business::create([
            'company_name' => 'Construction Co Ltd',
            'user_id' => $businessOwner->id
        ]);

        // Create Regular User Account
        User::create([
            'name' => 'Regular User',
            'email' => 'user@example.com',
            'password' => bcrypt('password'),
            'role' => 'user',
            'email_verified_at' => now()
        ]);

        // Run the Course Seeder
        $this->call(CourseSeeder::class);
    }
}
