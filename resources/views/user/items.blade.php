@extends('layouts.app')

@section('content')
<link href="{{ asset('css/items.css') }}" rel="stylesheet">
<link href="{{ url('css/general.css') }}" rel="stylesheet">
<div class="container">
    <h1 class="items-heading mb-4">
        @if (Auth::id() == $userId)
            Your Items
        @else
            {{ $userName }}'s Items
        @endif
    </h1>
    <div class="btn-group mb-4" role="group">
        <button 
            class="btn {{ $currentState === 'Pending' ? 'btn-primary' : 'btn-secondary' }} filter-button"
            data-state="Pending"
            data-user-id="{{ $userId }}">
            Pending
        </button>
        <button 
            class="btn {{ $currentState === 'Auction' ? 'btn-primary' : 'btn-secondary' }} filter-button"
            data-state="Auction"
            data-user-id="{{ $userId }}">
            Auction
        </button>
        <button 
            class="btn {{ $currentState === 'NotSold' ? 'btn-primary' : 'btn-secondary' }} filter-button"
            data-state="NotSold"
            data-user-id="{{ $userId }}">
            Not Sold
        </button>
        <button 
            class="btn {{ $currentState === 'Sold' ? 'btn-primary' : 'btn-secondary' }} filter-button"
            data-state="Sold"
            data-user-id="{{ $userId }}">
            Sold
        </button>
        <button 
            class="btn {{ $currentState === 'Suspended' ? 'btn-primary' : 'btn-secondary' }} filter-button"
            data-state="Suspended"
            data-user-id="{{ $userId }}">
            Suspended
        </button>
    </div>
    
    <div id="items-container">
        @include('partials.items-list', ['items' => $items])
    </div>
    {{ $items->links() }}
</div>
@endsection
