@extends('layouts.app')

@section('content')

    <form method="POST" action="{{ route('password.reset') }}">
        @csrf

        <label for="password">New Password</label>
        <input type="password" name="password" required>

        <label for="password_confirmation">Confirm Password</label>
        <input type="password" name="password_confirmation" required>

        <button type="submit">Reset Password</button>



        @error('password_confirmation')
            <div class="alert alert-danger" style="color: red;">{{ $message }}</div>
        @enderror

        @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li style="color: red;">
                    <span class="material-symbols-rounded">
                    warning
                    </span>
                    {{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    </form>
@endsection
