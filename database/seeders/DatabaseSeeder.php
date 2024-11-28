<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Business;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        // Create Admin Accounts
        $adminUsers = [
            [
                'name' => 'Super Admin',
                'email' => 'admin@admin.com',
                'password' => bcrypt('password'),
                'role' => 'admin',
            ],
            [
                'name' => 'Course Admin',
                'email' => 'course.admin@admin.com',
                'password' => bcrypt('password'),
                'role' => 'admin',
            ]
        ];

        foreach ($adminUsers as $admin) {
            User::create(array_merge($admin, [
                'email_verified_at' => now()
            ]));
        }

        // Create Business Accounts
        $businessUsers = [
            [
                'name' => 'Construction Corp',
                'email' => 'business@construction.com',
                'company' => 'Construction Corporation Ltd'
            ],
            [
                'name' => 'BuildRight Solutions',
                'email' => 'business@buildright.com',
                'company' => 'BuildRight Solutions Inc'
            ],
            [
                'name' => 'Safety First Co',
                'email' => 'business@safetyfirst.com',
                'company' => 'Safety First Construction'
            ]
        ];

        foreach ($businessUsers as $business) {
            // Create business owner first
            $businessOwner = User::create([
                'name' => $business['name'],
                'email' => $business['email'],
                'password' => bcrypt('password'),
                'role' => 'business',
                'email_verified_at' => now()
            ]);

            // Then create business and link to owner
            Business::create([
                'name' => $business['company'],
                'user_id' => $businessOwner->id,
                'email' => $business['email']
            ]);
        }

        // Create Regular User Accounts
        $regularUsers = [
            [
                'name' => 'John Worker',
                'email' => 'john@example.com',
            ],
            [
                'name' => 'Sarah Builder',
                'email' => 'sarah@example.com',
            ],
            [
                'name' => 'Mike Constructor',
                'email' => 'mike@example.com',
            ],
            [
                'name' => 'Test User',
                'email' => 'test@test.com',
            ]
        ];

        foreach ($regularUsers as $user) {
            User::create(array_merge($user, [
                'password' => bcrypt('password'),
                'role' => 'user',
                'email_verified_at' => now()
            ]));
        }

        // Run the Course Seeder
        $this->call(CourseSeeder::class);

        // Run the Discount Code Seeder
        $this->call(DiscountCodeSeeder::class);

        // Output seeding completion message
        $this->command->info('Database seeded with:');
        $this->command->info('- ' . User::where('role', 'admin')->count() . ' admin users');
        $this->command->info('- ' . User::where('role', 'business')->count() . ' business users');
        $this->command->info('- ' . User::where('role', 'user')->count() . ' regular users');
        $this->command->info('- ' . Business::count() . ' businesses');
        $this->command->info('- ' . \App\Models\DiscountCode::count() . ' discount codes');
    }
}
