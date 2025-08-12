<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Chat;

class ChatPolicy
{
    public function sendMessage(User $user, Chat $chat): bool
    {
        return $user->isadmin || $user->userid === $chat->userid;
    }
    public function seeMessages(User $user, Chat $chat): bool
    {
        return $user->isadmin || $user->userid === $chat->userid;
    }
    public function closeChat(User $user, Chat $chat): bool
    {
        return $user->isadmin || $user->userid === $chat->userid;
    }
}
