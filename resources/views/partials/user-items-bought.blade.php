<a href="{{ route('item.show', ['id' => $item->itemid]) }}" class="text-decoration-none">
    <div class="item-card border p-3 mb-3 rounded-md">
        <div class="item-body">
            <h5 class="item-title text-lg font-semibold text-gray-800">{{ $item->name }}</h5>
            <p class="item-text text-gray-600">
                Price: <span class="item-price">${{ number_format($item->soldprice, 2) }}</span><br>
                Status: <span class="item-status">{{ ucfirst($item->state) }}</span>
            </p>
        </div>
    </div>
</a>
