@extends('layouts.app')

@section('content')
<link href="{{ url('css/main_page.css') }}" rel="stylesheet">
<script src="{{ asset('js/countdown.js') }}"></script>

@if ($results->isEmpty())
    <p style="font-size:30px !important; padding: 30px;" >No items found.</p>
@else
    <section id="items">
        @each('partials.item', $results, 'item')
        <article class="item">
        </article>
    </section>
@endif
<div>
    {{ $results->links() }}
</div>
@endsection
