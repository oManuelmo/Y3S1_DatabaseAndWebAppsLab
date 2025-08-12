<?php

namespace App\Policies;

use App\Models\User;

use App\Models\Follow;

class FollowPolicy
{
    public function unfollow(User $user, ?Follow $follow)
    {
        return $follow === null || $user->userid === $follow->followerid;
    }
}
