<link href="{{ asset('css/chat.css') }}" rel="stylesheet">
<script src="https://js.pusher.com/7.0/pusher.min.js"></script>
<script src="{{ asset('js/chat.js') }}"></script>
<script>
    window.chatData = {
        chatid: "{{ $chat->chatid }}",
        authUserId: "{{ Auth::id() }}",
        isChatClosed: "{{ $chat->statustype == 'closed' ? 'true' : 'false' }}",
    };
</script>
<div class="chat">
    <h1>Support</h1>
    <div id="chatBox">
        <div id="chatMessages">
            @foreach ($chat->messages as $i => $message)
                @php
                    $isSent = $message->senderid == Auth::id();
                    $isSystemMessage = $message->senderid == 0;
                @endphp
                <div class="message {{ $isSent ? 'sent' : 'received' }} {{ $isSystemMessage ? 'system' : '' }}" data-index="{{ $i + 1 }}" data-chatid="{{ $chat->chatid }}">
                    <strong>{{ $isSystemMessage ? 'System' : $message->sender->firstname }}</strong>:
                    <p>{!! $message->messagetext !!}</p>
                    <small style="color: black !important;">{{ \Carbon\Carbon::parse($message->createdat)->format('Y-m-d H:i:s') }}</small>
                </div>
            @endforeach
        </div>

        <div id="chat-buttons">
            <form id="messageForm" action="{{ route('chat.send', $chat->chatid) }}" method="POST">
                @csrf
                <input type="text" name="message" id="messageInput" placeholder="Type your message...">
                <button type="submit">Send</button>
            </form>
            <form id="closeChatForm" action="{{ route('chat.close', $chat->chatid) }}" method="POST" style="display: inline-block;">
                @csrf
                <button type="submit">Close Chat</button>
            </form>
        </div>
    </div>
</div>
