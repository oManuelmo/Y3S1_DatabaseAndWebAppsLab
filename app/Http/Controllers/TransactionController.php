<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Stripe\Stripe;
use Stripe\PaymentIntent;
use Illuminate\Support\Facades\Gate;

class TransactionController extends Controller
{
    public function showDepositForm()
    {
        return view('user/deposit');
    }
    public function showWithdrawForm()
    {
        return view('user/withdraw');
    }

    public function createDeposit(Request $request)
    {
        $validated = $request->validate([
            'depositValue' => 'required|numeric|min:1|max:999999.99',
            'payment_method_id' => 'required',
        ]);
        $userid = Auth::id();
        $user= User::find($userid);
        $this->authorize('deposit', $user);
        if ($user->balance + $validated['depositValue']>999999.99){
            back()->withErrors(['payment' => 'The maximum value on the account has been exceeded.']);
        }
        Stripe::setApiKey(env('STRIPE_SECRET'));
        
        try {
            $paymentIntent = PaymentIntent::create([
                'amount' => $validated['depositValue'] * 100,
                'currency' => 'usd',
                'payment_method' => $validated['payment_method_id'],
                'confirm' => true,
                'automatic_payment_methods' => [
                    'enabled' => true,
                    'allow_redirects' => 'never',
                ],
            ]);
    
            if ($paymentIntent->status === 'requires_action') {
                return redirect($paymentIntent->next_action->redirect_to_url->url);
            }
            if ($paymentIntent->status === 'succeeded') {
                
                $transaction = new Transaction();
                $transaction->userid = $userid;
                $transaction->transactiontype = "Deposit";
                $transaction->value = $validated['depositValue'];
                $transaction->time = now();
                $transaction->save();
                
                $user->increment('balance', $validated['depositValue']);
                
                return redirect()->route('profile.show', ['userid' => $userid]);
            } else {
                return back()->withErrors(['payment' => 'Payment failed. Please try again.']);
            }
        } catch (\Stripe\Exception\ApiErrorException $e) {
            return back()->withErrors(['payment' => $e->getMessage()]);
        }
    }
    
    public function createWithdraw(Request $request)
    {
        
        $validated = $request->validate([
            'withdrawValue' => 'required|numeric|min:1|max:999999.99',
            'iban' => 'required',
        ]);
        if (!(preg_match('/^[A-Z]{2}[0-9]{2}[0-9]{21}$/', $validated['iban']) || 
            preg_match('/^[A-Z]{2}[0-9]{2} [0-9]{4} [0-9]{4} [0-9]{4} [0-9]{4} [0-9]{4} [0-9]{1}$/', $validated['iban']))){
            return back()->withErrors(['IBAN' => 'The IBAN format is invalid.']);
        }
        $userid = Auth::id();
        $user= User::find($userid);
        $this->authorize('withdraw', Transaction::class);
        if($user->balance-$user->bidbalance>=$validated['withdrawValue']){
            $transaction = new Transaction();
            $transaction->userid = $userid;
            $transaction->transactiontype = "Withdraw";
            $transaction->value = $validated['withdrawValue'];
            $transaction->time = now();
            $transaction->save();
            $user->decrement('balance', $validated['withdrawValue']);
            return redirect()->route('profile.show', ['userid' => $userid]);
        }else{
            return back()->withErrors(['withdrawValue' => 'Insufficient balance for withdrawal.']);
        }
    }
    public function showTransactions()
    {
        $user= User::find(Auth::id());
        if (Gate::denies('userSelf-only', [$user, $user])) {
            return back()->withErrors('You cannot see this page.');
        }
        $transactions = Transaction::where('userid', Auth::id())->orderBy('time','desc')->get();
        return view('user.transactions', [
            'transactions' => $transactions,
        ]);
    }
    public function showMenu($userid)
    {
        $user = User::findOrFail($userid);
        $this->authorize('viewTransactionMenu', [$user, Transaction::class]);
        return view('user.transactions-menu', compact('user'));
    }
    
}