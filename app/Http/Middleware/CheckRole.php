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
    public function handle(Request $request, Closure $next, string $role)
    {
        if (!Auth::check()) {
            return redirect('login');
        }

        $user = $request->user();
        $roles = explode('|', $role);

        // Special handling for 'employee' role
        if (in_array('employee', $roles)) {
            if (!$user->isEmployee()) {
                abort(403, 'This section is only accessible to business employees.');
            }
            return $next($request);
        }

        // Check if user has any of the specified roles
        if (!$user->hasRole($roles)) {
            abort(403, 'Unauthorized action. You do not have the required role.');
        }

        return $next($request);
    }
}
