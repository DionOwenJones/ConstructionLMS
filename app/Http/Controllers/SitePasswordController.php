<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SitePasswordController extends Controller
{
    public function show()
    {
        // If already verified, redirect to home
        if (session('site_password_verified')) {
            return redirect('/');
        }
        
        return view('auth.site-password');
    }

    public function check(Request $request)
    {
        $correctPassword = config('app.site_password');
        
        if ($request->password === $correctPassword) {
            session(['site_password_verified' => true]);
            return redirect()->intended('/');
        }

        return back()->with('error', 'Incorrect password. Please try again.');
    }
}
