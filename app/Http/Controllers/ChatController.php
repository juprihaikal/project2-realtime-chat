<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Message;
use App\Models\Conversation;
use App\Events\MessageSent;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    public function index($id = null)
    {
        $users = User::where (
            'id', '!=', auth()->id()
        )->get();
        $messages = [];
        $receiver = null;

        if ($id) {
            $receiver = User::findOrFail($id);
            $conversation = Conversation::whereHas(
                'users', function ($q) use ($id) {
                    $q->where('user_id',  auth()->id());
        }
        )->whereHas('users', 
        function ($q) use ($id) {
            $q->where('user_id', $id);
    }

    )->first(); 
     if ($conversation) {
        $messages = Message::where('conversation_id', $conversation->id)
        ->with('user')
        ->get();
}
        }

        return view('chat.index', compact('users', 'messages', 'receiver'));
    }

    public function send(Request $request)
    {
        $receiverId = $request->receiver_id;
        $conversation = Conversation::where('type', 'private')
        ->whereHas('users', function ($q)
        use ($receiverId) {
            $q->where('user_id', auth()->id());
        })

        ->whereHas('users', function ($q)
        use ($receiverId) {
            $q->where('user_id', $receiverId);
        })

        ->first();
        if (!$conversation) {
            $conversation = Conversation::create(['type' => 'private']);

            $conversation->users()->attach([auth()->id(), $receiverId]);
        }
        $message = Message::create(['conversation_id' => $conversation->id,
        'user_id' => auth()->id(), 'message' => $request->message]);
        broadcast(new MessageSent($message, $receiverId))->toOthers();


        return response()->json(['seucces' => true, 'message' => $message]);
    }
  }


