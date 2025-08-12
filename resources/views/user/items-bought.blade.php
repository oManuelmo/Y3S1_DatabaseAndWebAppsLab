@extends('layouts.app')

@section('content')
<link href="{{ asset('css/items.css') }}" rel="stylesheet">
<link href="{{ url('css/general.css') }}" rel="stylesheet">
<div class="container">
    <h1 class="items-heading mb-4">
        @if (Auth::id() == $userId)
            Your bought Items
        @else
            {{ $userName }}'s bought Items
        @endif
    </h1>
    <div id="items-container">
        @each('partials.user-items-bought', $items, 'item')
    </div>
    {{ $items->links() }}
</div>
@endsection