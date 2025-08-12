<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Item;
use Illuminate\Support\Facades\DB;

class MainController extends Controller
{
    public function showMainPage()
    {
        $upcomingItems = Item::where('state', 'Auction')
            ->where('deadline', '>', now())
            ->orderBy('deadline')
            ->take(10)
            ->get();

        $items = Item::where('state', 'Auction')->paginate(12);

        return view('pages.main', [
            'items' => $items,
            'upcomingItems' => $upcomingItems
        ]);
    }

    public function fetchNextUpcomingItems(Request $request)
    {
        $lastDeadline = $request->query('lastDeadline');
        $count = $request->query('count', 1);
        $excludedIds = json_decode($request->query('excludedIds', '[]'));
    
        $nextItems = Item::where('state', 'Auction')
            ->where('deadline', '>', $lastDeadline)
            ->whereNotIn('itemid', $excludedIds) 
            ->orderBy('deadline')
            ->take($count)
            ->get()
            ->map(function ($item) {
                return [
                    'itemid' => $item->itemid,
                    'name' => $item->name,
                    'description' => $item->description,
                    'deadline' => $item->deadline,
                    'images' => DB::table('product_images')
                        ->join('images', 'product_images.imageid', '=', 'images.imageid')
                        ->where('product_images.itemid', $item->itemid)
                        ->pluck('images.imageurl')
                        ->toArray(),
                ];
            });
    
        return response()->json($nextItems);
    }
    
    
    
}
