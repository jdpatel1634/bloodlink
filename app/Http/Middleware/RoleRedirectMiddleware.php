<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleRedirectMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  $role
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        if (!Auth::check()) {
            // If not authenticated, redirect to the general login page or specific panel login.
            // This assumes Filament handles unauthenticated redirects, or you can specify.
            // For now, let Filament's default unauthenticated middleware handle it.
            return $next($request);
        }

        $user = Auth::user();

        // Check if the user has the required role
        if ($user->role !== $role) {
            // Auth::logout();
            // Determine the correct login route based on the attempted panel's role
            // This logic assumes Filament panel login routes follow a pattern like 'filament.<panel_id>.auth.login'
            $redirectRoute = match ($role) {
                'admin' => 'login',
                'donor' => 'login',
                'patient' => 'login',
                default => 'login', // Fallback to general login if role is unknown
            };

            return redirect()->back()->withErrors(['email' => 'You do not have permission to access this panel.']);
        }

        return $next($request);
    }
}
