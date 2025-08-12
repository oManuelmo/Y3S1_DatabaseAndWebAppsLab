@extends('layouts.app')

@section('content')
<script src="{{ asset('js/item.js') }}"></script>
<script src="{{ asset('js/countdown.js') }}"></script>
<script src="{{ asset('js/report.js') }}" defer></script>
<link href="{{ asset('css/item.css') }}" rel="stylesheet">
<link href="{{ url('css/general.css') }}" rel="stylesheet">
<link href="{{ url('css/report.css') }}" rel="stylesheet">
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
<div class="container item-details">
<head>
    <meta name= "csrf-token" content= "{{ csrf_token() }}">
</head> 
<div class="container">
    <h1 class="mb-4">
        @if($item->state !== "Sold" && $item->state !== "Not Sold")
            Auction Details
        @else
            Item Details
        @endif
    </h1>
    
    <div class="item">
        <div class="item-body">
            <div class="item-flex">
                <div class="item-carousel">
                    <div class="carousel-container">
                        <div class="carousel-wrapper">
                            @if($images->isEmpty())
                                <img src="{{ asset('no-image.png') }}" alt="Image for {{ $item->name }}">   
                            @else
                                <div class="carousel-items">
                                    @foreach($images as $image)
                                        <div class="carousel-item">
                                            <img src="{{ asset('storage/' . $image->imageurl) }}" alt="Image for {{ $item->name }}">
                                        </div>
                                    @endforeach
                                </div>
                                @if(count($images) > 1)
                                    <div class="carousel-arrow-container">
                                        <button class="carousel-button prev-button" id="prevButton">
                                            <span class="material-symbols-rounded">arrow_back_ios</span>
                                        </button>
                                        <button class="carousel-button next-button" id="nextButton">
                                            <span class="material-symbols-rounded">arrow_forward_ios</span>
                                        </button>
                                    </div>
                                @endif
                            @endif
                        </div>
                    </div>
                    @if (Auth::check() && $item->state !== "Sold" && $item->state !== "NotSold" && $item->state !== "Suspended" && Auth::id() != $item->ownerid)
                        @if(!Auth::user()->isadmin)
                            <button id="followBtn" class="follow-btn follow"
                            data-item-id="{{ $item->itemid }}" 
                            data-is-following="{{ $isFollowing }}"
                            data-toggle-follow-url="{{ route('toggle-follow') }}"></button>
                        @endif
                    @endif

                    @if (Auth::id() === $item->ownerid && ($item->state == "Pending" || ($item->state == "Auction" && !$item->bids()->exists())))
                        <form action="{{ route('item.delete', $item->itemid) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this item?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">Delete</button>
                        </form>
                        <form action="{{ route('item.edit', $item->itemid) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-primary">Edit</button>
                        </form>
                    @endif


                    <form action="{{ route('item.bids.history', ['id' => $item->itemid]) }}" method="GET">
                        @csrf
                        <button type="submit" class="button">See bid history</button>
                    </form>
                    @if (Auth::check() && Auth::id() != $item->ownerid)
                        <button id="reportAuctionBtn" class="btn btn-warning">Report Auction</button>
                    @endif

                    @if (Auth::check() & Auth::id() != $item->ownerid && $item->state == "Auction")
                        @if(!Auth::user()->isadmin)
                            @if(Auth::user()->userid == $item->topbidder)
                                <form id="bid-form" action="{{ route('bids.store', $item->itemid) }}" method="POST">
                                    @csrf
                                    <label class="bid-amount" for="amount">Bid Amount:</label>
                                    <input type="number" name="amount" placeholder="You are the top bidder" required min="0.01" max="9999999.99" step="0.01" autofocus>
                                    <span class="material-symbols-rounded" data-tooltip="The bid must be greater then the highest bid">info</span>
                                    @if ($errors->has('amount'))
                                        <div class="alert alert-danger mt-2">
                                            <strong>{{ $errors->first('amount') }}</strong>
                                        </div>
                                    @endif

                                    <div id="message-container"></div>
                                    <button type="submit">Place Bid</button>
                                </form>
                            @else
                                <form id="bid-form" action="{{ route('bids.store', $item->itemid) }}" method="POST">
                                    @csrf
                                    <label class="bid-amount" for="amount">Bid Amount:</label>
                                    <input type="number" name="amount" placeholder="{{ number_format(($item->soldprice ?? $item->initialprice)*0.1+($item->soldprice ?? $item->initialprice), 2) }}" required min="0.01" max="9999999.99" step="0.01" autofocus>
                                    <span class="material-symbols-rounded" data-tooltip="The bid must be greater then the highest bid">info</span>
                                    @if ($errors->has('amount'))
                                        <div class="alert alert-danger mt-2">
                                            <strong>{{ $errors->first('amount') }}</strong>
                                        </div>
                                    @endif

                                    <div id="message-container"></div>
                                    <button type="submit">Place Bid</button>
                                </form>
                            @endif
                        @endif
                    @endif

                </div>

                <div class="item-details-text">
                    <h5 class="item-title">{{ $item->name }}</h5>
                    <p class="item-text">
                        <strong>
                            @if($item->state !== "Sold")
                                Highest Bid:
                            @else
                                Sold Price:
                            @endif
                        </strong>
                        <span id="current-price"> ${{ number_format($item->soldprice ?? $item->initialprice, 2) }}</span>
                        @if ($item->state !== "Sold" && $item->state !== "Not Sold" && $item->state !== "Pending" && $item->state !== "Suspended")
                            <div class="countdown-container">
                                <p id="deadline" class="countdown" data-deadline="{{ \Carbon\Carbon::parse($item['deadline'])->toIso8601String() }}" style="margin-top:1em">
                                    Time left: <span class="time">Loading...</span> 
                                </p>
                            </div>
                        @endif
                        <br>
                        <strong>Description:</strong> {{ $item->description }}<br>
                        <div id="sep" style="width: 100%; border-top: 2px solid gray;"></div>
                    </p>
                    <p class="item-text">
                        
                        @if (Auth::id() != $item->ownerid && $artistName === "Me")
                        <strong>Artist:</strong> {{ $item->owner->firstname }}<br>
                        @else
                        <strong>Artist:</strong> {{ ucfirst($artistName) }}<br>
                        @endif
                        <strong>Width:</strong> {{ $item->width }} cm<br>
                        <strong>Height:</strong> {{ $item->height }} cm<br>
                        <strong>Theme:</strong> {{ $item->theme }}<br>
                        <strong>Style:</strong> {{ $item->style }}<br>
                        <strong>Technique:</strong> {{ $item->technique }}<br>
                    </p>    

                    <p class="item-seller">
                        <strong>Seller:</strong>
                        <a class="seller-info" href="{{ route('profile.show', $item->owner->userid) }}">
                            <img src="{{ $item->owner->image ? Storage::url($item->owner->image->imageurl) : asset('profile.png') }}" alt="{{ $item->owner->firstName }} {{ $item->owner->lastName }}" class="seller-pfp">
                            <span class="seller-name">{{ ucfirst($item->owner->firstname) }} {{ ucfirst($item->owner->lastname) }}</span>
                        </a><br>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="reportPopup" class="report-popup" style="display: none;">
    <div class="popup-content">
        <h3>Report Auction</h3>
        <form id="reportForm" action="{{ route('item.report') }}" method="POST">
            @csrf
            <input type="hidden" name="reportedauction" value="{{ $item->itemid }}">
            <label for="type">Reason:</label>
            <select id="type" name="type" required>
                @foreach($reportTypes as $reportType)
                    <option value="{{ $reportType->value }}">{{ $reportType->value }}</option>
                @endforeach
            </select>
            <label for="reportText">Details:</label>
            <textarea id="reportText" name="reportText" rows="4" placeholder="Describe the issue (optional)"></textarea>
            <div class="popup-actions">
                <button type="submit" class="btn btn-primary">Submit</button>
                <button type="button" id="closePopupBtn" class="btn btn-secondary">Cancel</button>
            </div>
        </form>
    </div>
</div>

<div id="popupOverlay" class="popup-overlay" style="display: none;"></div>


<script src="{{ asset('js/follow.js') }}" defer></script>
@endsection
