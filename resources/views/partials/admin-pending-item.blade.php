<article class="pending-item">
    <a href="{{ route('item.show', ['id' => $item->itemid]) }}" class="item-info">
        <p>ID: {{ $item->itemid }}</p>
        <p>Onwer: {{ $item->getOwner()->firstname }} {{ $item->getOwner()->lastname }}</p>
        <p>Name: {{ $item->name }}</p>
    </a>
    <section class="admin-pending-item-buttons">
        <button class="button accept-button" data-item-id="{{ $item->itemid }}">Accept</button>
    </section>
</article>
