@extends('layouts.admin')

@section('title', 'Edit User')

@section('content')
<section id="edit-user">
    <div>
        <h1 class="text-center mb-4">Edit User: {{ $user->firstname }} {{ $user->lastname }}</h1>

        <form action="{{ route('admin.users.update', $user->userid) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <fieldset>
                <legend>Personal Information</legend>

                <label for="firstname">First Name</label>
                <input type="text" id="firstname" name="firstname" maxlength="50" class="form-control" value="{{ old('firstname', $user->firstname) }}" required>
                @if ($errors->has('firstname'))
                <span class="error">
                <span class="material-symbols-rounded">warning</span>
                {{ $errors->first('firstname') }}
                </span>
                @endif
                <br>

                <label for="lastname">Last Name</label>
                <input type="text" id="lastname" name="lastname" maxlength="50" class="form-control" value="{{ old('lastname', $user->lastname) }}" required>
                @if ($errors->has('lastname'))
                <span class="error">
                <span class="material-symbols-rounded">warning</span>
                {{ $errors->first('lastname') }}
                </span>
                @endif
                <br>

                <label for="email">Email</label>
                <input type="email" id="email" name="email" maxlength="100" class="form-control" value="{{ old('email', $user->email) }}" required>
                @if ($errors->has('email'))
                <span class="error">
                <span class="material-symbols-rounded">warning</span>
                {{ $errors->first('email') }}
                </span>
                @endif
                <br>

                <label for="password">Password (Leave blank to keep current)</label>
                <input type="password" id="password" maxlength="100" name="password" class="form-control">
                @if ($errors->has('password'))
                <span class="error">
                <span class="material-symbols-rounded">warning</span>
                {{ $errors->first('password') }}
                </span>
                @endif
            </fieldset>

            <fieldset>
                <legend>Privileges</legend>
                <label for="isadmin">Admin Privileges</label>
                <select id="isadmin" name="isadmin" class="form-control">
                    <option value="0" {{ !$user->isadmin ? 'selected' : '' }}>No</option>
                    <option value="1" {{ $user->isadmin ? 'selected' : '' }}>Yes</option>
                </select>
            </fieldset>

            <fieldset>
                <legend>Address Information</legend>

                <label for="address">Address</label>
                <input type="text" id="address" name="address" maxlength="100" class="form-control" value="{{ old('address', $user->address) }}">
                @if ($errors->has('address'))
                <span class="error">
                <span class="material-symbols-rounded">warning</span>
                {{ $errors->first('address') }}
                </span>
                @endif
                <br>

                <label for="city">City</label>
                <input type="text" id="city" name="city" maxlength="100" class="form-control" value="{{ old('city', $user->city) }}">
                @if ($errors->has('city'))
                <span class="error">
                <span class="material-symbols-rounded">warning</span>
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
                <span class="material-symbols-rounded">warning</span>
                {{ $errors->first('country') }}
                </span>
                @endif
                <br>

                <label for="postalcode">Postal Code</label>
                <input type="text" id="postalcode" name="postalcode" maxlength="20" class="form-control" value="{{ old('postalcode', $user->postalcode) }}">
                @if ($errors->has('postalcode'))
                <span class="error">
                <span class="material-symbols-rounded">warning</span>
                {{ $errors->first('postalcode') }}
                </span>
                @endif
            </fieldset>

            <fieldset>
                <legend>Contact & Financial Information</legend>

                <label for="phone">Phone</label>
                <input type="text" id="phone" name="phone" maxlength="20" class="form-control" value="{{ old('phone', $user->phone) }}">
                @if ($errors->has('phone'))
                <span class="error">
                <span class="material-symbols-rounded">warning</span>
                {{ $errors->first('phone') }}
                </span>
                @endif
                <br>

                <label for="balance">Balance</label>
                <input type="number" id="balance" name="balance" class="form-control" min="0" max="9999999.99" autofocus value="{{ old('balance', $user->balance) }}">
                @if ($errors->has('balance'))
                <span class="error">
                <span class="material-symbols-rounded">warning</span>
                {{ $errors->first('balance') }}
                </span>
                @endif
                <br>

                <label for="bidbalance">Bid Balance</label>
                <input type="number" id="bidbalance" name="bidbalance" class="form-control" min="0" max="9999999.99" autofocus value="{{ old('bidbalance', $user->bidbalance) }}">
                @if ($errors->has('bidbalance'))
                <span class="error">
                <span class="material-symbols-rounded">warning</span>
                {{ $errors->first('bidbalance') }}
                </span>
                @endif
            </fieldset>

            <fieldset>
                <legend>Birthdate</legend>

                <label for="birthdate"></label>
                <input type="date" id="birthdate" name="birthdate" class="form-control" value="{{ old('birthdate', $user->birthdate->format('Y-m-d')) }}">
                @if ($errors->has('birthdate'))
                <span class="error">
                <span class="material-symbols-rounded">warning</span>
                {{ $errors->first('birthdate') }}
                </span>
                @endif
                <br>
            </fieldset>

            <button type="submit" class="btn btn-primary btn-block">Update User</button>
        </form>
    </div>
</section>
@endsection
