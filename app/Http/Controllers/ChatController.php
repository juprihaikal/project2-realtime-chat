<?php

namespace App\Http\Controllers;

use App\Events\MessageSent;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    public function index()
    {
        return $this->renderChat();
    }

    public function show(Conversation $conversation)
    {
        abort_unless(
            $conversation->users()->where('user_id', auth()->id())->exists(),
            403
        );

        return $this->renderChat($conversation);
    }

    public function storePrivate(Request $request)
    {
        $validated = $request->validate([
            'user_id' => ['required', 'exists:users,id', 'not_in:'.auth()->id()],
        ]);

        $conversation = Conversation::query()
            ->where('type', 'private')
            ->forUser(auth()->id())
            ->whereHas('users', fn ($q) => $q->where('user_id', $validated['user_id']))
            ->first();

        if (! $conversation) {
            $conversation = Conversation::create(['type' => 'private']);
            $conversation->users()->attach([auth()->id(), $validated['user_id']]);
            
            broadcast(new \App\Events\ConversationCreated($conversation));
        }

        return redirect()->route('chat.show', $conversation);
    }

    public function storeGroup(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'members' => ['required', 'array', 'min:1'],
            'members.*' => ['exists:users,id', 'not_in:'.auth()->id()],
        ]);

        $memberIds = collect($validated['members'])
            ->push(auth()->id())
            ->unique()
            ->values()
            ->all();

        $conversation = Conversation::create([
            'type' => 'group',
            'name' => $validated['name'],
        ]);

        $conversation->users()->attach($memberIds);

        broadcast(new \App\Events\ConversationCreated($conversation));

        return redirect()->route('chat.show', $conversation);
    }

    public function send(Request $request)
    {
        $validated = $request->validate([
            'conversation_id' => ['required', 'exists:conversations,id'],
            'message' => ['required', 'string', 'max:5000'],
        ]);

        $conversation = Conversation::query()
            ->forUser(auth()->id())
            ->findOrFail($validated['conversation_id']);

        $message = Message::create([
            'conversation_id' => $conversation->id,
            'user_id' => auth()->id(),
            'message' => $validated['message'],
        ]);

        $conversation->touch();

        broadcast(new MessageSent($message));

        return response()->json([
            'success' => true,
            'message' => $message->load('user'),
        ]);
    }

    private function renderChat(?Conversation $activeConversation = null)
    {
        if ($activeConversation) {
            $activeConversation->load([
                'users',
                'messages' => fn ($q) => $q->with('user')->oldest(),
            ]);
        }

        $conversations = Conversation::query()
            ->forUser(auth()->id())
            ->with(['users', 'messages' => fn ($q) => $q->latest()->limit(1)->with('user')])
            ->withCount('messages')
            ->orderByDesc('updated_at')
            ->get();

        $users = User::query()
            ->where('id', '!=', auth()->id())
            ->orderBy('name')
            ->get();

        return view('chat.index', compact(
            'conversations',
            'activeConversation',
            'users',
        ));
    }
}
