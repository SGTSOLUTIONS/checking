<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfAuthenticatedCustom
{
    public function handle(Request $request, Closure $next): Response
    {
        // Web guard
        if (Auth::guard('web')->check()) {

            $user = Auth::guard('web')->user();

            if ($user->role == 'admin') {
                return redirect()->route('admin.dashboard');
            }

            if ($user->role == 'team_leader') {
                return redirect()->route('teamleader.dashboard');
            }

            if ($user->role == 'surveyor') {
                return redirect()->route('surveyor.dashboard');
            }
        }

        // Corporation guard
        if (Auth::guard('corporation')->check()) {
            return redirect()->route('corporation.dashboard');
        }

        return $next($request);
    }
}
