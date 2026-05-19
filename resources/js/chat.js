export function initChatListener() {
    const channel = document.querySelector('[data-chat-channel]')?.dataset.chatChannel;
    const activeConversationId = document.querySelector('[data-active-conversation]')?.dataset.activeConversation;

    if (!channel) {
        return;
    }

    const subscribe = () => {
        if (!window.Echo) {
            requestAnimationFrame(subscribe);
            return;
        }

        window.Echo.private(channel)
            .listen('.MessageSent', (e) => {
                if (
                    activeConversationId &&
                    String(e.conversation_id) !== String(activeConversationId)
                ) {
                    return;
                }

                const messages = document.getElementById('messages');

                if (!messages) {
                    return;
                }

                const text = e.message?.message ?? '';
                const name = e.message?.user?.name ?? 'Unknown';

                const row = document.createElement('div');
                row.className = 'flex justify-start';
                
                const isGroup = document.querySelector('[data-is-group]')?.dataset.isGroup === 'true';
                const nameHtml = isGroup ? `<div class="text-xs font-semibold text-gray-400 mb-1">${name}</div>` : '';
                
                row.innerHTML = `
                    <div class="max-w-[75%] rounded-2xl px-4 py-2 bg-gray-700 text-gray-100 rounded-bl-sm">
                        ${nameHtml}
                        <div class="break-words">${text}</div>
                    </div>
                `;

                messages.appendChild(row);
                messages.scrollTop = messages.scrollHeight;
            })
            .listen('.ConversationCreated', (e) => {
                const conversation = e.conversation;
                const conversationsList = document.getElementById('conversations-list');
                
                if (!conversationsList) return;
                
                // Remove the "No conversations yet" message if it exists
                const emptyMsg = conversationsList.querySelector('p.text-gray-500');
                if (emptyMsg) {
                    emptyMsg.remove();
                }

                // Check if it already exists
                const existingLink = conversationsList.querySelector(`a[href$="/chat/conversation/${conversation.id}"]`);
                if (existingLink) return;

                const isGroup = conversation.type === 'group';
                
                // Determine display name
                let displayName = '';
                if (isGroup) {
                    displayName = conversation.name;
                } else {
                    // For private chat, find the other user's name
                    const currentUserId = channel.split('.')[1];
                    const otherUser = conversation.users.find(u => String(u.id) !== String(currentUserId));
                    displayName = otherUser ? otherUser.name : 'Unknown User';
                }

                const badgeClass = isGroup ? 'bg-purple-600' : 'bg-green-700';
                const badgeText = isGroup ? 'Group' : 'DM';

                const a = document.createElement('a');
                a.href = `/chat/conversation/${conversation.id}`;
                a.className = 'block px-4 py-3 border-b border-gray-800 hover:bg-gray-800';
                a.innerHTML = `
                    <div class="flex items-center gap-2">
                        <span class="text-xs px-1.5 py-0.5 rounded ${badgeClass}">
                            ${badgeText}
                        </span>
                        <span class="font-medium truncate flex-1">${displayName}</span>
                    </div>
                    <p class="text-sm text-gray-500 mt-1">No messages yet</p>
                `;

                // Append to the bottom of the list
                conversationsList.appendChild(a);
            })
            .error((error) => {
                console.error('Echo subscription failed:', error);
            });
    };

    subscribe();
}
