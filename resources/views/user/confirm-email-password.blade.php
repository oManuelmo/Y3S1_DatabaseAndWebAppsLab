@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>Confirm Your Email and Password</h1>

        @if ($errors->any())
            <div class="alert alert-danger">
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

        <form action="{{ route('profile.validate.email-password', $user->userid) }}" method="POST">
            @csrf

            <label for="email" class="form-label">Current Email</label>
            <input type="email" id="email" name="email" class="form-control" required>
            @if ($errors->has('email'))
            <span class="error">
            <span class="material-symbols-rounded">
                warning
                </span>
                {{ $errors->first('email') }}
            </span>
            @endif
            <br>

            <label for="password" class="form-label">Current Password</label>
            <input type="password" id="password" name="password" class="form-control" required>
            @if ($errors->has('password'))
            <span class="error">
            <span class="material-symbols-rounded">
                warning
                </span>
                {{ $errors->first('password') }}
            </span>
            @endif
            <br>

            <button type="submit" class="btn btn-primary">Confirm</button>
        </form>
    </div>
@endsection
