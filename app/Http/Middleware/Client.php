<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class Client
{

    public function handle(Request $request, Closure $next)
    {
        if (Auth::check() && !Auth::user()->isadmin) {
            return $next($request);
        }        
        return redirect()->route('main')->withErrors(['message' => "You can't do this things as an admin."]);
    }
}
