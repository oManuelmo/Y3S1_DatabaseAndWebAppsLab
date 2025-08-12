<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\Bid;
use App\Models\Item;
use App\Models\User;
use App\Models\Follow;
use App\Models\Notification;
use App\Http\Controllers\ItemController;
use App\Events\ItemNotification;


class BidController extends Controller
{
    public function store(Request $request, $item_id)
    {
        $currentUser = $request->user();
        $item = Item::find($item_id);
        $this->authorize('bidItem', $item);
        if($currentUser == null){
            return response()->json([
                'success' => false,
                'message' => 'You must be logged in to place a bid.'
            ],404);
        }

        $request->validate([
            'amount' => 'required|numeric|min:0.01', 
        ]);
        
        if($request->amount > $currentUser->balance){
            return response()->json([
                'success' => false,
                'message' => 'Your balance is insufficient to place this bid.'
            ], 400);
        }

        $topBidder = $item->topbidder;

        if ($topBidder && $topBidder == $currentUser->userid) {
            return response()->json([
                'success' => false,
                'message' => 'You cannot bid because you are already the top bidder.'
            ], 400);
        }


        $currentUser->bidbalance = $currentUser->bidbalance + $request->amount;

        if($currentUser->bidbalance> $currentUser->balance){
            return response()->json([
                'success' => false,
                'message' => 'You have bid all of your balance.'
            ], 422);  
        }

        $itemController = new ItemController();
        $response = $itemController->bidItem($request, $item_id);

        if ($response->status() === 422) {
            return response()->json([
                'success' => false,
                'message' => 'Your bid must be higher than the current price.'
            ], 422);         
        }
        
        $currentTime = Carbon::now();
        $updatedItem = Item::find($item_id);

        $updatedItem->topbidder = $currentUser->userid;
        
        $currentUser->save();
        $deadLine = Carbon::parse($updatedItem->deadline);
        
        if ($currentTime->diffInMinutes($deadLine) <= 15) {
            $newDeadLine = $deadLine->addMinutes(30);
            $updatedItem->duration += 30;
            $updatedItem->deadline = $newDeadLine; 
            $updatedItem->save();
        }
        
        $updatedItem->save();

        $bid = new Bid();
        $bid->bidderid = $currentUser->userid; 
        $bid->itemid = $item_id;
        $bid->value = $request->amount;
        $bid->time = new \DateTime();
        $bid->save();

        $bidders =  Bid::where('itemid', $updatedItem->itemid)->with('bidder')->get()->pluck('bidder')->unique();
        $followers = Follow::where('itemid', $updatedItem->itemid)->with('user')->get()->pluck('user')->unique();
        $participants = $bidders->union($followers)->unique();
        $owner = User::find($updatedItem->ownerid);

        event(new ItemNotification($updatedItem,'newbidowner', $owner->userid));

        Notification::create([    
            'userid' => $owner->userid,
            'type' => 'newbidowner',
            'bidid' => $bid->bidid,
            'itemid' => $updatedItem->itemid,
            'itemname' => null,
            'transactionid' => null,
            'datetime' => Carbon::now(),
        ]);

        foreach($participants as $participant){

            if($participant->userid !== $currentUser->userid){

                event(new ItemNotification($updatedItem,'newbid', $participant->userid));

                Notification::create([    
                    'userid' => $participant->userid,
                    'type' => 'newbid',
                    'bidid' => $bid->bidid,
                    'itemid' => $updatedItem->itemid,
                    'itemname' => null,
                    'transactionid' => null,
                    'datetime' => Carbon::now(),
                ]);
            }

        }

        info('Response JSON:', [
            'success' => true,
            'message' => 'Your bid was placed successfully!',
            'item' => [
                'soldprice' => $updatedItem->soldprice,
                'deadline' => $updatedItem->deadline
            ]
        ]);
        
        return response()->json([
            'success' => true,
            'message' => 'Your bid was placed successfully!',
            'item' => [
                'soldprice' => $updatedItem->soldprice,
                'deadline' => $updatedItem->deadline
            ]
        ]);
    }
}