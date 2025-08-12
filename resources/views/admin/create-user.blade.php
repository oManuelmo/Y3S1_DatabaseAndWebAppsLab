@extends('layouts.admin')

@section('content')
<form method="POST" action="{{ route('admin.users.store') }}" id="create-user">
    {{ csrf_field() }}

    <fieldset>
        <legend>Personal Information</legend>
        <label for="firstname">First name</label>
        <input id="firstname" type="text" name="firstname" maxlength="50" value="{{ old('firstname') }}" required autofocus>
        @if ($errors->has('firstname'))
        <span class="error">
            <span class="material-symbols-rounded">warning</span>
            {{ $errors->first('firstname') }}
        </span>
        @endif
        <br>

        <label for="lastname">Last name</label>
        <input id="lastname" type="text" name="lastname" maxlength="50" value="{{ old('lastname') }}" required autofocus>
        @if ($errors->has('lastname'))
        <span class="error">
            <span class="material-symbols-rounded">warning</span>
            {{ $errors->first('lastname') }}
        </span>
        @endif
        <br>

        <label for="birthdate">Birthdate</label>
        <input id="birthdate" type="date" name="birthdate" value="{{ old('birthdate') }}" required>
        @if ($errors->has('birthdate'))
        <span class="error">
            <span class="material-symbols-rounded">warning</span>
            {{ $errors->first('birthdate') }}
        </span>
        @endif
        <br>
    </fieldset>

    <fieldset>
        <legend>Contact Information</legend>
        <label for="email">E-Mail address</label>
        <input id="email" type="email" name="email" maxlength="100" value="{{ old('email') }}" required>
        @if ($errors->has('email'))
        <span class="error">
            <span class="material-symbols-rounded">warning</span>
            {{ $errors->first('email') }}
        </span>
        @endif
        <br>

        <label for="phone_number">Phone number</label>
        <input type="text" id="phone_number" name="phone_number" maxlength="20" class="form-control" value="{{ old('phone_number') }}" required>
        @if ($errors->has('phone_number'))
        <span class="error">
            <span class="material-symbols-rounded">warning</span>
            {{ $errors->first('phone_number') }}
        </span>
        @endif
        <br>
    </fieldset>

    <fieldset>
        <legend>Account Information</legend>
        <label for="password">Password</label>
        <input id="password" type="password" name="password" maxlength="100" required>
        @if ($errors->has('password'))
        <span class="error">
            <span class="material-symbols-rounded">warning</span>
            {{ $errors->first('password') }}
        </span>
        @endif
        <br>

        <label for="password-confirm">Confirm Password</label>
        <input id="password-confirm" type="password" maxlength="100" name="password_confirmation" required>
        <br>

        <label for="isadmin">Admin Privileges</label>
        <select id="isadmin" name="isadmin" class="form-control">
            <option value="0">No</option>
            <option value="1">Yes</option>
        </select>
    </fieldset>

    <fieldset>
        <legend>Address Information</legend>
        <label for="country">Country</label>
        <select id="country" class="form-control @error('country') is-invalid @enderror" name="country" required>
            <option value="">Select a country</option>
            @foreach($countries as $country)
            <option value="{{ $country }}" {{ old('country') == $country ? 'selected' : '' }}>{{ $country }}</option>
            @endforeach
        </select>
        @if ($errors->has('country'))
        <span class="error">
            <span class="material-symbols-rounded">warning</span>
            {{ $errors->first('country') }}
        </span>
        @endif
        <br>

        <label for="city">City</label>
        <input type="text" id="city" name="city" class="form-control" maxlength="100" value="{{ old('city') }}" required>
        @if ($errors->has('city'))
        <span class="error">
            <span class="material-symbols-rounded">warning</span>
            {{ $errors->first('city') }}
        </span>
        @endif
        <br>

        <label for="postal_code">Postal Code</label>
        <input type="text" id="postal_code" name="postal_code" class="form-control" maxlength="20" value="{{ old('postal_code') }}" required>
        @if ($errors->has('postal_code'))
        <span class="error">
            <span class="material-symbols-rounded">warning</span>
            {{ $errors->first('postal_code') }}
        </span>
        @endif
        <br>

        <label for="address">Address</label>
        <input type="text" id="address" name="address" class="form-control" maxlength="100" value="{{ old('address') }}" required>
        @if ($errors->has('address'))
        <span class="error">
            <span class="material-symbols-rounded">warning</span>
            {{ $errors->first('address') }}
        </span>
        @endif
        <br>
    </fieldset>

    <button type="submit">Create user</button>
</form>
@endsection
