@extends('layouts.app')

@section('content')
<script src="{{ asset('js/create_item.js') }}"></script>
<link href="{{ url('css/create_item.css') }}" rel="stylesheet">
<link href="{{ url('css/general.css') }}" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.css" rel="stylesheet">
<script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.js"></script>


  <div class="container create-item">
    <h1 class="mb-4">Create Auction</h1>
    <form method="POST" action="{{ route('item.create') }}" enctype="multipart/form-data">
        @csrf
        <input type="hidden" name="ownerid" value="{{ Auth::id() }}">
        <label for="name" style="font-size: 20px; margin-top: 20px;">Name</label>
        <input id="name" maxlength="100" type="text" name="name" value="{{ old('name') }}" required autofocus>
        @if ($errors->has('name'))
          <span class="error">
              <span class="material-symbols-rounded">
                warning
                </span>
              {{ $errors->first('name') }}
          </span>
        @endif
        <br>
        <label for="initialprice" style="font-size: 20px;">Initial Price</label>
        <input id="initialprice" type="number" step="0.01" min="0" name="initialprice" value="{{ old('initialprice') }}">
        @if ($errors->has('initialprice'))
          <span class="error">
          <span class="material-symbols-rounded">
                warning
                </span>
              {{ $errors->first('initialprice') }}
          </span>
        @endif
        <br>
        <label for="artist" style="font-size: 20px;">Artist</label>
        <select id="artist" name="artist" onchange="toggleNewArtistInput()" required>
            <option value="">Select a famous artist</option>
            @foreach($famousArtists as $artist)
                <option value="{{ $artist->artistid }}" {{ old('artistid') == $artist->artistid ? 'selected' : '' }}>{{ $artist->name }}</option>
            @endforeach
        </select>

        
        <label for="new_artist_name" style="font-size: 20px;">OR</label>
        <input type="text" id="new_artist_name" maxlength="100" name="new_artist_name" placeholder="Enter a new artist's name" >

        @if ($errors->has('artistid'))
          <span class="error">
          <span class="material-symbols-rounded">
                warning
                </span>
          {{ $errors->first('artistid') }}</span>
        @endif
        @if ($errors->has('new_artist_name'))
          <span class="error">
          <span class="material-symbols-rounded">
                warning
                </span>
          {{ $errors->first('new_artist_name') }}</span>
        @endif
        <br>
        <label for="width" style="font-size: 20px;">Width (in cm)</label>
        <input id="width" type="number" step="0.1" min="0" name="width" value="{{ old('width') }}" required>
        @if ($errors->has('width'))
          <span class="error">
          <span class="material-symbols-rounded">
                warning
                </span>
              {{ $errors->first('width') }}
          </span>
        @endif
        <br>
        <label for="height" style="font-size: 20px;">Height (in cm)</label>
        <input id="height" type="number" step="0.1" min="0" name="height" value="{{ old('height') }}" required>
        @if ($errors->has('height'))
          <span class="error">
          <span class="material-symbols-rounded">
                warning
                </span>
              {{ $errors->first('height') }}
          </span>
        @endif
        <br>
        <label for="style" style="font-size: 20px;">Style</label>
        <select id="style" name="style" required>
            <option value="">Select a style</option>
            @foreach($styles as $style)
              <option value="{{ $style->enumlabel }}" {{ old('style') == $style->enumlabel ? 'selected' : '' }}>{{ $style->enumlabel }}</option>
            @endforeach
        </select>
        @if ($errors->has('style'))
          <span class="error">
          <span class="material-symbols-rounded">
                warning
                </span>
              {{ $errors->first('style') }}
          </span>
        @endif
        <br>
        <label for="theme" style="font-size: 20px;">Theme</label>
        <select id="theme" name="theme" required>
            <option value="">Select a theme</option>
            @foreach($themes as $theme)
              <option value="{{ $theme->enumlabel }}" {{ old('theme') == $theme->enumlabel ? 'selected' : '' }}>{{ $theme->enumlabel }}</option>
            @endforeach
        </select>
        @if ($errors->has('theme'))
          <span class="error">
          <span class="material-symbols-rounded">
                warning
                </span>
              {{ $errors->first('theme') }}
          </span>
        @endif
        <br>
        <label for="technique" style="font-size: 20px;">Technique</label>
        <select id="technique" name="technique" required>
            <option value="">Select a technique</option>
            @foreach($techniques as $technique)
              <option value="{{ $technique->enumlabel }}" {{ old('technique') == $technique->enumlabel ? 'selected' : '' }}>{{ $technique->enumlabel }}</option>
            @endforeach
        </select>
        @if ($errors->has('technique'))
          <span class="error">
          <span class="material-symbols-rounded">
                warning
                </span>
              {{ $errors->first('technique') }}
          </span>
        @endif
        <br>
        <label for="description" style="font-size: 20px;">Description</label>
        <textarea id="description" name="description" required>{{ old('description') }}</textarea>
        @if ($errors->has('description'))
          <span class="error">
          <span class="material-symbols-rounded">
                warning
                </span>
              {{ $errors->first('description') }}
          </span>
        @endif
        <br>
        <br>
        <label for="images">Images (up to 5)</label>
        @php
            $images = $images ?? [];
        @endphp
        <div id="image-upload-container">
            @for ($i = 0; $i < 5; $i++)
                <div class="image-upload" id="image-upload-{{ $i + 1 }}">
                    <input id="image{{ $i + 1 }}" type="file" name="images[]" accept="image/*" class="file-input item-image-input" data-index="{{ $i + 1 }}">
                    <div id="preview{{ $i + 1 }}" class="image-preview">
                        <label id="icon{{ $i + 1 }}" for="image{{ $i + 1 }}" class="upload-icon">add_photo_alternate</label>
                    </div>
                </div>
            @endfor
        </div>

        @if ($errors->has('images'))
          <span class="error">
          <span class="material-symbols-rounded">
                warning
                </span>
              {{ $errors->first('images') }}
          </span>
        @endif
        <br>
        @foreach ($errors->get('images.*') as $error)
          <span class="error">
          <span class="material-symbols-rounded">
                warning
                </span>
          {{ $error[0] }}</span>
        @endforeach
        <br>
        <label for="duration_days" style="font-size: 20px; margin-top: 20px;">Auction Duration</label>
        <div class="duration-inputs">
          <div class="duration-input">
              <span>Days</span>
              <input id="duration_days" type="number" name="duration_days" min="0" value="{{ old('duration_days') }}" required>
          </div>
          <div class="duration-input">
               <span>Hours</span>
              <input id="duration_hours" type="number" name="duration_hours" min="0" max="23" value="{{ old('duration_hours') }}" required>
          </div>
          <div class="duration-input">
              <span>Minutes</span>
              <input id="duration_minutes" type="number" name="duration_minutes" min="0" max="59" value="{{ old('duration_minutes') }}" required>
          </div>
        </div>
        <button type="submit" style="font-size: 16px !important;">Register Item</button>
        <a class="button button-outline" href="{{ route('main') }}">Cancel</a>
    </form>
</div>

<div id="imageCropModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center">
    <div class="bg-white p-6 rounded-lg max-w-md w-full shadow-lg">
        <h2 class="text-xl font-semibold mb-4 text-center">Crop Your Image</h2>
        <div class="cropper-container mb-4">
            <img id="cropImage" src="" alt="Crop Image">
        </div>
        <div class="flex justify-center space-x-4">
            <button id="cropButton" class="button-standard">Crop and Upload</button>
            <button id="closeCropModal" class="button-standard text-gray-700">Cancel</button>
        </div>
    </div>
</div>

@endsection