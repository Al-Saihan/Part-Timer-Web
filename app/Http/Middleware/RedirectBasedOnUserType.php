<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RedirectBasedOnUserType
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check()) {
            $user = auth()->user();
            
            // Redirect to appropriate dashboard based on user_type
            if ($user->user_type === 'seeker') {
                return redirect()->route('seeker.dashboard');
            } elseif ($user->user_type === 'recruiter') {
                return redirect()->route('recruiter.dashboard');
            }
        }

        return $next($request);
    }
}
