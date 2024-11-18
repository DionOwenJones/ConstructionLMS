<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Str;

class SocialiteController extends Controller
{
    public function redirect($provider)
    {
        try {
            return Socialite::driver($provider)->redirect();
        } catch (Exception $e) {
            Log::error('Social login redirect error: ' . $e->getMessage());
            return redirect('/login')
                ->with('error', 'Unable to initialize social login. Please try again.');
        }
    }

    public function callback($provider)
    {
        try {
            $socialUser = Socialite::driver($provider)->user();
            
            if (!$socialUser || !$socialUser->getEmail()) {
                Log::error('Social login failed: No email provided');
                return redirect('/login')
                    ->with('error', 'Unable to get email from social login.');
            }

            $user = User::where('email', $socialUser->getEmail())->first();
            
            if (!$user) {
                $user = User::create([
                    'name' => $socialUser->getName() ?? 'User',
                    'email' => $socialUser->getEmail(),
                    'password' => bcrypt(Str::random(16)),
                    'social_id' => $socialUser->getId(),
                    'social_type' => $provider,
                    'social_avatar' => $socialUser->getAvatar(),
                ]);
            }

            Auth::login($user);
            
            return redirect()->intended('/dashboard');
            
        } catch (Exception $e) {
            Log::error('Social login callback error: ' . $e->getMessage());
            return redirect('/login')
                ->with('error', 'Social login failed. Please try again. Error: ' . $e->getMessage());
        }
    }
}
