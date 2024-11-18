<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class TestEmailController extends Controller
{
    public function testEmail()
    {
        try {
            $user = auth()->user();
            
            if (!$user) {
                return 'Please login first';
            }

            // Test 1: Basic Mail Send
            Mail::raw('Test email from Construction LMS', function($message) use ($user) {
                $message->to($user->email)
                    ->subject('Test Email');
                
                Log::info('Attempting to send email to: ' . $user->email);
            });
            
            Log::info('Basic email test completed');

            // Test 2: Verification Notification
            try {
                $user->sendEmailVerificationNotification();
                Log::info('Verification email sent');
            } catch (\Exception $e) {
                Log::error('Verification email failed: ' . $e->getMessage());
                return 'Error sending verification email: ' . $e->getMessage();
            }

            return 'Email tests completed. Check logs for details.';

        } catch (\Exception $e) {
            Log::error('Email test failed: ' . $e->getMessage());
            return 'Error: ' . $e->getMessage();
        }
    }
}
