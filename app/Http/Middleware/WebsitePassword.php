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
        Log::info('WebsitePassword middleware checking path: ' . $request->path());

        // Only allow access to the password entry page and its assets
        $allowedPaths = [
            'site-password',
            'check-site-password',
            'css/*',
            'js/*',
            'images/*',
            'build/*',
            'favicon.ico',
            '_debugbar/*'
        ];

        // Check if the current path is in the allowed list
        $isAllowedPath = false;
        foreach ($allowedPaths as $path) {
            if ($request->is($path)) {
                Log::info('Allowing access to whitelisted path: ' . $request->path());
                $isAllowedPath = true;
                break;
            }
        }

        if ($isAllowedPath) {
            return $next($request);
        }

        // Check if password is verified in session
        if (!Session::has('site_password_verified')) {
            Log::info('Password not verified, redirecting to password page');
            return redirect()->route('site.password');
        }

        Log::info('Password verified, allowing access');
        return $next($request);
    }
}
