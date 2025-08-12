<article class="item">
    <a href="{{ route('item.show', ['id' => $item->itemid]) }}" class="item-info">
        <p>ID: {{ $item->itemid }}</p>
        <p>Onwer: {{ $item->getOwner()->firstname }} {{ $item->getOwner()->lastname }}</p>
        <p>Name: {{ $item->name }}</p>
        <p class="item-state">State: {{ $item->state }}</p> 
    </a>
    <section class="admin-item-buttons">
        @php
            $hasBids = $item->bids->isNotEmpty();
            $isEditable = !$hasBids && in_array($item->state, ['Auction', 'Pending']);
            $canSuspend = in_array($item->state, ['Auction']);
        @endphp
        <a  
            style = "font-size: 15px !important;"
            href="{{ $isEditable ? route('admin.items.edit', $item->itemid) : '#' }}" 
            class="button {{ !$isEditable ? 'disabled' : '' }}" 
            {{ !$isEditable ? 'aria-disabled=true tabindex=-1' : '' }}
            title="{{ $hasBids ? 'You cannot edit an item with bids' : '' }}">
            Edit
        </a>
        <form method="POST" style="display:inline;">
            @csrf
            @method('DELETE')
            @if ($isEditable)
                <button 
                    style = "font-size: 15px !important;"
                    type="submit" 
                    class="button delete-item-button"
                    data-item-id="{{ $item->itemid }}"
                    onclick="return confirm('Are you sure you want to delete this item?')">
                    Delete
                </button>
            @else
                <button 
                    style = "font-size: 15px !important;"
                    disabled
                    type="button" 
                    class="button disabled delete-item-button"
                    title="{{ $hasBids ? 'You cannot delete an item with bids' : 'This item cannot be deleted in its current state' }}">
                    Delete
                </button>
            @endif
        </form> 
        <form style = "font-size: 15px !important;" class="suspend-item-form" data-item-id="{{ $item->itemid }}" style="display: inline;">
            @csrf
            @if ($item->state === 'Suspended')
                <button 
                    style = "font-size: 15px !important;"
                    type="button" 
                    class="button unsuspend-item-button" 
                    data-item-id="{{ $item->itemid }}">
                    Unsuspend
                </button>
            @elseif ($item->state === 'Auction')
                <button 
                    style = "font-size: 15px !important;"
                    type="button" 
                    class="button suspend-item-button" 
                    data-item-id="{{ $item->itemid }}">
                    Suspend
                </button>
            @else
                <button 
                    style = "font-size: 15px !important;"
                    disabled 
                    type="button" 
                    class="button disabled suspend-item-button" 
                    title="This item cannot be suspended or unsuspended in its current state.">
                    Suspend / Unsuspend
                </button>
            @endif
        </form>
    </section>
</article>
