@extends('layouts.admin')

@section('title', 'Users')

@section('content')
<h1>Users</h1>
<section id ="users-header">
    <form action="{{ route('admin.users.search') }}" method="GET" class="search-form">
        <input type="text" name="query" placeholder="Search by name" value="{{ request('query') }}">
        <button style="font-size: 20px !important;" type="submit">Search</button>
    </form>

    <form action="{{ route('admin.users.create') }}" method="GET">
        <button style="font-size: 20px !important; margin-bottom: 0.7em;" type="submit">Create user</button>
    </form>
</section>
@if (!empty($query))
<h2>Search for "{{ $query }}"</h2>
@endif
<section id="users">
    @if ($users->count() == 0) 
        <p> No users found. </p>
    @else
        @each('partials.admin-user', $users, 'user')
    @endif
</section>
<div>
    {{ $users->links() }}
</div>
@endsection
