<?php

namespace App\Events;

use App\Models\Chat;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Support\Facades\Log;

class ChatClosed implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $chat; 

    public function __construct(Chat $chat)
    {
        $this->chat = $chat; 
    }

    public function broadcastOn()
    {
        return new Channel('chat.' . $this->chat->chatid); 
    }

    public function broadcastAs()
    {
        return 'ChatClosed';
    }
}
