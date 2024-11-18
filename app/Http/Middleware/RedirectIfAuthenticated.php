<?php

namespace App\Http\Middleware;

use App\Providers\RouteServiceProvider;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$guards): Response
    {
        $guards = empty($guards) ? [null] : $guards;

        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                $user = Auth::user();

                // If user is a business owner or employee
                if ($user->isBusinessOwner() || $user->isBusinessEmployee()) {
                    return redirect()->route('business.dashboard');
                }

                // If user is an admin
                if ($user->isAdmin()) {
                    return redirect()->route('admin.dashboard');
                }

                // Regular user
                return redirect(RouteServiceProvider::HOME);
            }
        }

        return $next($request);
    }
}
