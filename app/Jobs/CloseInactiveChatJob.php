<?php

namespace App\Jobs;

use App\Models\Chat;
use App\Models\Message;
use App\Events\MessageSent;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Events\ChatClosed;
use Carbon\Carbon;
use App\Jobs\CloseInactiveChat;

class CloseInactiveChatJob implements ShouldQueue
{
    protected $chat;

    public function __construct(Chat $chat)
    {
        $this->chat = $chat;
    }

    public function handle()
    {
        $inactiveChats = Chat::where('status', 'active')
            ->where('updatedat', '<', now()->subMinutes(5))
            ->get();
    
        foreach ($inactiveChats as $chat) {
            $warningMessage = Message::create([
                'chatid' => $chat->chatid,
                'senderid' => null,  
                'message' => "Due to inactivity, the chat will be closed in 15 seconds.",
                'createdat' => Carbon::now(),
            ]);
    
            broadcast(new MessageSent($warningMessage))->toOthers();
    
            $chat->update(['status' => 'closed']);
    
            broadcast(new ChatClosed($chat))->toOthers();
        }
    }
    public function closeInactiveChats()
    {
        CloseInactiveChat::dispatch();

        return response()->json(['message' => 'Inactive chats closure process started.']);
    }
    
}
