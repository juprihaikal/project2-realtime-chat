<x-app-layout>

<div
    class="flex h-[calc(100vh-4rem)] bg-gray-900 text-white"
    data-chat-channel="chat.{{ auth()->id() }}"
    @if($activeConversation) data-active-conversation="{{ $activeConversation->id }}" @endif
>

    {{-- SIDEBAR --}}
    <aside class="w-80 border-r border-gray-700 flex flex-col shrink-0">

        <div class="p-4 border-b border-gray-700 flex items-center justify-between">
            <h2 class="text-lg font-bold">Chats</h2>
            <button
                type="button"
                id="open-group-modal"
                class="text-sm px-3 py-1 bg-blue-600 hover:bg-blue-500 rounded"
            >
                + Group
            </button>
        </div>

        <div class="flex-1 overflow-y-auto">
            @forelse ($conversations as $conversation)
                @php
                    $lastMessage = $conversation->messages->first();
                    $isActive = $activeConversation?->id === $conversation->id;
                @endphp
                <a
                    href="{{ route('chat.show', $conversation) }}"
                    class="block px-4 py-3 border-b border-gray-800 hover:bg-gray-800 {{ $isActive ? 'bg-gray-800' : '' }}"
                >
                    <div class="flex items-center gap-2">
                        <span class="text-xs px-1.5 py-0.5 rounded {{ $conversation->isGroup() ? 'bg-purple-600' : 'bg-green-600' }}">
                            {{ $conversation->isGroup() ? 'Group' : 'DM' }}
                        </span>
                        <span class="font-medium truncate">{{ $conversation->displayName() }}</span>
                    </div>
                    @if ($lastMessage)
                        <p class="text-sm text-gray-400 truncate mt-1">
                            {{ $lastMessage->user->name }}: {{ $lastMessage->message }}
                        </p>
                    @else
                        <p class="text-sm text-gray-500 mt-1">No messages yet</p>
                    @endif
                </a>
            @empty
                <p class="p-4 text-gray-500 text-sm">No conversations yet. Start a private chat or create a group.</p>
            @endforelse
        </div>

        <div class="border-t border-gray-700 p-4">
            <h3 class="text-sm font-semibold text-gray-400 mb-2">Start private chat</h3>
            <div class="max-h-40 overflow-y-auto space-y-1">
                @foreach ($users as $user)
                    <form method="POST" action="{{ route('chat.private') }}">
                        @csrf
                        <input type="hidden" name="user_id" value="{{ $user->id }}">
                        <button
                            type="submit"
                            class="w-full text-left px-2 py-1.5 rounded hover:bg-gray-800 text-sm"
                        >
                            {{ $user->name }}
                        </button>
                    </form>
                @endforeach
            </div>
        </div>
    </aside>

    {{-- CHAT AREA --}}
    <main class="flex-1 flex flex-col min-w-0">

        @if ($activeConversation)
            <header class="p-4 border-b border-gray-700">
                <div class="flex items-center gap-2">
                    <span class="text-xs px-2 py-0.5 rounded {{ $activeConversation->isGroup() ? 'bg-purple-600' : 'bg-green-600' }}">
                        {{ $activeConversation->isGroup() ? 'Group' : 'Private' }}
                    </span>
                    <h1 class="text-xl font-semibold">{{ $activeConversation->displayName() }}</h1>
                </div>
                @if ($activeConversation->isGroup())
                    <p class="text-sm text-gray-400 mt-1">
                        Members: {{ $activeConversation->users->pluck('name')->join(', ') }}
                    </p>
                @endif
            </header>

            <div id="messages" class="flex-1 overflow-y-auto p-4 space-y-2">
                @foreach ($activeConversation->messages as $message)
                    <div class="mb-2 {{ $message->user_id === auth()->id() ? 'text-blue-300' : '' }}">
                        <strong>{{ $message->user_id === auth()->id() ? 'You' : $message->user->name }}</strong>:
                        {{ $message->message }}
                    </div>
                @endforeach
            </div>

            <form id="chat-form" class="p-4 border-t border-gray-700">
                @csrf
                <input type="hidden" id="conversation_id" value="{{ $activeConversation->id }}">
                <div class="flex gap-2">
                    <input
                        type="text"
                        id="message"
                        class="flex-1 border rounded p-2 text-black"
                        placeholder="Type a message..."
                        required
                        autocomplete="off"
                    >
                    <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-500 rounded">
                        Send
                    </button>
                </div>
            </form>
        @else
            <div class="flex-1 flex items-center justify-center text-gray-500">
                <p>Select a conversation or start a new chat</p>
            </div>
        @endif
    </main>
</div>

{{-- CREATE GROUP MODAL --}}
<div id="group-modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/60">
    <div class="bg-gray-800 rounded-lg w-full max-w-md mx-4 p-6 shadow-xl">
        <h3 class="text-lg font-bold mb-4">Create group chat</h3>
        <form method="POST" action="{{ route('chat.group') }}" class="space-y-4">
            @csrf
            <div>
                <label for="group-name" class="block text-sm text-gray-400 mb-1">Group name</label>
                <input
                    type="text"
                    id="group-name"
                    name="name"
                    required
                    maxlength="255"
                    class="w-full rounded p-2 text-black"
                    placeholder="e.g. Project Team"
                >
            </div>
            <div>
                <label class="block text-sm text-gray-400 mb-2">Add members</label>
                <div class="max-h-48 overflow-y-auto space-y-2 border border-gray-600 rounded p-2">
                    @foreach ($users as $user)
                        <label class="flex items-center gap-2 text-sm cursor-pointer hover:bg-gray-700 p-1 rounded">
                            <input type="checkbox" name="members[]" value="{{ $user->id }}" class="rounded">
                            {{ $user->name }}
                        </label>
                    @endforeach
                </div>
            </div>
            <div class="flex justify-end gap-2">
                <button type="button" id="close-group-modal" class="px-4 py-2 rounded bg-gray-600 hover:bg-gray-500">
                    Cancel
                </button>
                <button type="submit" class="px-4 py-2 rounded bg-blue-600 hover:bg-blue-500">
                    Create
                </button>
            </div>
        </form>
    </div>
</div>

<script>
const groupModal = document.getElementById('group-modal');
document.getElementById('open-group-modal')?.addEventListener('click', () => {
    groupModal.classList.remove('hidden');
    groupModal.classList.add('flex');
});
document.getElementById('close-group-modal')?.addEventListener('click', () => {
    groupModal.classList.add('hidden');
    groupModal.classList.remove('flex');
});
groupModal?.addEventListener('click', (e) => {
    if (e.target === groupModal) {
        groupModal.classList.add('hidden');
        groupModal.classList.remove('flex');
    }
});

document.getElementById('chat-form')?.addEventListener('submit', async (e) => {
    e.preventDefault();

    const messageInput = document.getElementById('message');
    const conversationId = document.getElementById('conversation_id').value;

    const response = await fetch('{{ route('chat.send') }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
        },
        body: JSON.stringify({
            conversation_id: conversationId,
            message: messageInput.value,
        }),
    });

    const data = await response.json();
    const messages = document.getElementById('messages');

    const row = document.createElement('div');
    row.className = 'mb-2 text-blue-300';
    row.innerHTML = '<strong>You</strong>: ' + data.message.message;
    messages.appendChild(row);
    messages.scrollTop = messages.scrollHeight;

    messageInput.value = '';
});

const messagesEl = document.getElementById('messages');
if (messagesEl) {
    messagesEl.scrollTop = messagesEl.scrollHeight;
}
</script>

</x-app-layout>



