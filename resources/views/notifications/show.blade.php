
@if($notifications->isEmpty())
    <p>You have no notifications.</p>
@else
    <ul class="list-group">
        @php
            $notifications = $notifications->sortByDesc('datetime');
        @endphp
        @foreach($notifications as $notification)
            <li class="list-group-item">
                @if($notification->type === 'top_bidder' && $notification->item)
                    <p>
                        <strong>Congratulations!</strong> 
                        You won the auction for 
                        <a href="{{ route('item.show', ['id' => $notification->item->itemid]) }}">{{ $notification->item->name }}</a> 
                        for ${{ $notification->item->soldprice }}!
                    </p>
                @elseif($notification->type === 'owner_soldItem' && $notification->item)
                    <p>
                        <strong>Item Sold:</strong> 
                        Your item 
                        <a href="{{ route('item.show', ['id' => $notification->item->itemid]) }}">{{ $notification->item->name }}</a> 
                        was sold for ${{ $notification->item->soldprice }}.
                    </p>
                @elseif($notification->type === 'owner_notSold' && $notification->item)
                    <p>
                        <strong>Item Not Sold:</strong> 
                        Unfortunately, your item 
                        <a href="{{ route('item.show', ['id' => $notification->item->itemid]) }}">{{ $notification->item->name }}</a> 
                        was not sold.
                    </p>
                @elseif($notification->type === 'endedgeneral' && $notification->item)
                    <p>
                        <strong>Auction Ended:</strong> 
                        The auction for item 
                        <a href="{{ route('item.show', ['id' => $notification->item->itemid]) }}">{{ $notification->item->name }}</a> 
                        has ended.
                    </p>
                @elseif($notification->type === 'ending5mingeneral' && $notification->item)
                    <p>
                        <strong>Auction Ending Soon:</strong> 
                        The auction for item 
                        <a href="{{ route('item.show', ['id' => $notification->item->itemid]) }}">{{ $notification->item->name }}</a> 
                        is about to end in just a few minutes!
                    </p>
                @elseif($notification->type === 'ending5minowner' && $notification->item)
                    <p>
                        <strong>Auction Ending Soon:</strong> 
                        The auction for your item 
                        <a href="{{ route('item.show', ['id' => $notification->item->itemid]) }}">{{ $notification->item->name }}</a> 
                        is about to end in just a few minutes!
                    </p>
                @elseif($notification->type === 'newbid' && $notification->bid && $notification->bid->bidder && $notification->item)
                    <p>
                        <strong>New Bid:</strong> 
                        User <strong>{{ $notification->bid->bidder->firstname }} {{ $notification->bid->bidder->lastname }}</strong> 
                        placed a bid on item 
                        <a href="{{ route('item.show', ['id' => $notification->item->itemid]) }}">{{ $notification->item->name }}</a>.
                    </p>
                @elseif($notification->type === 'newbidowner' && $notification->bid && $notification->bid->bidder && $notification->item)
                    <p>
                        <strong>New Bid:</strong> 
                        User <strong>{{ $notification->bid->bidder->firstname }} {{ $notification->bid->bidder->lastname }}</strong> 
                        placed a bid on your item 
                        <a href="{{ route('item.show', ['id' => $notification->item->itemid]) }}">{{ $notification->item->name }}</a>.
                    </p>
                @elseif($notification->type === 'winner' && $notification->item)
                    <p>
                        <strong>Winner Announced:</strong> 
                        The user <strong>{{ $notification->item->topBidder->firstname }} {{ $notification->item->topBidder->lastname }}</strong> 
                        won the auction for the item 
                        <a href="{{ route('item.show', ['id' => $notification->item->itemid]) }}">{{ $notification->item->name }}</a>.
                    </p>
                @elseif($notification->type === 'canceled')
                    <p>
                        <strong>Auction Cancelled:</strong> 
                        The auction for item <span style="color: red;">{{ $notification->itemname }}</span> was canceled by an Administrator.
                    </p>
                @elseif($notification->type === 'canceledowner')
                    <p>
                        <strong>Auction Cancelled:</strong> 
                        The auction for your item <span style="color: red;">{{ $notification->itemname }}</span> was canceled by an Administrator.
                    </p>
                @elseif($notification->type === 'suspended' && $notification->item)
                    <p>
                        <strong>Auction Suspended:</strong> 
                        The auction for item 
                        <a href="{{ route('item.show', ['id' => $notification->item->itemid]) }}">{{ $notification->item->name }}</a> 
                        was temporarily suspended by an Administrator.
                    </p>
                @elseif($notification->type === 'suspendedowner' && $notification->item)
                    <p>
                        <strong>Auction Suspended:</strong> 
                        The auction for your item 
                        <a href="{{ route('item.show', ['id' => $notification->item->itemid]) }}">{{ $notification->item->name }}</a> 
                        was temporarily suspended by an Administrator.
                    </p>
                @elseif($notification->type === 'unsuspended' && $notification->item)
                    <p>
                        <strong>Auction Resumed:</strong> 
                        The auction for item 
                        <a href="{{ route('item.show', ['id' => $notification->item->itemid]) }}">{{ $notification->item->name }}</a> 
                        is available again. Check it out!
                    </p>
                @else($notification->type === 'unsuspendedowner' && $notification->item)
                    <p>
                    <strong>Auction Resumed:</strong> 
                        The auction for your item 
                        <a href="{{ route('item.show', ['id' => $notification->item->itemid]) }}">{{ $notification->item->name }}</a> 
                        is available again. Check it out!
                    </p>
                @endif
                <p><small>Date: {{ \Carbon\Carbon::parse($notification->datetime)->format('d/m/Y H:i') }}</small></p>
            </li>
        @endforeach
    </ul>
@endif
