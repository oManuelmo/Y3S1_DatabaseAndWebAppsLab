@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>Edit Profile</h1>

        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif


        <form action="{{ route('profile.update.other-info', $user->userid) }}" method="POST">
            @csrf
            @method('PUT')

            <label for="firstname" class="form-label">First Name</label>
            <input type="text" id="firstname" name="firstname" maxlength="50" value="{{ old('firstname', $user->firstname) }}" class="form-control" required>
            @if ($errors->has('firstname'))
            <span class="error">
            <span class="material-symbols-rounded">
                warning
                </span>
                {{ $errors->first('firstname') }}
            </span>
            @endif
            <br>

            <label for="lastname" class="form-label">Last Name</label>
            <input type="text" id="lastname" name="lastname" maxlength="50" value="{{ old('lastname', $user->lastname) }}" class="form-control" required>
            @if ($errors->has('lastname'))
            <span class="error">
            <span class="material-symbols-rounded">
                warning
                </span>
                {{ $errors->first('lastname') }}
            </span>
            @endif
            <br>
            <label for="address" class="form-label">Address</label>
            <input type="text" id="address" name="address" maxlength="100" value="{{ old('address', $user->address) }}" class="form-control">
            @if ($errors->has('address'))
            <span class="error">
            <span class="material-symbols-rounded">
                warning
                </span>
                {{ $errors->first('address') }}
            </span>
            @endif
            <br>
            <label for="city" class="form-label">City</label>
            <input type="text" id="city" name="city" maxlength="100" value="{{ old('city', $user->city) }}" class="form-control">
            @if ($errors->has('city'))
            <span class="error">
            <span class="material-symbols-rounded">
                warning
                </span>
                {{ $errors->first('city') }}
            </span>
            @endif
            <br>
            <label for="country">Country</label>
            <select id="country" class="form-control @error('country') is-invalid @enderror" name="country" required>
            <option value="{{ old('country', $user->country) }}">{{ old('country', $user->country) }}</option>
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
            <br>
            <label for="postalcode" class="form-label">Postal Code</label>
            <input type="text" id="postalcode" name="postalcode" maxlength="20" value="{{ old('postalcode', $user->postalcode) }}" class="form-control">
            @if ($errors->has('postalcode'))
            <span class="error">
            <span class="material-symbols-rounded">
                warning
                </span>
                {{ $errors->first('postalcode') }}
            </span>
            @endif
            <br>
            <label for="phone" class="form-label">Phone</label>
            <input type="text" id="phone" name="phone" maxlength="20" value="{{ old('phone', $user->phone) }}" class="form-control">
            @if ($errors->has('phone'))
            <span class="error">
            <span class="material-symbols-rounded">
                warning
                </span>
                {{ $errors->first('phone') }}
            </span>
            @endif
            <br>
            <label for="birthdate" class="form-label">Birth Date</label>
            <input type="date" id="birthdate" name="birthdate" value="{{ old('birthdate', $user->birthdate->format('Y-m-d')) }}">
            @if ($errors->has('birthdate'))
            <span class="error">
            <span class="material-symbols-rounded">
                warning
                </span>
                {{ $errors->first('birthdate') }}
            </span>
            @endif
            <br>
            <button type="submit" class="btn btn-primary">Update Profile</button>
        </form>
    </div>
@endsection
