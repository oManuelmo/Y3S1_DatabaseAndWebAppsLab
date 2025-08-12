<a href="{{ route('item.show', $follow->itemid) }}" class="item-container" data-id="{{ $follow->itemid }}">
    @php
        $item = $follow->getItem();
        $images = DB::table('product_images')
            ->join('images', 'product_images.imageid', '=', 'images.imageid')
            ->where('product_images.itemid', $follow->itemid)
            ->pluck('images.imageurl');
        $i = 0;
    @endphp
    <div class="existing-image" id="existing-image">
        @if (isset($images[$i]) && !empty($images[$i]))
            <img src="{{ Storage::url($images[$i]) }}" alt="Product Image" class="preview-image">
        @else
            <img src="{{ asset('no-image.png') }}" alt="Product Image" class="preview-image">
        @endif
    </div>

    <div class="countdown-container">
        <p class="countdown" data-deadline="{{ \Carbon\Carbon::parse($item['deadline'])->toIso8601String() }}" style="margin-top:1em">
            Time left: <span class="time">Loading...</span>
        </p>        
    </div>

    <p class="item-price">
        @if ($item->currentprice)
            Highest Bid: ${{ number_format($item->currentprice, 2) }}
        @else
            Starting Price: ${{ number_format($item->initialprice, 2) }}
        @endif
    </p>
    <p class="item-name">{{ $item->name }}</p>
    <p class="item-description">{{ $item->description }}</p>

</a>
