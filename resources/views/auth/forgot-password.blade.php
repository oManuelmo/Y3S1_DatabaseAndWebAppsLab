@extends('layouts.app')

@section('content')
<form method="POST" action="{{ route('password.email') }}">
    @csrf
    <label for="email">Email</label>
    <input type="email" name="email" required>
    <p>This should be the email of your account</p>
    <button type="submit">Send Reset Code</button>
</form>
@endsection
