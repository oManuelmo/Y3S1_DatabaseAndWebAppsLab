@extends('layouts.app')

@section('content')
<form method="POST"  style="margin-bottom: 5em;"action="{{ route('register') }}">
    {{ csrf_field() }}
    
    <fieldset>
        <legend>Personal Information</legend>
        
        <label for="firstname">First name</label>
        <input id="firstname" type="text" maxlength="50" name="firstname" value="{{ old('firstname') }}" required autofocus>
        @if ($errors->has('firstname'))
            <span class="error">
            <span class="material-symbols-rounded">
                warning
                </span>
                {{ $errors->first('firstname') }}
            </span>
        @endif
        <br>

        <label for="lastname">Last name</label>
        <input id="lastname" type="text" maxlength="50" name="lastname" value="{{ old('lastname') }}" required>
        @if ($errors->has('lastname'))
            <span class="error">
            <span class="material-symbols-rounded">
                warning
                </span>
                {{ $errors->first('lastname') }}
            </span>
        @endif
        <br>

        <label for="birthdate">Birthdate</label>
        <input id="birthdate" type="date" name="birthdate" value="{{ old('birthdate') }}" required>
        @if ($errors->has('birthdate'))
            <span class="error">
            <span class="material-symbols-rounded">
                warning
                </span>
                {{ $errors->first('birthdate') }}
            </span>
        @endif
    </fieldset>

    <fieldset>
        <legend>Account Information</legend>

        <label for="email">E-Mail address</label>
        <input id="email" type="email" maxlength="100" name="email" value="{{ old('email') }}" required>
        @if ($errors->has('email'))
            <span class="error">
            <span class="material-symbols-rounded">
                warning
                </span>
                {{ $errors->first('email') }}
            </span>
        @endif
        <br>

        <label for="phone_number">Phone number</label>
        <input type="text" id="phone_number" maxlength="20" name="phone_number" class="form-control" value="{{ old('phone_number') }}" required>
        @if ($errors->has('phone_number'))
            <span class="error">
            <span class="material-symbols-rounded">
                warning
                </span>
                {{ $errors->first('phone_number') }}
            </span>
        @endif
        <br>

        <label for="password">Password</label>
        <input id="password" type="password" maxlength="100" name="password" required>
        @if ($errors->has('password'))
            <span class="error">
            <span class="material-symbols-rounded">
                warning
                </span>
                {{ $errors->first('password') }}
            </span>
        @endif
        <br>

        <label for="password-confirm">Confirm Password</label>
        <input id="password-confirm" type="password" maxlength="100" name="password_confirmation" required>
    </fieldset>

    <fieldset>
        <legend>Location Information</legend>

        <label for="country">Country</label>
        <select id="country" class="form-control @error('country') is-invalid @enderror" name="country" required>
            <option value="">Select a country</option>
            @foreach($countries as $country)
                <option value="{{ $country }}" {{ old('country') == $country ? 'selected' : '' }}>{{ $country }}</option>
            @endforeach
        </select>
        @if ($errors->has('country'))
            <span class="error">
            <span class="material-symbols-rounded">
                warning
                </span>
                {{ $errors->first('country') }}
            </span>
        @endif

        <label for="city">City</label>
        <input type="text" id="city" name="city" maxlength="100" class="form-control" value="{{ old('city') }}" required>
        @if ($errors->has('city'))
            <span class="error">
            <span class="material-symbols-rounded">
                warning
                </span>
                {{ $errors->first('city') }}
            </span>
        @endif

        <label for="postal_code">Postal Code</label>
        <input type="text" id="postal_code" name="postal_code" maxlength="20" class="form-control" value="{{ old('postal_code') }}" required>
        @if ($errors->has('postal_code'))
            <span class="error">
            <span class="material-symbols-rounded">
                warning
                </span>
                {{ $errors->first('postal_code') }}
            </span>
        @endif

        <label for="address">Address</label>
        <input type="text" id="address" name="address" maxlength="100" class="form-control" value="{{ old('address') }}" required>
        @if ($errors->has('address'))
            <span class="error">
            <span class="material-symbols-rounded">
                warning
                </span>
                {{ $errors->first('address') }}
            </span>
        @endif
    </fieldset>
    <div style="display: flex; flex-direction: column;">
      <button type="submit" style="font-size: 20px !important;">
          Register
      </button>
      <a class="button button-outline" href="{{ route('login') }}">Login</a>
    </div>
</for>
@endsection
