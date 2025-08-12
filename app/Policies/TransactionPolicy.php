<?php

namespace App\Policies;

use App\Models\Transaction;
use App\Models\User;

class TransactionPolicy
{
    public function withdraw(User $user)
    {
        return $user->balance > 0;
    }
    
}
