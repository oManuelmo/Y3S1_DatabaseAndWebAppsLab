<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class CheckBannedUser
{

    public function handle($request, Closure $next)
    {
        if (Auth::check()) {
            $user = User::findOrFail(Auth::user()->userid);
            if ($user->isBanned()) {
                Auth::logout();

                return redirect()->route('login')->withErrors([
                    'error' => 'Your account is banned until ' . $user->bantime
                ]);
            }
        }
        return $next($request);
    }
}
