<x-app-layout>

<div
    class="flex h-[calc(100vh-4rem)] bg-gray-900 text-white"
    data-chat-channel="chat.{{ auth()->id() }}"
    @if($activeConversation) 
        data-active-conversation="{{ $activeConversation->id }}" 
        data-is-group="{{ $activeConversation->isGroup() ? 'true' : 'false' }}" 
    @endif
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

        {{-- CONVERSATION LIST --}}
        <div class="flex-1 overflow-y-auto">
            @forelse ($conversations as $conversation)
                @php
                    $lastMessage = $conversation->messages->first();
                    $isActive = $activeConversation?->id === $conversation->id;

                    // Untuk private chat: ambil user lawan bicara
                    $otherUser = null;
                    if (!$conversation->isGroup()) {
                        $otherUser = $conversation->users->firstWhere('id', '!=', auth()->id());
                    }
                @endphp
                <a
                    href="{{ route('chat.show', $conversation) }}"
                    class="block px-4 py-3 border-b border-gray-800 hover:bg-gray-800 {{ $isActive ? 'bg-gray-800' : '' }}"
                >
                    <div class="flex items-center gap-2">
                        {{-- Badge DM / Group --}}
                        <span class="text-xs px-1.5 py-0.5 rounded {{ $conversation->isGroup() ? 'bg-purple-600' : 'bg-green-700' }}">
                            {{ $conversation->isGroup() ? 'Group' : 'DM' }}
                        </span>

                        {{-- Nama conversation --}}
                        <span class="font-medium truncate flex-1">{{ $conversation->displayName() }}</span>

                        {{-- Indikator online/offline untuk private chat --}}
                        @if (!$conversation->isGroup() && $otherUser)
                            <span
                                title="{{ $otherUser->is_online ? 'Online' : 'Offline' }}"
                                class="inline-block w-2.5 h-2.5 rounded-full flex-shrink-0 {{ $otherUser->is_online ? 'bg-green-400' : 'bg-gray-500' }}"
                            ></span>
                        @endif
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

        {{-- START PRIVATE CHAT (daftar user dengan indikator online/offline) --}}
        <div class="border-t border-gray-700 p-4">
            <h3 class="text-sm font-semibold text-gray-400 mb-2">Start private chat</h3>
            <div class="max-h-48 overflow-y-auto space-y-1">
                @foreach ($users as $user)
                    <form method="POST" action="{{ route('chat.private') }}">
                        @csrf
                        <input type="hidden" name="user_id" value="{{ $user->id }}">
                        <button
                            type="submit"
                            class="w-full text-left px-2 py-1.5 rounded hover:bg-gray-800 text-sm flex items-center gap-2"
                        >
                            {{-- Indikator online/offline --}}
                            <span
                                title="{{ $user->is_online ? 'Online' : ($user->last_seen ? 'Last seen ' . $user->last_seen->diffForHumans() : 'Offline') }}"
                                class="inline-block w-2 h-2 rounded-full flex-shrink-0 {{ $user->is_online ? 'bg-green-400' : 'bg-gray-500' }}"
                            ></span>
                            <span class="truncate">{{ $user->name }}</span>
                            @if ($user->is_online)
                                <span class="ml-auto text-xs text-green-400 font-medium">Online</span>
                            @elseif ($user->last_seen)
                                <span class="ml-auto text-xs text-gray-500">{{ $user->last_seen->diffForHumans() }}</span>
                            @endif
                        </button>
                    </form>
                @endforeach
            </div>
        </div>

        {{-- LOGOUT BUTTON DI SIDEBAR BAWAH --}}
        <div class="border-t border-gray-700 p-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2">
                    {{-- Dot online (milik user sendiri) --}}
                    <span class="inline-block w-2.5 h-2.5 rounded-full bg-green-400"></span>
                    <span class="text-sm font-medium truncate">{{ auth()->user()->name }}</span>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button
                        type="submit"
                        id="logout-btn"
                        class="text-xs px-3 py-1.5 bg-red-600 hover:bg-red-500 rounded text-white transition-colors duration-150"
                        title="Logout"
                    >
                        Logout
                    </button>
                </form>
            </div>
        </div>

    </aside>

    {{-- CHAT AREA --}}
    <main class="flex-1 flex flex-col min-w-0">

        @if ($activeConversation)
            <header class="p-4 border-b border-gray-700">
                <div class="flex items-center gap-2">
                    <span class="text-xs px-2 py-0.5 rounded {{ $activeConversation->isGroup() ? 'bg-purple-600' : 'bg-green-700' }}">
                        {{ $activeConversation->isGroup() ? 'Group' : 'Private' }}
                    </span>
                    <h1 class="text-xl font-semibold">{{ $activeConversation->displayName() }}</h1>

                    {{-- Indikator online untuk private chat di header --}}
                    @if (!$activeConversation->isGroup())
                        @php
                            $chatPartner = $activeConversation->users->firstWhere('id', '!=', auth()->id());
                        @endphp
                        @if ($chatPartner)
                            <span class="flex items-center gap-1 ml-2">
                                <span class="inline-block w-2.5 h-2.5 rounded-full {{ $chatPartner->is_online ? 'bg-green-400' : 'bg-gray-500' }}"></span>
                                <span class="text-xs {{ $chatPartner->is_online ? 'text-green-400' : 'text-gray-400' }}">
                                    {{ $chatPartner->is_online ? 'Online' : ($chatPartner->last_seen ? 'Last seen ' . $chatPartner->last_seen->diffForHumans() : 'Offline') }}
                                </span>
                            </span>
                        @endif
                    @endif
                </div>

                @if ($activeConversation->isGroup())
                    <p class="text-sm text-gray-400 mt-1">
                        Members:
                        @foreach ($activeConversation->users as $member)
                            <span class="inline-flex items-center gap-1">
                                <span class="inline-block w-1.5 h-1.5 rounded-full {{ $member->is_online ? 'bg-green-400' : 'bg-gray-500' }}"></span>
                                {{ $member->name }}{{ !$loop->last ? ',' : '' }}
                            </span>
                        @endforeach
                    </p>
                @endif
            </header>

            <div id="messages" class="flex-1 overflow-y-auto p-4 space-y-4 flex flex-col">
                @foreach ($activeConversation->messages as $message)
                    @php
                        $isOwn = $message->user_id === auth()->id();
                    @endphp
                    <div class="flex {{ $isOwn ? 'justify-end' : 'justify-start' }}">
                        <div class="max-w-[75%] rounded-2xl px-4 py-2 {{ $isOwn ? 'bg-blue-600 text-white rounded-br-sm' : 'bg-gray-700 text-gray-100 rounded-bl-sm' }}">
                            @if (!$isOwn && $activeConversation->isGroup())
                                <div class="text-xs font-semibold text-gray-400 mb-1">{{ $message->user->name }}</div>
                            @endif
                            <div class="break-words">{{ $message->message }}</div>
                        </div>
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
                            <span class="inline-block w-2 h-2 rounded-full {{ $user->is_online ? 'bg-green-400' : 'bg-gray-500' }}"></span>
                            {{ $user->name }}
                            @if ($user->is_online)
                                <span class="text-xs text-green-400">Online</span>
                            @endif
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
    row.className = 'flex justify-end';
    row.innerHTML = `
        <div class="max-w-[75%] rounded-2xl px-4 py-2 bg-blue-600 text-white rounded-br-sm">
            <div class="break-words">${data.message.message}</div>
        </div>
    `;
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
