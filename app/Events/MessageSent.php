<?php

namespace App\Events;

use App\Models\Message;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class MessageSent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $message;

    public function __construct(Message $message)
    {
        $this->message = $message;
        $this->message->load('sender');
    }

    public function broadcastOn()
    {
        return new Channel('chat.' . $this->message->chatid);
    }

    public function broadcastAs()
    {
        return 'MessageSent';
    }

    public function broadcastWith()
    {
        return [
            'message' => $this->message,
            'createdat' => \Carbon\Carbon::parse($this->message->createdat)->format('Y-m-d H:i:s'),
        ];
    }
}
