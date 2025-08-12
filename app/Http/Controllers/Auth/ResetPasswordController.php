<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class ResetPasswordController extends Controller
{
    public function showResetPasswordForm()
    {
        return view('auth.reset-password');
    }

    public function resetPassword(Request $request)
    {
        $email = session('reset_email');

        $request->validate([
            'password' => 'required|confirmed|min:8|max:100',
        ]);

        $user = User::where('email', $email)->first();

        if (!$user) {
            return back()->withErrors(['email' => 'No user found for this email.']);
        }

        $user->password = Hash::make($request->password);
        $user->resetcode = null;
        $user->resetcodeexpires = null;
        $user->save();

        session()->forget('reset_email');

        return redirect()->route('login')->with('success', 'Password reset successfully.');
    }

    
}
