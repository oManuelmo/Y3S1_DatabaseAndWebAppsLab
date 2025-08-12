<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Item;

class ItemPolicy
{

    public function editItem(User $user, Item $item): bool
    {
        return ($user->isadmin || $user->userid === $item->ownerid) && !$item->bids()->exists();
    }

    public function deleteItem(User $user, Item $item): bool
    {
        return ($user->isadmin || $user->userid === $item->ownerid) && !$item->bids()->exists();
    }

    public function acceptItem(User $user, Item $item): bool
    {
        return $user->isadmin && $item->state === 'Pending'; 
    }
    
    public function bidItem(User $user, Item $item): bool
    {
        return $user->userid !== $item->ownerid && $item->state === 'Auction';
    }
    public function reportItem(User $user, Item $item) : bool
    {
        return $user->userid !== $item->ownerid && $item->state === 'Auction';
    }
}
