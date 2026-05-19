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
            .error((error) => {
                console.error('Echo subscription failed:', error);
            });
    };

    subscribe();
}
