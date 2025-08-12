@extends('layouts.app')

@section('title', 'Items You Are Following')

@section('content')
<script src="{{ asset('js/countdown.js') }}"></script>
<link href="{{ url('css/followed-items.css') }}" rel="stylesheet">
<div class="container mt-5">
    <h1 class="mb-4">Items You're Following</h1>

    @if($followedItems->isEmpty())
        <p class="text-muted">You are not following any items yet.</p>
    @else
        <section id="items" class="row">
            @each('partials.followed-items', $followedItems, 'follow')
        </section>
        {{ $followedItems->links() }}
    @endif

</div>
@endsection
