<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CorporationAuth
{
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::guard('corporation')->check()) {
            return redirect()->route('corporation.login');
        }

        $user = Auth::guard('corporation')->user();

        // Check if user is active
        if ($user->status !== 'active') {
            Auth::guard('corporation')->logout();
            return redirect()->route('corporation.login')->with('error', 'Your account is inactive.');
        }

        return $next($request);
    }
}
