<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  $role
     * @return mixed
     */
    public function handle(Request $request, Closure $next, $role)
    {
        if (!Auth::check()) {
            return redirect('login');
        }

        $user = Auth::user();
        
        // If checking for business role
        if ($role === 'business') {
            if (!$user->hasRole('business')) {
                return redirect()->route('dashboard')
                    ->with('error', 'Access denied. Business account required.');
            }
        }
        // If checking for user role
        else if ($role === 'user') {
            if ($user->hasRole('business')) {
                return redirect()->route('business.dashboard');
            }
            if ($user->hasRole('admin')) {
                return redirect()->route('admin.dashboard');
            }
        }
        // For other roles
        else if (!$user->hasRole($role)) {
            return redirect()->route('dashboard')
                ->with('error', 'You do not have permission to access this area.');
        }

        return $next($request);
    }
}
