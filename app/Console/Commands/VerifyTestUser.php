<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Illuminate\Support\Carbon;

class VerifyTestUser extends Command
{
    protected $signature = 'user:verify-test';
    protected $description = 'Verify the test user\'s email';

    public function handle()
    {
        $user = User::where('email', 'test@example.com')->first();

        if (!$user) {
            $this->error('Test user not found');
            return 1;
        }

        $user->forceFill([
            'email_verified_at' => Carbon::now(),
        ])->save();

        $this->info('Test user verified successfully');
        return 0;
    }
}
