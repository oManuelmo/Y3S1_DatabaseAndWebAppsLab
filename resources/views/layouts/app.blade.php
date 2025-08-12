<!DOCTYPE html>
<html lang="en-GB">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link href="{{ asset('css/general.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />

    <script src="https://js.pusher.com/7.0/pusher.min.js" defer></script>
    <script type="text/javascript">
        // Fix for Firefox autofocus CSS bug
        // See: http://stackoverflow.com/questions/18943276/html-5-autofocus-messes-up-css-loading/18945951#18945951
    </script>
    <script type="text/javascript" src="{{ asset('js/app.js') }}" defer></script>
    <script type="text/javascript" src="{{ asset('js/user-items.js') }}" defer></script>
    <script src="{{ asset('js/filter.js') }}"></script>
    <script src="{{ asset('js/user-popup.js') }}"></script>

</head>
<body>
    <main>
        @php
            use App\Models\Item;
            $styles = Item::distinct()->pluck('style');
            $techniques = Item::distinct()->pluck('technique');
            $themes = Item::distinct()->pluck('theme');
        @endphp

        @if (Auth::check() && Auth::user()->isadmin)
        <section class="admin-header">
            <a href="{{ url('/admin') }}" title="Admin-page">
                <span>Admin page</span>
            </a>
            <span class="material-symbols-rounded" data-tooltip="Access the admin page to manage all platform content and settings.">info</span>
        </section>
        @endif
        <header>
            
            <div class="header-left">
                <h1><a href="{{ url('/main') }}">Bidtano</a></h1>
            </div>

            @if(request()->is('main') || request()->is('items') || request()->is('search'))
                <div class="header-center">
                    <form action="{{ route('search.index') }}" method="GET">
                        <label for="query">
                            <span class="material-symbols-rounded" data-tooltip="Use this to search for items by writing their name or part of it">info</span>
                        </label>
                        <input type="text" name="query" id="query" placeholder="Search items..." value="{{ request('query') }}">

                        <button id="search-btn" type="submit" class="button">
                            <span class="material-symbols-rounded">search</span>
                        </button>

                        <label id="filters-btn" class="button">
                            <span class="material-symbols-rounded">tune</span>
                        </label>

                        <div id="filters-dropdown" style="display:none; position:absolute; background-color:white; border: 1px solid #ccc; padding: 10px; box-shadow: 0 4px 8px rgba(0,0,0,0.1);">
                            <label for="style">
                                Style:
                                <span class="material-symbols-rounded" data-tooltip="Choose the artistic style of the item.">info</span>
                            </label>
                            <select name="style" id="style">
                                <option value="">Select Style</option>
                                @foreach($styles as $style)
                                    <option value="{{ $style }}" {{ request('style') == $style ? 'selected' : '' }}>{{ $style }}</option>
                                @endforeach
                            </select>

                            <label for="technique">
                                Technique:
                                <span class="material-symbols-rounded" data-tooltip="Choose the technique used in crafting the item.">info</span>
                            </label>
                            <select name="technique" id="technique">
                                <option value="">Select Technique</option>
                                @foreach($techniques as $technique)
                                    <option value="{{ $technique }}" {{ request('technique') == $technique ? 'selected' : '' }}>{{ $technique }}</option>
                                @endforeach
                            </select>

                            <label for="theme">
                                Theme:
                                <span class="material-symbols-rounded" data-tooltip="Pick the theme related to the item.">info</span>
                            </label>
                            <select name="theme" id="theme">
                                <option value="">Select Theme</option>
                                @foreach($themes as $theme)
                                    <option value="{{ $theme }}" {{ request('theme') == $theme ? 'selected' : '' }}>{{ $theme }}</option>
                                @endforeach
                            </select>

                            <label for="max-width">
                                Max Width (cm):
                                <span class="material-symbols-rounded" data-tooltip="Specify the maximum width of the item you're searching for.">info</span>
                            </label>
                            <input type="number" name="max-width" id="max-width" value="{{ request('max-width') }}" min="1">

                            <label for="max-height">
                                Max Height (cm):
                                <span class="material-symbols-rounded" data-tooltip="Specify the maximum height of the item you're searching for.">info</span>
                            </label>
                            <input type="number" name="max-height" id="max-height" value="{{ request('max-height') }}" min="1">
                        </div>

                    </form>
                </div>
            @endif

            <div class="header-right">
                @if (Auth::check())
                    @if (!Auth::user()->isadmin)
                        <div class="balance-container">
                            <a href="{{ route('withdraw.form', Auth::id()) }}" 
                            id="buttonMinus"><span class="material-symbols-rounded">remove</span></a>
                            <div class="balance">${{ number_format(Auth::user()->balance - Auth::user()->bidbalance,2) }}</div>
                            <a href="{{ route('deposit.form', Auth::id()) }}" 
                            id="buttonPlus"><span class="material-symbols-rounded">add</span></a>
                        </div>
                        <a href="javascript:void(0);" id="toggleSidebar" data-user-id="{{ Auth::id() }}" title="Notifications">
                            <span class="material-symbols-rounded">notifications</span>
                        </a>
                    @endif
                    <button id="show-popup-btn" class="btn-popup">
                        <img src="{{ Auth::user()->image ? Storage::url(Auth::user()->image->imageurl) : asset('profile.png') }}" alt="Profile Picture" id="profileImagePreview">
                    </button>
                @else
                    <a href="{{ url('/login') }}" title="Login">
                        <span class="material-symbols-rounded">login</span>
                    </a>
                    <a href="{{ url('/register') }}" title="Register">
                        <span class="material-symbols-rounded">person_add</span>
                    </a>
                @endif
            </div>
        </header>
        <div id="notificationsSidebar" class="sidebar">
            <div class="sidebar-header">
                <h2>My Notifications</h2>
                <button id="closeSidebar" class="close-btn">&times;</button>
            </div>
            <div class="sidebar-content">
                <div id="notificationsContent"></div>
            </div>
        </div>

        @if (Auth::check())
            <div class="user-popup">
                <div class="user-popup-header">
                    <h2>{{ Auth::user()->firstname }}</h2>
                    <button id="close-popup" class="close-btn">&times;</button>
                </div>
                <div class="user-popup-content">
                    <a href="{{ route('profile.show', ['userid' => auth()->id()]) }}" title="Profile">
                        <p>Profile<span class="material-symbols-rounded">person</span></p>
                    </a>
                    <a href="{{ url('/logout') }}" title="Logout">
                        <p>Logout<span class="material-symbols-rounded">logout</span></p>
                    </a>
                    @if (!Auth::user()->isadmin)
                        <a href="{{ route('item.create.form') }}" title="Create">
                            <p>Create Auction<span class="material-symbols-rounded">wall_art</span></p>
                        </a>
                        <a href="{{ route('withdraw.form', Auth::id()) }}" title="Withdraw">
                            <p>Withdraw<span class="material-symbols-rounded">shift_lock</span></p>
                        </a>
                        <a href="{{ route('deposit.form', Auth::id()) }}" title="Deposit">
                            <p>Deposit<span class="material-symbols-rounded">attach_money</span></p>
                        </a>
                    @endif
                </div>
            </div>
        @endif

        <div id="notification-container" class="notification-container"></div>
        <section id="content">
            @yield('content')
        </section>
    </main>

    <footer class="bg-light text-center py-4">
        <div class="container">
            <p class="mb-1">Bidtano © {{ date('Y') }}</p>
            <nav>
                <a href="{{ route('about') }}" class="text-decoration-none me-3">About Us</a>
                <a href="{{ route('features') }}" class="text-decoration-none me-3">Features</a>
                <a href="{{ route('contacts') }}" class="text-decoration-none">Contact</a>
                @if ( Auth::check())
                    @if (!Auth::user()->isadmin)
                        <form action="{{ route('chat.create') }}" method="POST">
                            @csrf
                            <button style="font-size: 14px !important; font-family: Open Sans, sans-serif; padding-bottom: 0 !important;" type="submit" class="button-support" >Support</button>
                        </form>
                    @endif
                @endif
            </nav>
        </div>
    </footer>
</body>
</html>
