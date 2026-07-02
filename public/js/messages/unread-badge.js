// messages/unread-badge.js
// Maneja el badge de mensajes no leídos en el topbar en tiempo real

document.addEventListener('DOMContentLoaded', () => {
    const badge = document.getElementById('nav-unread-badge');
    const bottomBadge = document.getElementById('bottom-nav-unread-badge');

    const userIdMeta = document.querySelector('meta[name="user-id"]');
    if (!userIdMeta) return;
    const currentUserId = userIdMeta.content;

    function renderBadge(count) {
        const text = count > 9 ? '9+' : count;
        const show = count > 0;
        if (badge) {
            badge.style.display = show ? '' : 'none';
            badge.textContent = text;
            badge.title = show ? `${count} mensaje${count !== 1 ? 's' : ''} no leído${count !== 1 ? 's' : ''}` : '';
        }
        if (bottomBadge) {
            bottomBadge.style.display = show ? '' : 'none';
            bottomBadge.textContent = text;
        }
    }

    async function updateUnreadBadge() {
        try {
            const res = await fetch('/api/unread-messages-count', {
                headers: { 'Accept': 'application/json' },
                credentials: 'same-origin'
            });
            if (!res.ok) return;
            const data = await res.json();
            renderBadge(data.count || 0);
        } catch (e) {
            console.error('[Badge] Error actualizando badge:', e);
        }
    }

    window.updateTopbarBadge = updateUnreadBadge;

    function subscribeUserChannel() {
        if (!window.Echo) {
            setTimeout(subscribeUserChannel, 500);
            return;
        }
        if (!window.Echo.socketId()) {
            setTimeout(subscribeUserChannel, 500);
            return;
        }
        window.Echo
            .private(`user.${currentUserId}`)
            .listen('.MessageSent', (e) => {
                if (window.currentChatMatchId && String(window.currentChatMatchId) === String(e.match_id)) {
                    return;
                }
                if (String(e.sender_id) !== String(currentUserId)) {
                    updateUnreadBadge();
                }
            })
            .error((error) => {
                console.error('[Badge] Error en canal privado:', error);
                setTimeout(subscribeUserChannel, 2000);
            });
    }
    subscribeUserChannel();

    updateUnreadBadge();

    setInterval(updateUnreadBadge, 15000);
});
