@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="mb-4">Our Features</h1>
    
    <p>Welcome to our auction platform! Here are some of the features you can enjoy:</p>

    @foreach ($features as $section => $details)
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <h2>{{ $section }}</h2>
            </div>
            <div class="card-body">
                <ul class="list-unstyled">
                    @foreach ($details as $feature)
                        <li class="mb-2">
                            <i class="bi bi-check-circle text-success"></i> {{ $feature }}
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endforeach

    <div class="mt-4">
        <p>Ready to start your journey? <a href="{{ route('register') }}">Create an account</a> or <a href="{{ route('login') }}">log in</a> to begin!</p>
    </div>
</div>
@endsection
