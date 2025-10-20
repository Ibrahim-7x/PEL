<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

class SessionTimeoutMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Skip session timeout for other lightweight routes
        if ($request->is('favicon.ico')) {
            return $next($request);
        }

        // Only apply to authenticated users
        if (Auth::check()) {
            $lastActivity = Session::get('last_activity');
            $timeout = config('session.lifetime') * 60; // Convert minutes to seconds
            $warningTime = 15 * 60; // Show warning 15 minutes before expiry

            // If no last activity recorded, set it now
            if (!$lastActivity) {
                Session::put('last_activity', now()->timestamp);
            } else {
                $inactiveTime = now()->timestamp - $lastActivity;

                // Check if session has expired
                if ($inactiveTime >= $timeout) {
                    Auth::logout();
                    Session::flush();
                    Session::regenerate();

                    // Return JSON response for AJAX requests
                    if ($request->expectsJson()) {
                        return response()->json([
                            'message' => 'Your session has expired due to inactivity. Please login again.',
                            'expired' => true,
                            'redirect' => route('login')
                        ], 401);
                    }

                    // Redirect to login for regular requests
                    return redirect()->route('login')->with('error', 'Your session has expired due to inactivity. Please login again.');
                }

                // Show warning if approaching timeout (but not for AJAX requests to avoid spam)
                if ($inactiveTime >= ($timeout - $warningTime) && !$request->expectsJson()) {
                    $remainingMinutes = round(($timeout - $inactiveTime) / 60);
                    Session::flash('session_warning', "Your session will expire in {$remainingMinutes} minute(s) due to inactivity.");
                }

                // Update last activity time
                Session::put('last_activity', now()->timestamp);
            }
        }

        return $next($request);
    }
}