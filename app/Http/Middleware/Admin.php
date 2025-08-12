<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class Admin
{

    public function handle(Request $request, Closure $next)
    {
        if (Auth::check() && Auth::user()->isadmin) {
            return $next($request);
        }        

        return redirect()->route('main')->withErrors(['message' => 'You do not have access to this area.']);
    }
}
