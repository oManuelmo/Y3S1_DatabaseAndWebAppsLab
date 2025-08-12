<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Item;

class CheckAuctionNotEnded
{

    public function handle(Request $request, Closure $next)
    {
        $item = Item::find($request->route('item'));

        if (!$item) {
            return redirect()->back()->withErrors(['message' => 'Item not found.']);
        }

        if ($item->state === "Sold" || $item->state === "NotSold") {
            return redirect()->back()->withErrors(['message' => 'The auction for this item has already ended.']);
        }

        return $next($request);
    }
}
