@extends('layouts.app')

@section('content')
<script src="{{ asset('js/edit_item.js') }}"></script>
<link href="{{ url('css/edit_item.css') }}" rel="stylesheet">
<link href="{{ url('css/general.css') }}" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.css" rel="stylesheet">
<script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.js"></script>

    <div style="margin-bottom: 5em;" class="container edit-item">
        <h1 class="mb-4">Edit Auction</h1>

        <form method="POST" action="{{ route('item.update', $item->itemid) }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <label for="name">Name</label>
            <input id="name" type="text" maxlength="100" name="name" value="{{ old('name', $item->name) }}" required>
            @if ($errors->has('name'))
                <span class="error">
                <span class="material-symbols-rounded">
                warning
                </span>{{ $errors->first('name') }}</span>
            @endif
            <br>
            <label for="initialprice">Initial Price</label>
            <input id="initialprice" type="number" name="initialprice" min="1" max="500000" value="{{ old('initialprice', $item->initialprice) }}" required autofocus>
            @if ($errors->has('initialprice'))
                <span class="error">
                <span class="material-symbols-rounded">
                warning
                </span>{{ $errors->first('initialprice') }}</span>
            @endif
            <br>
            <label for="artist">Artist</label>
            <select id="artist" name="artistid" onchange="toggleNewArtistInput()">
                <option value="{{old('artistid', $item->artistid)}}"> {{old('artistid', $item->artist->name)}}</option>
                @foreach($famousArtists as $artist)
                    <option value="{{ $artist->artistid }}" {{ old('artistid') == $artist->artistid ? 'selected' : '' }}>{{ $artist->name }}</option>
                @endforeach
            </select>

            <label for="new_artist_name">OR</label>
            <input type="text" id="new_artist_name" maxlength="100" name="new_artist_name" placeholder="Enter a new artist's name" value="{{ old('new_artist_name') }}" style="display: none;">

            @if ($errors->has('artistid'))
                <span class="error">
                <span class="material-symbols-rounded">
                warning
                </span>{{ $errors->first('artistid') }}</span>
            @endif
            @if ($errors->has('new_artist_name'))
            <span class="error">
            <span class="material-symbols-rounded">
                warning
                </span>{{ $errors->first('new_artist_name') }}</span>
            @endif
            <br>
            <label for="width">Width (in cm)</label>
            <input id="width" type="number" name="width" min="1" max="9999999.99" value="{{ old('width', $item->width) }}" required autofocus>
            <label for="height">Height (in cm)</label>
            <input id="height" type="number" name="height" min="1" max="9999999.99" value="{{ old('height', $item->height) }}" required autofocus>
            @if ($errors->has('height'))
                <span class="error">
                <span class="material-symbols-rounded">
                warning
                </span>{{ $errors->first('height') }}</span>
            @endif
            <br>
            <label for="style">Style</label>
            <select id="style" name="style" required>
                @foreach ($styles as $style)
                    <option value="{{ $style->enumlabel }}" {{ old('style', $item->style) == $style->enumlabel ? 'selected' : '' }}>
                        {{ $style->enumlabel }}
                    </option>
                @endforeach
            </select>
            @if ($errors->has('style'))
                <span class="error">
                <span class="material-symbols-rounded">
                warning
                </span>{{ $errors->first('style') }}</span>
            @endif
            <br>
            <label for="theme">Theme</label>
            <select id="theme" name="theme" required>
                @foreach ($themes as $theme)
                    <option value="{{ $theme->enumlabel }}" {{ old('theme', $item->theme) == $theme->enumlabel ? 'selected' : '' }}>
                        {{ $theme->enumlabel }}
                    </option>
                @endforeach
            </select>
            @if ($errors->has('theme'))
                <span class="error">
                <span class="material-symbols-rounded">
                warning
                </span>{{ $errors->first('theme') }}</span>
            @endif
            <br>
            <label for="technique">Technique</label>
            <select id="technique" name="technique" required>
                @foreach ($techniques as $technique)
                    <option value="{{ $technique->enumlabel }}" {{ old('technique', $item->technique) == $technique->enumlabel ? 'selected' : '' }}>
                        {{ $technique->enumlabel }}
                    </option>
                @endforeach
            </select>
            @if ($errors->has('technique'))
                <span class="error">
                <span class="material-symbols-rounded">
                warning
                </span>{{ $errors->first('technique') }}</span>
            @endif
            <br>
            <label for="description">Description</label>
            <textarea id="description" name="description" required>{{ old('description', $item->description) }}</textarea>
            @if ($errors->has('description'))
                <span class="error">
                <span class="material-symbols-rounded">
                warning
                </span>{{ $errors->first('description') }}</span>
            @endif
            <br>
            <label for="images">Images (up to 5)</label>
            
            <div id="image-upload-container">
                @php
                    $images = DB::table('product_images')
                        ->join('images', 'product_images.imageid', '=', 'images.imageid')
                        ->where('product_images.itemid', $item->itemid)
                        ->pluck('images.imageurl');
                @endphp
                <script>
                    const imageCount = {{ count($images) }};
                </script>

                @for ($i = 0; $i < 5; $i++)
                    <div class="image-upload" id="image-upload-{{ $i + 1 }}">
                        @if (isset($images[$i]) && !empty($images[$i]))
                            <div class="existing-image" id="existing-image-{{ $i + 1 }}">
                                <img src="{{ Storage::url($images[$i]) }}" alt="Existing Image" class="preview-image">
                                <input type="checkbox" name="delete_images[]" value="{{ $images[$i] }}" id="delete-image-checkbox-{{ $i + 1 }}" style="display: none;">
                                <span id="remove-button-{{ $i + 1 }}" class="delete-icon" onclick="handleExistingImageRemove({{ $i + 1 }}, '{{ $images[$i] }}')">delete</span>
                            </div>
                        @else
                            <input id="image{{ $i + 1 }}" type="file" name="images[]" accept="image/*" class="file-input item-image-input" data-index="{{ $i + 1 }}">
                            <div id="preview{{ $i + 1 }}" class="image-preview">
                                <label id="icon{{ $i + 1 }}" for="image{{ $i + 1 }}" class="upload-icon">add_photo_alternate</label>
                            </div>
                        @endif
                    </div>
                @endfor
            </div>

            @if ($errors->has('images'))
                <span class="error">
                <span class="material-symbols-rounded">
                warning
                </span>{{ $errors->first('images') }}</span>
            @endif
            <br>
            @foreach ($errors->get('images.*') as $error)
                <span class="error">
                <span class="material-symbols-rounded">
                warning
                </span>{{ $error[0] }}</span>
            @endforeach
            <br>
            <button type="submit" class="btn btn-success">Save Changes</button>
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
