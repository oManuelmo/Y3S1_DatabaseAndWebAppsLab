@extends('layouts.app')

@section('content')
<script src="https://js.stripe.com/v3/"></script>
<link href="{{ url('css/transactionform.css') }}" rel="stylesheet">
<script src="{{ asset('js/deposit.js') }}"></script>

<form id="deposit-form" method="POST" action="{{ route('deposit.create') }}">
    {{ csrf_field() }}
    
    <fieldset>
        <legend>Deposit Information</legend>

        <div id="card-container">
            <label for="card-element">Card Details</label>
            <div id="card-element"></div>
        </div>
        <div id="card-errors" role="alert"></div>

        <label for="depositValue" id="deposittitle">Deposit Value</label>
        <input id="depositValue" type="number" name="depositValue" min="1" max="999999.99" placeholder="10.00" required autofocus>
        @if ($errors->has('depositValue'))
            <span class="error">
            <span class="material-symbols-rounded">
                warning
                </span>
                {{ $errors->first('depositValue') }}
            </span>
        @endif

        @if ($errors->has('payment'))
            <div class="error">
            <span class="material-symbols-rounded">
                warning
                </span>
                {{ $errors->first('payment') }}
            </div>
        @endif
        
    </fieldset>

    <button style="font-size: 16px !important;" type="submit" id="deposit-button">Deposit</button>
</form>
@endsection
