// messages/online-status.js
// Maneja el estado de usuarios en línea usando presence channels de Reverb

document.addEventListener('DOMContentLoaded', () => {
    const userIdMeta = document.querySelector('meta[name="user-id"]');
    if (!userIdMeta || !window.Echo) return;
    const currentUserId = userIdMeta.content;

    // Set de IDs de usuarios que están en línea (vía presence channel)
    const onlineUsers = new Set();

    // Suscribirse al canal de presencia global
    window.Echo
        .join('presence-global')
        .here((users) => {
            // Todos los usuarios actualmente en línea
            users.forEach(user => {
                if (String(user.id) !== String(currentUserId)) {
                    onlineUsers.add(String(user.id));
                }
            });
            updateAllOnlineStatuses();
        })
        .joining((user) => {
            if (String(user.id) !== String(currentUserId)) {
                onlineUsers.add(String(user.id));
                updateOnlineStatus(user.id);
            }
        })
        .leaving((user) => {
            onlineUsers.delete(String(user.id));
            updateOnlineStatus(user.id);
        })
        .error((error) => {
            console.error('Error en presence-global:', error);
        });

    // Exponer la función para consultar si un usuario está en línea
    window.isUserOnline = (userId) => {
        return onlineUsers.has(String(userId));
    };

    // Actualizar todos los indicadores de estado en la página
    function updateAllOnlineStatuses() {
        document.querySelectorAll('[data-user-id]').forEach(el => {
            const userId = el.dataset.userId;
            updateOnlineStatusElement(el, userId);
        });
    }

    // Actualizar el indicador de estado de un usuario específico
    function updateOnlineStatus(userId) {
        document.querySelectorAll(`[data-user-id="${userId}"]`).forEach(el => {
            updateOnlineStatusElement(el, userId);
        });
    }

    // Actualizar un elemento específico (avatar dot, text status, etc.)
    function updateOnlineStatusElement(el, userId) {
        const isOnline = onlineUsers.has(String(userId));

        // Actualizar dots en avatares
        const dot = el.querySelector('.msg-status-dot') || el.closest('.msg-conv-item')?.querySelector('.msg-status-dot');
        if (dot) {
            dot.style.display = isOnline ? 'block' : 'none';
        }

        // Actualizar texto de estado en el header del chat
        const statusText = el.closest('.msg-conv-item')?.querySelector('.msg-conv-online-text');
        if (statusText) {
            statusText.textContent = isOnline ? 'En línea' : 'Desconectado';
        }

        // Actualizar en el header del chat activo
        const chatHeaderStatus = document.getElementById('chat-header-status');
        const chatHeaderDot = document.getElementById('chat-header-dot');
        if (chatHeaderStatus && chatHeaderDot) {
            const activeItem = document.querySelector('.msg-conv-item.active');
            if (activeItem && String(activeItem.dataset.userId) === String(userId)) {
                chatHeaderStatus.textContent = isOnline ? 'En línea' : 'Desconectado';
                chatHeaderDot.style.display = isOnline ? 'block' : 'none';
            }
        }
    }

    // Fallback: usar last_activity_at para usuarios que no están en el presence channel
    async function loadFallbackStatuses() {
        try {
            const res = await fetch('/api/users/online-status', {
                headers: { 'Accept': 'application/json' },
                credentials: 'same-origin',
                method: 'GET'
            });
            if (!res.ok) {
                console.error('Error en online-status:', res.status);
                return;
            }
            const data = await res.json();
            data.users?.forEach(user => {
                if (!onlineUsers.has(String(user.id))) {
                    // No está en el presence channel, usar last_activity_at
                    updateOnlineStatus(user.id);
                }
            });

        } catch (e) {
            console.error('Error cargando estados fallback:', e);
        }
    }

    // Cargar estados fallback al inicio
    loadFallbackStatuses();
    // Y cada 60 segundos
    setInterval(loadFallbackStatuses, 60000);
});
