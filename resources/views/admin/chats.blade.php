@extends('layouts.admin') 

@section('content')
    <div class="container">
        <h1>Admin - Active Chats</h1>

        <ul>
            @if (!$chats->isEmpty())
                @foreach ($chats as $chat)
                    <li>
                        <a href="{{ route('admin.chat.view', $chat->chatid) }}">
                            Chat with {{ $chat->user->firstname }} {{ $chat->user->lastname }}
                        </a>
                        <p>Status: {{ $chat->statustype }}</p>
                        <p>Last message at: {{ $chat->updatedat->format('Y-m-d H:i:s') }}</p>
                    </li>
                @endforeach
            @else
                <p>No Active Chats</p>
            @endif
        </ul>
    </div>
@endsection
