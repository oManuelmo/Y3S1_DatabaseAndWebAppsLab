@extends('layouts.app')

@section('content')
<link href="{{ url('css/login.css') }}" rel="stylesheet">

<form method="POST" action="{{ route('login') }}">
    {{ csrf_field() }}
    
    <fieldset>
        <legend>Login Details</legend>

        <label for="email">E-mail</label>
        <input id="email" type="email" name="email" maxlength="100" value="{{ old('email') }}" required autofocus>

        <label for="password">Password</label>
        <input id="password" type="password" maxlength="100" name="password" required>

        <label>
            <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}> Remember Me
        </label>
    </fieldset>

    <div class="auth">
        <button class="login-btn" type="submit">Login</button>
        <a class="button button-outline" href="{{ route('password.request') }}">Forgot Password?</a>
        <a class="button button-outline" href="{{ route('register') }}">Create an account</a>
    </div>

    @if (session('success'))
        <p class="success" style="color: green !important; text-align: center;">
            {{ session('success') }}
        </p>
    @endif

    @if ($errors->any())
        <div style=" color: #dc3545; text-align: center;" class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>
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
