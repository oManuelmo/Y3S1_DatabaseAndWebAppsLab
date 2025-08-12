<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Item;

class CheckItemNotSuspended
{

    public function handle(Request $request, Closure $next)
    {
        $item = Item::find($request->route('item'));

        if (!$item->state === "Suspended") {
            return redirect()->back()->withErrors(['message' => 'The item is suspended.']);
        }

        return $next($request);
    }
}