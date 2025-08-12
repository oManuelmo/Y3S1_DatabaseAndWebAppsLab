<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Follow;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class FollowController extends Controller
{
    public function toggleFollow(Request $request)
    {
        $userId = Auth::id();
        $itemId = $request->input('itemId');
        $follow = Follow::where('followerid', $userId)->where('itemid', $itemId);

        if ($follow->exists()) {
            $this->authorize('unfollow', $follow->first());
            $follow->delete();
            return response()->json(['message' => "User has unfollowed item with ID {$itemId}."]);
        } else {
            $newFollow = new Follow();
            $newFollow->followerid = $userId;
            $newFollow->itemid = $itemId;
            $newFollow->save(); 
            return response()->json(['message' => "User is now following item with ID {$itemId}."]);
        }
    }
    public function showFollowedItems($userid)
    {
        $user = User::findOrFail($userid);
        if (Gate::denies('userSelf-only', [User::findOrFail(Auth::id()), $user])) {
            return back()->withErrors('You cannot see this page.');
        }
        $followedItems = Follow::with('item')->where('followerid', $user->userid)->paginate(20);
        return view('pages.followed-items', compact('followedItems'));
    }
}