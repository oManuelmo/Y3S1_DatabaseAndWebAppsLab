<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;
use App\Models\Item;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Support\Facades\Gate;

class NotificationController extends Controller
{

    public function showNotifications($userid)
    {
        $user = User::findOrFail($userid);
        if (Gate::denies('userSelf-only', [User::findOrFail(Auth::id()), $user])) {
            return back()->withErrors('You cannot see this page.');
        }
        if (auth()->id() != $userid) {
            return redirect()->route('login')->with('error', 'You have no right to access this notifications.');
        }

       
        $notifications = Notification::where('userid', $userid)->get();
        return view('notifications.show', compact('notifications'));
    }
}
