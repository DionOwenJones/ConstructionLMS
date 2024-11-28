<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BusinessAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::check()) {
            return redirect('login');
        }

        $user = Auth::user();

        // Check if user is a business owner (not an employee)
        if (!$user->isBusinessOwner()) {
            return redirect()->route('dashboard');
        }

        // Check if user has a business record
        if (!$user->business) {
            return redirect()->route('profile.upgrade.business.setup')
                ->with('warning', 'Please set up your business profile first.');
        }

        return $next($request);
    }
}
