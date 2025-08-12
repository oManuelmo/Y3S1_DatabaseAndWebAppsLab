
<!DOCTYPE html>
<html lang="en-GB">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }} - Admin Page</title>

        
        <link href="{{ url('css/app.css') }}" rel="stylesheet">
        <link href="{{ url('css/admin.css') }}" rel="stylesheet">

        <link href="{{ url('css/general.css') }}" rel="stylesheet">
        
        <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
        <script type="text/javascript"></script>
        <script type="text/javascript" src="{{ asset('js/admin.js') }}" defer></script>
    </head>
    <body>
        <main>
            @php
                use App\Models\Report;
                $reportCount = Report::count();
            @endphp

            <section class="admin-header">
                <a href="{{ url('/main') }}" title="main-page">
                    <span>Main page</span>
                </a>
                <span class="material-symbols-rounded" data-tooltip="Access the main page to browse the website as an user." >info</span>
            </section>
            <header>
                <h1><a href="{{ url('/admin') }}">Administration</a></h1>
                @if (Auth::check())
                    <a class="logout" href="{{ url('/logout') }}"> <span class="material-symbols-rounded">logout</span> </a>
                @endif
            </header>
            <section id="admin-pages">
                <a href="{{ route('admin.dashboard') }}"><button class="admin-page-button">Dashboard</button></a>
                <a href="{{ route('admin.users') }}"><button class="admin-page-button">Users</button></a>
                <a href="{{ route('admin.items') }}"><button class="admin-page-button">Auctions</button></a>
                <a href="{{ route('admin.items.pending') }}"><button class="admin-page-button">Pending Auctions</button></a>
                <a href="{{ route('admin.categories', ['type' => 'styles']) }}"><button class="admin-page-button">Manage Styles</button></a>
                <a href="{{ route('admin.categories', ['type' => 'themes']) }}"><button class="admin-page-button">Manage Themes</button></a>
                <a href="{{ route('admin.categories', ['type' => 'techniques']) }}"><button class="admin-page-button">Manage Techniques</button></a>
                <a href="{{ route('admin.reports') }}">
                    <button class="admin-page-button">
                        @if($reportCount > 0)
                            <span class="badge">{{ $reportCount }}</span>
                        @endif
                        Reports
                    </button>
                </a>
                <a href="{{ route('admin.chats') }}"><button class="admin-page-button">User Support</button></a>
            </section>
            <div class="sep" style="border-top: 2px solid gray; width: 100%;"></div>
            <section style="padding: 20px;" id="content">
                @yield('content')
            </section>
        </main>
    </body>
</html>