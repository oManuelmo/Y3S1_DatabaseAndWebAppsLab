<?php

namespace App\Jobs;

use App\Models\Chat;
use App\Events\ChatClosed;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable; 
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;

class CloseInactiveChat implements ShouldQueue
{
    use Dispatchable, Queueable, SerializesModels, InteractsWithQueue;

    protected $chat;

    public function __construct(Chat $chat)
    {
        $this->chat = $chat;
    }

    public function handle()
    {
        $this->chat->update(['status' => 'closed']);

        broadcast(new ChatClosed($this->chat))->toOthers();
    }
}

