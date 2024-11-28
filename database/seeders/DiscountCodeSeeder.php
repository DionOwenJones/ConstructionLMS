<?php

namespace Database\Seeders;

use App\Models\DiscountCode;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class DiscountCodeSeeder extends Seeder
{
    public function run()
    {
        $discountCodes = [
            [
                'code' => 'WELCOME25',
                'description' => 'Welcome discount for new users - 25% off',
                'discount_percentage' => 25.0,
                'valid_from' => Carbon::now(),
                'valid_until' => Carbon::now()->addMonths(3),
                'is_active' => true,
                'usage_limit' => 100,
                'times_used' => 0
            ],
            [
                'code' => 'SUMMER2024',
                'description' => 'Summer special discount - 30% off all courses',
                'discount_percentage' => 30.0,
                'valid_from' => Carbon::now(),
                'valid_until' => Carbon::now()->addMonths(2),
                'is_active' => true,
                'usage_limit' => 50,
                'times_used' => 0
            ],
            [
                'code' => 'BUSINESS50',
                'description' => 'Special business discount - 50% off for business accounts',
                'discount_percentage' => 50.0,
                'valid_from' => Carbon::now(),
                'valid_until' => Carbon::now()->addYear(),
                'is_active' => true,
                'usage_limit' => 25,
                'times_used' => 0
            ],
            [
                'code' => 'EXPIRED15',
                'description' => 'Expired discount code for testing',
                'discount_percentage' => 15.0,
                'valid_from' => Carbon::now()->subMonths(2),
                'valid_until' => Carbon::now()->subMonth(),
                'is_active' => false,
                'usage_limit' => 100,
                'times_used' => 0
            ]
        ];

        foreach ($discountCodes as $discountCode) {
            DiscountCode::firstOrCreate(
                ['code' => $discountCode['code']],
                $discountCode
            );
        }

        $this->command->info('Created ' . count($discountCodes) . ' discount codes');
    }
}
