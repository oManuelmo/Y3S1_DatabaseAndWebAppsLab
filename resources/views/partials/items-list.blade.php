@if ($items->isEmpty())
    <p>No items found.</p>
@else
    <div class="line">
        @each('partials.user-item', $items, 'item')
    </div>
@endif
