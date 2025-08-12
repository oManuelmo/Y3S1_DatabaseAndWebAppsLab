@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="mb-4">About Us</h1>

    <div class="card mb-4">
        <div class="card-header bg-primary text-white">
            <h2>Our Team</h2>
        </div>
        <div class="card-body">
            <ul class="list-unstyled">
                @foreach ($team as $member)
                    <li class="mb-3">
                        <strong>{{ $member['name'] }}</strong> ({{ $member['id'] }})
                    </li>
                @endforeach
            </ul>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header bg-secondary text-white">
            <h2>Group Information</h2>
        </div>
        <div class="card-body">
            <p><strong>Group:</strong> {{ $group }}</p>
            <p><strong>University:</strong> {{ $university }}</p>
            <p>{{ $project_note }}</p>
        </div>
    </div>

    <div class="mt-4">
        <p>We hope you enjoy exploring and using our auction platform!</p>
    </div>
</div>
@endsection
