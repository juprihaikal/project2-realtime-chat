<x-app-layout>

<div class="flex h-screen">

    <!-- sidebar -->
    <div class="w-1/4 border-r p-4">

        <h2 class="text-xl font-bold mb-4">
            Users
        </h2>

        @foreach ($users as $user)

            <a href="/chat/{{ $user->id }}">

                <div
                    class="p-2 border-b hover:bg-gray-100"
                >

                    {{ $user->name }}

                </div>

            </a>

        @endforeach

    </div>

    <!-- chat -->
    <div class="w-3/4 flex flex-col">

        <!-- header -->
        <div class="p-4 border-b">

            @if($receiver)

                Chat with
                {{ $receiver->name }}

            @else

                Select User

            @endif

        </div>

        <!-- messages -->
        <div
            id="messages"
            class="flex-1 overflow-y-auto p-4"
        >

            @foreach ($messages as $message)

                <div class="mb-2">

                    <strong>

                        {{ $message->user->name }}

                    </strong>

                    :

                    {{ $message->message }}

                </div>

            @endforeach

        </div>

        <!-- form -->
        @if($receiver)

        <form
            id="chat-form"
            class="p-4 border-t"
        >

            @csrf

            <input
                type="hidden"
                id="receiver_id"
                value="{{ $receiver->id }}"
            >

            <div class="flex">

                <input
                    type="text"
                    id="message"
                    class="w-full border rounded p-2"
                    placeholder="Type message..."
                >

                <button
                    class="ml-2 px-4 py-2 bg-blue-500 text-white rounded"
                >

                    Send

                </button>

            </div>

        </form>

        @endif

    </div>

</div>

</x-app-layout>