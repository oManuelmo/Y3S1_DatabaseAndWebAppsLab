<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Carbon\Carbon;
use App\Models\Item;
use App\Models\User;
use App\Models\Bid;
use App\Models\Follow;
use App\Models\Notification;
use App\Events\ItemNotification;
use App\Models\Transaction;


class NotifyEndAuction extends Command
{
 
    protected $signature = 'notify:end-auction';

    protected $description = 'Send notification to users that an auction ended';

    public function __construct()
    {
        parent::__construct();
    }


    public function handle()
    {
        info('teste minuto a minuto');
        
        $timeNow = Carbon::now();
        $timePlus5min = Carbon::now()->addMinutes(5);
        $timePlus4min = Carbon::now()->addMinutes(4);


        $endedItems = Item::where('deadline','<=',$timeNow)->where('state', 'Auction')->get();
        $endingItems = Item::where('deadline', '>=', $timePlus4min)->where('deadline', '<=', $timePlus5min)->where('state', 'Auction')->get();
        
        foreach ($endedItems as $item) {
            $topBider = User::find($item->topbidder);
            $owner = User::find($item->ownerid);

            $bidders_ =  Bid::where('itemid', $item->itemid)->with('bidder')->get()->pluck('bidder')->unique();

            $followers_ = Follow::where('itemid', $item->itemid)->with('user')->get()->pluck('user')->unique();

            $participants_ = $bidders_->union($followers_)->unique();  

            if($topBider){
                $item->state = 'Sold';
                $topBider->balance = $topBider->balance - $item->soldprice;
                $topBider->bidbalance = $topBider->bidbalance - $item->soldprice;
                $topBider->save();
                info("bora pras transactions");
                $transaction = new Transaction();
                $transaction->userid = $item->ownerid;
                $transaction->transactiontype = "Selling";
                $transaction->value = $item->soldprice;
                $transaction->time = now();
                $transaction->save();

                $transaction = new Transaction();
                $transaction->userid = $item->topbidder;
                $transaction->transactiontype = "Buying";
                $transaction->value = $item->soldprice;
                $transaction->time = now();
                $transaction->save();

                $owner->balance = $owner->balance + $item->soldprice;
                $owner->save();

                info('Item vendido com sucesso!', [
                    'item_id' => $item->itemid,
                    'item_owner' => $item->ownerid,
                    'top_bidder_id' => $topBider->userid,
                    'sold_price' => $item->soldprice,
                ]);
                

                //manda notificacao para o dono do item a dizer q o item foi vendido com sucesso
                event(new ItemNotification($item,'owner_soldItem', $owner->userid));

                //manda notificacao para o topbidder a dizer q o item foi comprado com sucesso
                event(new ItemNotification($item,'top_bidder',$topBider->userid));

                //notificacao para o owner do item a dizer que o seu item foi vendido
                Notification::create([    
                    'userid' => $owner->userid,
                    'type' => 'owner_soldItem',
                    'bidid' => null,
                    'itemid' => $item->itemid,
                    'itemname' => null,
                    'transactionid' => null,
                    'datetime' => Carbon::now(),
                ]);

                //notificacao para o topbidder do item a dizer que o comprou o item
                Notification::create([    
                    'userid' => $topBider->userid,
                    'type' => 'top_bidder',
                    'bidid' => null,
                    'itemid' => $item->itemid,
                    'itemname' => null,
                    'transactionid' => null,
                    'datetime' => Carbon::now(),
                ]);

            }
            else{
                info('nao tem topbidder');
                $item->state = 'NotSold';

                //manda notificacao para o dono do item a dizer que o item nao foi vendido
                if($owner){
                    event(new ItemNotification($item,'owner_notSold', $owner->userid));
                }

                //notificacao para o owner do item a dizer que o seu item nao foi comprado
                Notification::create([    
                    'userid' => $owner->userid,
                    'type' => 'owner_notSold',
                    'bidid' => null,
                    'itemid' => $item->itemid,
                    'itemname' => null,
                    'transactionid' => null,
                    'datetime' => Carbon::now(),
                ]);

            }
            $item->save();  

            foreach($participants_ as $participant_){

                Notification::create([    
                    'userid' => $participant_->userid,
                    'type' => 'endedgeneral',
                    'bidid' => null,
                    'itemid' => $item->itemid,
                    'itemname' => null,
                    'transactionid' => null,
                    'datetime' => Carbon::now(),
                ]);


                if($topBider){
                    if($participant_->userid !== $topBider->userid ){
                        //manda notificacao para todos aqueles que estao a seguir o item ou que deram bid 
                        event(new ItemNotification($item,'winner', $participant_->userid));

                        Notification::create([    
                            'userid' => $participant_->userid,
                            'type' => 'winner',
                            'bidid' => null,
                            'itemid' => $item->itemid,
                            'itemname' => null,
                            'transactionid' => null,
                            'datetime' => Carbon::now(),
                        ]);
                    }
                }
                else{
                    event(new ItemNotification($item,'endedgeneral', $participant_->userid)); 

                    Notification::create([    
                        'userid' => $participant_->userid,
                        'type' => 'endedgeneral',
                        'bidid' => null,
                        'itemid' => $item->itemid,
                        'itemname' => null,
                        'transactionid' => null,
                        'datetime' => Carbon::now(),
                    ]);
                }
            }

        }

        foreach ($endingItems as $item){
            $owr = User::find($item->ownerid);

            event(new ItemNotification($item,'ending5minowner', $owr->userid));

            Notification::create([    
                'userid' => $owr->userid,
                'type' => 'ending5minowner',
                'bidid' => null,
                'itemid' => $item->itemid,
                'itemname' => null,
                'transactionid' => null,
                'datetime' => Carbon::now(),
            ]);

            $bidders = Bid::where('itemid', $item->itemid)->with('bidder')->get()->pluck('bidder')->unique();
            $followers = Follow::where('itemid', $item->itemid)->with('user')->get()->pluck('user')->unique();

            $participants = $bidders->union($followers)->unique(); 

            foreach($participants as $participant){

                Notification::create([    
                    'userid' => $participant->userid,
                    'type' => 'ending5mingeneral',
                    'bidid' => null,
                    'itemid' => $item->itemid,
                    'itemname' => null,
                    'transactionid' => null,
                    'datetime' => Carbon::now(),
                ]);

                event(new ItemNotification($item,'ending5mingeneral', $participant->userid));
            }

        }
            
    }
}
