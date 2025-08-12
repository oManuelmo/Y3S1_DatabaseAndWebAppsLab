@extends('layouts.app')

@section('content')
<link href="{{ asset('css/transactions.css') }}" rel="stylesheet">
<link href="{{ url('css/general.css') }}" rel="stylesheet">
<div class="container">
    <h1 class="transactions-heading mb-4">Transactions History</h1>
    <div id="transactions-container">
        @each('partials.transaction', $transactions, 'transaction')
    </div>
</div>
@endsection
