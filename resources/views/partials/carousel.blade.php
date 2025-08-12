<h1>Ending Auctions</h1>
<div id="custom-carousel" class="carousel">
    <div class="carousel-wrapper">
        @foreach($upcomingItems as $item)
            @php
                $images = DB::table('product_images')
                    ->join('images', 'product_images.imageid', '=', 'images.imageid')
                    ->where('product_images.itemid', $item['itemid'])
                    ->pluck('images.imageurl');
            @endphp
            <div class="carousel-item">
                <div class="carousel-item-content">
                    <div class="carousel-item-left">
                        @if (isset($images[0]) && !empty($images[0]))
                            <img src="{{ Storage::url($images[0]) }}" alt="Product Image" class="carousel-image">
                        @else
                            <img src="{{ asset('no-image.png') }}" alt="Product Image" class="carousel-image">
                        @endif
                    </div>
                    <div class="carousel-item-right">
                        <h3 class="carousel-item-name">{{ $item['name'] }}</h3>
                        <p class="carousel-item-description">{{ $item['description'] }}</p>

                        <p class="carousel-item-price">
                            @if ($item->soldprice)
                                Highest Bid: ${{ number_format($item->soldprice, 2) }}
                            @else
                                Starting Price: ${{ number_format($item->initialprice, 2) }}
                            @endif
                        </p>

                        <div class="countdown-container">
                            <p class="countdown" data-deadline="{{ \Carbon\Carbon::parse($item['deadline'])->toIso8601String() }}">
                                Time left: <span class="time">Loading...</span>
                            </p>
                        </div>
                        <div class="check-auction-link">
                            <a href="{{ route('item.show', $item['itemid']) }}" class="check-auction-link">Check Auction</a>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
    <div class="carousel-controls">
        <button class="carousel-control prev-control" id="prevButton">
            <span class="material-symbols-rounded">arrow_back_ios</span>
        </button>
        <button class="carousel-control next-control" id="nextButton">
            <span class="material-symbols-rounded">arrow_forward_ios</span>
        </button>
    </div>
</div>
