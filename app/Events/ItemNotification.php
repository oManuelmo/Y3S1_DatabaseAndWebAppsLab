<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;


class ItemNotification implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $message;
    public $item;
    public $type;
    public $user_id;


    public function __construct($item, $type, $user_id)
    {
        $this->item = $item;
        $this->type = $type;
        $this->user_id = $user_id;
        $this->message = match ($type) {
            'top_bidder' => "Congratulations! You won the auction for '{$item->name}' for {$item->soldprice}$!",
            'owner_soldItem' => "Your item '{$item->name}' was sold for {$item->soldprice}$.",
            'owner_notSold' => "Unfortunately, your item '{$item->name}' was not sold.",
            'endedgeneral' => "The auction for the item '{$item->name}' has ended!",
            'ending5mingeneral' => "The auction for the item '{$item->name}' is about to end in just a few minutes!",
            'ending5minowner' => "The auction for your item '{$item->name}' is about to end in just a few minutes!",
            'newbid' => "{$item->topBidder->firstname} {$item->topBidder->lastname} placed  a new bid on the item '{$item->name}'.",
            'newbidowner' => "{$item->topBidder->firstname} {$item->topBidder->lastname} placed  a new bid on your item '{$item->name}'.",
            'winner' => "The user {$item->topBidder->firstname} {$item->topBidder->lastname} won the auction for the item '{$item->name}'!",
            'canceled' => "The auction for the item '{$item->name}' was canceled by an Administrator!",  
            'suspended' => "The auction for the item '{$item->name}' was suspended temporarily by an Administrator!",
            'unsuspended' => "The auction for the item '{$item->name}' is available again. Check it out!",
            'canceledowner' => "The auction for your item '{$item->name}' was canceled by an Administrator!",  
            'suspendedowner' => "The auction for your item '{$item->name}' was suspended temporarily by an Administrator!",
            'unsuspendedowner' => "The auction for your item '{$item->name}' is available again.",

        };  
    }   

    
    public function broadcastOn()
    {   
        return new Channel('user.' . $this->user_id);
    }


    public function broadcastAs()
    {
        return 'item-notification';
    }
}