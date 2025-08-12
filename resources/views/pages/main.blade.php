@extends('layouts.app')

@section('title', 'Items')

@section('content')
<script src="{{ asset('js/main.js') }}"></script>
<link href="{{ url('css/main_page.css') }}" rel="stylesheet">

<section id="upcoming-carousel" style="position:relative; overflow:hidden;">
    @include('partials.carousel', ['upcomingItems' => $upcomingItems])
</section>

<section id="items">
    @each('partials.item', $items, 'item')
    <article class="item">
    </article>
</section>

<div>
    <script src="{{ asset('js/countdown.js') }}"></script>
    {{ $items->links() }}
</div>
@endsection
