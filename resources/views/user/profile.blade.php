@extends('layouts.app')

@section('content')
    <link href="{{ asset('css/profile.css') }}" rel="stylesheet">
    <script src="{{ asset('js/profile.js') }}" defer></script> 
    <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.js" defer></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.css" rel="stylesheet">

    <div class="container mx-auto p-4" style="margin-bottom: 5em;">
        <h1 class="text-3xl font-semibold mb-6">Profile</h1>
        <div class="bg-white shadow-lg rounded-lg mb-6 p-6">
            <div class="flex justify-between items-center border-b pb-4 mb-4">
                <h3 class="text-xl font-semibold">User Information</h3>
            </div>

            <div class="flex items-center">
                @if (Auth()->id() == $user->userid)
                    <meta name="csrf-token" content="{{ csrf_token() }}">
                    <form id="updateProfilePictureForm" action="{{ route('profile.update.picture', $user->userid) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('POST')
                        <input type="file" name="profile_picture" id="profile_picture_input" class="hidden">
                        <label for="profile_picture_input" class="cursor-pointer">
                            <div class="profile-image-wrapper">
                                <img src="{{ $user->image ? Storage::url($user->image->imageurl) : asset('profile.png') }}" alt="Profile Picture" class="w-24 h-24 rounded-full object-cover" id="profileImagePreview">
                                <span class="edit-icon"><span class="material-symbols-rounded">edit</span></span>
                            </div>
                        </label>
                        <div id="successMessage" class="text-green-500" style="display: none;"></div>
                        <div id="errorMessage" class="text-red-500" style="display: none;"></div>
                    </form>

                    <div id="imageCropModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center" style="display: none;">
                        <div class="bg-white p-6 rounded-lg">
                            <h2 class="text-xl font-semibold mb-4">Crop Your Profile Picture</h2>
                            <div class="cropper-container mb-4">
                                <img id="cropImage" src="" alt="Crop Image" class="max-w-full">
                            </div>
                            <div class="flex justify-end space-x-4">
                                <button id="cropButton" class="button-standard">Crop and Upload</button>
                                <button id="closeCropModal" class="button-standard" style="color: black;">Cancel</button> 
                            </div>
                        </div>
                    </div>
                @else
                    <img src="{{ $user->image ? Storage::url($user->image->imageurl) : asset('profile.png') }}" alt="Profile Picture" class="w-24 h-24 rounded-full object-cover">
                @endif
            </div>

            <div class="Rate">
                @if (Auth()->id() != $user->userid && Auth::check())
                    @if(!Auth::user()->isadmin)
                        <form action="{{ route('profile.rate', $user->userid) }}" method="POST">
                            @csrf
                            <div class="rating">
                                <label for="rate" class="block mt-4">Rate this profile:</label>
                                <div class="stars">
                                    <input type="hidden" name="rate" id="rate" value="{{ old('rate', $previousRating ?? 0) }}">
                                    @for ($i = 1; $i <= 5; $i++)
                                        <span id="star" class="star @if($previousRating >= $i) filled @endif" data-value="{{ $i }}">&#9734;</span>
                                    @endfor
                                    <button id="rate-btn" type="submit" class="ml-4 button-standard">Submit Rating</button>
                                </div>
                            </div>
                        </form>
                    @endif
                @endif

                @if($user->averageRating())
                    <div class="average-rating">
                        <p><strong>User Rating:</strong>
                            <span class="stars-average">
                                ({{ number_format($user->averageRating(), 2) }} / 5)
                                @for ($i = 1; $i <= 5; $i++)
                                    @if ($i <= floor($user->averageRating()))
                                        <span class="star-filled">&#9733;</span>
                                    @else
                                        <span class="star">&#9734;</span>
                                    @endif
                                @endfor
                            </span>
                        </p>
                    </div>
                @else
                    <p>No ratings yet.</p>
                @endif
            </div>

            <div class="space-y-2">
                @if (Auth()->id() == $user->userid)
                    <p><strong class="font-semibold">Name:</strong> {{ $user->firstname }} {{ $user->lastname }}</p>
                    <p><strong class="font-semibold">Email:</strong> {{ $user->email }}</p>
                    <p><strong class="font-semibold">Phone:</strong> {{ $user->phone }}</p>
                    <p><strong class="font-semibold">Address:</strong> {{ $user->address }}</p>
                    <p><strong class="font-semibold">Balance:</strong> ${{ number_format($user->balance - $user->bidbalance,2) }}</p>
                    <p><strong class="font-semibold">Bid Balance:</strong> ${{ number_format($user->bidbalance,2) }}</p>
                @else
                    <p><strong class="font-semibold">Name:</strong> {{ $user->firstname }} {{ $user->lastname }}</p>
                    <p><strong class="font-semibold">Email:</strong> {{ $user->email }}</p>
                @endif
            </div>

            <div class="flex justify-between items-center space-x-4">
                @if (Auth::id() == $user->userid)
                    <a href="{{ route('profile.edit.options', $user->userid) }}" class="button-standard">Profile Settings</a>
                @endif
                @if (Auth()->id() == $user->userid && !$user->isadmin)
                    <a href="{{ route('transactions.show', $user->userid) }}" class="button-standard">Transactions</a>
                    <a href="{{ route('profile.user.items', ['id' => $user->userid]) }}" 
                    class="button-standard">My Auctions</a>
                @endif
                @if (Auth()->id() != $user->userid && !$user->isadmin)
                    <a href="{{ route('profile.user.items', ['id' => $user->userid]) }}" 
                    class="button-standard">User Auctions</a>
                @endif
                @if (!$user->isadmin)
                    <a href="{{ route('profile.items.bought', ['id' => $user->userid]) }}" class="button-standard">Bought Auctions</a>
                    <a href="{{ route('followed-items', ['id' => $user->userid]) }}" class="button-standard">Followed Auctions</a>
                @endif
            </div>
        </div>
    </div>

    <div id="updatePictureRoute" data-route="{{ route('profile.update.picture', $user->userid) }}" style="display: none;"></div>
@endsection