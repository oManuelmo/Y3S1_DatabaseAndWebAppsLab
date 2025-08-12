@extends('layouts.app')

@section('content')
    <link href="{{ url('css/transactionform.css') }}" rel="stylesheet">

    <form method="POST" action="{{ route('withdraw.create') }}">
        {{ csrf_field() }}
        
        <fieldset>
            <legend>Withdraw Information</legend>

            <label for="IBAN">IBAN</label>
            <input id="IBAN" type="text" placeholder="PT50000201231234567890154" name="iban" maxlength="31" value="{{ old('iban') }}" required autofocus>
            @if ($errors->has('IBAN'))
                <span class="error">
                <span class="material-symbols-rounded">
                warning
                </span>
                    {{ $errors->first('IBAN') }}
                </span>
            @endif

            <label for="withdrawValue" id="withdrawtitle">Withdraw Value</label>
            <input id="withdrawValue" type="number" name="withdrawValue" min="1" max="999999.99" placeholder="10.00" required autofocus>
            @if ($errors->has('withdrawValue'))
                <span class="error">
                <span class="material-symbols-rounded">
                warning
                </span>
                    {{ $errors->first('withdrawValue') }}
                </span>
            @endif

        </fieldset>

        <button style="font-size: 16px !important;" type="submit" id="withdraw">
            Withdraw
        </button>
    </form>
@endsection
