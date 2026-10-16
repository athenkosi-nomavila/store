<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Lets only approved administrators through. Runs after `auth`, so the
 * visitor is logged in; a pending account (or one whose access was removed
 * while it was logged in) is logged out and sent back to the login page.
 */
class EnsureUserIsAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->user()?->isAdmin()) {
            Auth::guard('web')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('admin.login')
                ->withErrors(['email' => 'Your account is waiting for an admin to approve it.']);
        }

        return $next($request);
    }
}
