<?php

namespace App\Providers;


use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\Item;
use App\Models\User;
use App\Models\Transaction;
use App\Models\Follow;
use App\Models\Chat;
use App\Policies\ItemPolicy;
use App\Policies\UserPolicy;
use App\Policies\TransactionPolicy;
use App\Policies\FollowPolicy;
use App\Policies\ChatPolicy;

class AuthServiceProvider extends ServiceProvider
{

    protected $policies = [
        Item::class => ItemPolicy::class,
        User::class => UserPolicy::class,
        Transaction::class => TransactionPolicy::class,
        Follow::class => FollowPolicy::class,
        Chat::class => ChatPolicy::class,
    ];


    public function boot(): void
    {
        $this->registerPolicies();
        Gate::define('admin-only', function (User $user) {
            return $user->isadmin;
        });
        Gate::define('userSelf-only', function (User $user1, User $user2) {
            return $user1->userid === $user2->userid;
        });
    }
}
