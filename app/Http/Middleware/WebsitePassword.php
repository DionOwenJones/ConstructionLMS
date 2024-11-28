<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class WebsitePassword
{
    public function handle(Request $request, Closure $next)
    {
        $password = config('app.site_password');
        
        // Only allow access to the password entry page and its assets
        $allowedPaths = [
            'site-password',
            'check-site-password',
            'css/*',
            'js/*',
            'images/*',
            'build/*',
            'favicon.ico',
            'courses',
            'courses/*'
        ];

        // Check if the current path is in the allowed list
        foreach ($allowedPaths as $path) {
            if ($request->is($path)) {
                return $next($request);
            }
        }

        // If no password is verified in session, redirect to password page
        if (!Session::has('site_password_verified')) {
            return redirect()->route('site.password');
        }

        return $next($request);
    }
}
