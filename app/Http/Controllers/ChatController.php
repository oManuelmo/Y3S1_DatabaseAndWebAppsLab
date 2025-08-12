<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Chat;
use App\Models\Message;
use App\Events\MessageSent;
use App\Events\ChatClosed;
use Carbon\Carbon;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Gate;

class ChatController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function createChat(Request $request)
    {
        $userId = $request->user()->userid;
        $this->authorize('createChat', User::class);
        $existingChat = Chat::where('userid', $userId)->where('statustype', 'active')->first();
        if ($existingChat) {
            return redirect()->route('chat.view', $existingChat->chatid);
        }

        $admin = User::where('isadmin', true)->first(); 

        if (!$admin) {
            return redirect()->route('chat.index')->with('error', 'No admin available');
        }

        $adminId = $admin->userid;

        $chat = Chat::create([
            'userid' => $userId,
            'statustype' => 'active',
            'createdat' => Carbon::now(),
            'updatedat' => Carbon::now(),
            'adminid' => $adminId,
        ]);

        $defaultMessage = Message::create([
            'chatid' => $chat->chatid,
            'senderid' => 0, 
            'messagetext' => "Welcome to our support chat, how can we assist you?",
            'createdat' => Carbon::now(),
        ]);

        broadcast(new MessageSent($defaultMessage));
        Log::info('MessageSent broadcasted:', $defaultMessage->toArray());

        return redirect()->route('chat.view', $chat->chatid);
    }

    public function getChatsForAdmin(Request $request)
    {
        if (Gate::denies('admin-only')) {
            return back()->withErrors('You cannot see this page.');
        }
        $chats = Chat::with('user')
            ->where('statustype', 'active')
            ->orderBy('updatedat', 'desc') 
            ->get();

        return view('admin.chats', compact('chats'));
    }

    public function sendMessage(Request $request, $chatId)
    {
        $chat = Chat::find($chatId);
        $this->authorize('sendMessage', $chat);
        $messageText = $request->input('message');

        if (empty($messageText)) {
            return response()->json(['error' => 'Message cannot be empty'], 400);
        }

        $message = new Message();
        $message->chatid = $chatId;
        $message->senderid = auth()->id();  
        $message->messagetext = $messageText;
        $message->save();

        if ($chat && !($chat->statustype=='closed')) {
            $chat->updatedat = Carbon::now(); 
            $chat->save();  
        }
        else{
            return response()->json(['error' => 'Message cannot be empty']);
        }

        broadcast(new MessageSent($message));

        return response()->json(['status' => 'success', 'message' => 'Message sent successfully']);
    }
    public function getMessages(Request $request)
    {
        $chatId = $request->chatid;
        $chat = Chat::find($chatId);
        $this->authorize('seeMessages', $chat);
        $messages = Message::where('chatid', $chatId)
            ->orderBy('createdat', 'asc')
            ->get();

        return response()->json($messages);
    }

    public function viewChat($chatId)
    {
        $chat = Chat::find($chatId);
        $this->authorize('seeMessages', $chat);
        if (!is_numeric($chatId) || (int)$chatId != $chatId) {
            return abort(400, 'Invalid chat ID');
        }
    
        $chatId = (int)$chatId;
    
        $chat = Chat::with('messages.sender')->findOrFail($chatId);
    
        return view('user.chat', compact('chat'));
    }
    

    public function viewChatForAdmin($chatId)
    {
        if (Gate::denies('admin-only')) {
            return back()->withErrors('You cannot see this page.');
        }
        $chat = Chat::with(['messages', 'user', 'admin'])->findOrFail($chatId);
        return view('admin.chat', compact('chat'));
    }  
    public function closeChat($chatid)
    {
        try {
            $chat = Chat::findOrFail($chatid);
            $this->authorize('closeChat', $chat);
            $closedBy = Auth::user()->isadmin ? 'admin' : 'user';

            $closeMessage = Message::create([
                'chatid' => $chat->chatid,
                'senderid' => 0, 
                'messagetext' => "The chat has been closed by $closedBy.",
                'createdat' => now(),
            ]);

            broadcast(new MessageSent($closeMessage));

            $chat->statustype = 'closed';
            $chat->save();

            Log::info('Broadcasting ChatClosed event for chat ID: ' . $chat->chatid);
            event(new ChatClosed($chat));

            return response()->json(['message' => 'Chat closed successfully.']);
        } catch (\Exception $e) {
            Log::error('Error closing chat: ' . $e->getMessage()); 
            return response()->json(['status' => 'error', 'message' => 'Failed to close chat'], 500);
        }
    }

    public function checkChatStatus($chatid)
    {
        $chat = Chat::find($chatid);

        if (!$chat) {
            return response()->json(['error' => 'Chat not found'], 404);
        }

        return response()->json(['status' => $chat->statustype]);
    }


}
