<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\MailModel;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Mail;

class ForgotPasswordController extends Controller
{
    public function showForgotPasswordForm()
    {
        $email = session('email');
        return view('auth.forgot-password');
    }

    public function sendResetCode(Request $request)
    {

        $request->validate(['email' => 'required|email|exists:users,email']);
    
        $resetCode = rand(1000, 9999); 
    
        $user = User::where('email', $request->email)->first();
        $user->resetcode = $resetCode;
        $user->resetcodeexpires = now()->addMinutes(15); 
        $user->save();

        $mailData = [
            'firstname' => $user->firstname,
            'email' => $user->email,
            'code' => $user->resetcode,
        ];
        
        session(['reset_email' => $request->email]);
        
        Mail::to($request->email)->send(new MailModel($mailData));
    
        return redirect()->route('password.verify')->with('email', $request->email);
    }


    

    public function showVerifyCodeForm()
    {
        return view('auth.verify-code');
    }

    public function verifyResetCode(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'code' => 'required|numeric'
        ]);
    
        $user = User::where('email', $request->email)->first();
    
        if (!$user || $user->resetcode != $request->code) {
            return back()->withErrors(['code' => 'Invalid reset code.']);
        }
    
        if ($user->reset_code_expires && $user->reset_code_expires->isPast()) {
            return back()->withErrors(['code' => 'Reset code has expired.']);
        }
    
        $user->resetcode = null;
        $user->resetcodeexpires = null;
        $user->save();
    
        return redirect()->route('password.reset')->with('email', $request->email);
    }
    
}
