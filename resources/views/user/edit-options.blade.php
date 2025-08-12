@extends('layouts.app')

@section('title', 'Edit Profile Options')


@section('content')
    <link href="{{ asset('css/profile-options.css') }}" rel="stylesheet">
    <script src="{{ asset('js/delete-confirm.js') }}" defer></script>
    <div class="container mx-auto p-4">
        <h1 class="text-3xl font-semibold mb-6">Profile Settings</h1>
        <div class="bg-white shadow-lg rounded-lg mb-6 p-6">
            <p class="mb-4">Choose an option below to update your profile:</p>
            <div class="options">
                <a style="font-size: 20px !important;" href="{{ route('profile.confirm.email-password', $user->userid) }}" class="button-standard">
                    Edit Email & Password
                </a>
                <a style="font-size: 20px !important;" href="{{ route('profile.edit.other-info', $user->userid) }}" class="button-standard">
                    Edit Other Information
                </a>
                <form class="delete" action="{{ route('profile.delete', $user->userid) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button style="font-size: 20px !important;" type="button" class="button-danger">
                        Delete Account
                    </button>
                </form>           
            </div>
        </div>
    </div>
@endsection
