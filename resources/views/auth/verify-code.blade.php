@extends('layouts.app')

@section('content')
<form method="POST" action="{{ url('/reset-code') }}">
    @csrf
    <label for="email">Email</label>
    <input type="email" name="email" value="{{ session('email') }}" readonly>
    <label for="code">Reset Code</label>
    <input type="number" name="code" required>
    <button type="submit">Verify Code</button>
    @if ($errors->has('code'))
            <span class="error">
                <span class="material-symbols-rounded">
                warning
                </span>
                {{ $errors->first('code') }}
            </span>
        @endif
</form>
@endsection
