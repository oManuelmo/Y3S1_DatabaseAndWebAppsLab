@extends('layouts.app')

@section('title', 'Items')

@section('content')

<section id="items">
    @each('partials.item', $items, 'item')
    <article class="item">
    </article>
</section>
<div>
    {{ $items->links() }}
</div>
@endsection