
@extends('layouts.app')

@section('content')
    @php
        $images = DB::table('product_images')
            ->join('images', 'product_images.imageid', '=', 'images.imageid')
            ->where('product_images.itemid', $item['itemid'])
            ->pluck('images.imageurl');
    @endphp
    <div class="container">
    <link href="{{ url('css/bid-history.css') }}" rel="stylesheet">
        <div class="item-details">
            <h2><a href="{{ route('item.show', ['id' => $item->itemid]) }}" class="item-info">{{ $item->name }}</a></h2>
            @if($images->isEmpty())
                <img src="{{ asset('no-image.png') }}" alt="Image for {{ $item->name }}">   
            @else
                <img src="{{ asset('storage/' . $images[0]) }}" alt="Image for {{ $item->name }}">
            @endif
            <p>{{ $item->description }}</p>
            <p><strong>Starting Price:</strong> ${{ $item->initialprice }}</p>
        </div>

        <div class="bid-history">
            <h3>Bid History</h3>
            @if ($bids->isEmpty())
                <p>No bids placed for this item.</p>
            @else
                <table>
                    <thead>
                        <tr>
                            <th>Bidder</th>
                            <th>Bid Amount</th>
                            <th>Bid Time</th>
                        </tr>
                    </thead>
                    <tbody>
                        @each('partials.bid', $bids, 'bid')  
                    </tbody>
                </table>
            @endif
        </div>
    </div>
@endsection
