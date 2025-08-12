@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>Edit Email and Password</h1>

        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        <form action="{{ route('profile.update.email-password', $user->userid) }}" method="POST">
            @csrf
            @method('PUT')

            <label for="email" class="form-label">Email</label>
            <input type="email" id="email" maxlength="100" name="email" value="{{ old('email', $user->email) }}" class="form-control" required>
            @if ($errors->has('email'))
            <span class="error">
            <span class="material-symbols-rounded">
                warning
                </span>
                {{ $errors->first('email') }}
            </span>
            @endif
            <br>
            <label for="password" class="form-label">New password (Leave blank to keep current password)</label>
            <input type="password" id="password" maxlength="100" name="password" class="form-control">
            @if ($errors->has('password'))
            <span class="error">
            <span class="material-symbols-rounded">
                warning
                </span>
                {{ $errors->first('password') }}
            </span>
            @endif
            <br>
            <label for="password_confirmation" class="form-label">Confirm new Password</label>
            <input type="password" id="password_confirmation" maxlength="100" name="password_confirmation" class="form-control">

            <button type="submit" class="btn btn-primary">Update Email and Password</button>
        </form>
    </div>
@endsection
