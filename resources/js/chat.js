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
                row.className = 'mb-2';

                const strong = document.createElement('strong');
                strong.textContent = name;
                row.appendChild(strong);
                row.appendChild(document.createTextNode(': ' + text));

                messages.appendChild(row);
                messages.scrollTop = messages.scrollHeight;
            })
            .error((error) => {
                console.error('Echo subscription failed:', error);
            });
    };

    subscribe();
}
