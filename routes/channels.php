<?php

use Illuminate\Support\Facades\Broadcast;
use App\Models\User;


/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/
Broadcast::channel('user.{userId}', function (User $user, $userId) {
    // Autentica o canal apenas se o usuário autenticado for o mesmo do canal
    return (int) $user->userid === (int) $userId;
});