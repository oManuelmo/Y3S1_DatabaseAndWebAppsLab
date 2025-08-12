<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    public function editUser(User $user, User $targetUser): bool
    {
        return $user->isadmin || $user->userid === $targetUser->userid;
    }
    public function deleteUser(User $user, User $targetUser): bool
    {
        return $user->isadmin && $targetUser->userid !== $user->userid;
    }

    public function banUser(User $user, User $targetUser): bool
    {
        return $user->isadmin && $targetUser->userid !== $user->userid;
    }

    public function unbanUser(User $user, User $targetUser): bool
    {
        return $user->isadmin && $targetUser->userid !== $user->userid;
    }
    
    public function deleteOwnAccount(User $user, User $targetUser): bool
    {
        return $user->userid === $targetUser->userid;
    }
    public function viewTransactionMenu(User $user, User $targetUser): bool
    {
        return $user->userid === $targetUser->userid;
    }
    public function deposit(User $authUser, User $user)
    {
        return $authUser->userid === $user->userid;
    }
    public function rate(User $user, User $targetUser): bool
    {
        return $user->userid !== $targetUser->userid;
    }
    public function viewNotifications(User $user, User $targetUser) {
        return $user->userid !== $targetUser->userid;
    }
    public function createChat(User $user) {
        return !$user->isadmin;
    }
}

