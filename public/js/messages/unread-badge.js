// messages/unread-badge.js
// Maneja el badge de mensajes no leídos en el topbar en tiempo real

document.addEventListener('DOMContentLoaded', () => {
    const badge = document.getElementById('nav-unread-badge');
    if (!badge) return;

    // Obtener el ID del usuario actual
    const userIdMeta = document.querySelector('meta[name="user-id"]');
    if (!userIdMeta) return;
    const currentUserId = userIdMeta.content;

    // Función para actualizar el badge
    async function updateUnreadBadge() {
        try {
            const res = await fetch('/api/unread-messages-count', {
                headers: { 'Accept': 'application/json' },
                credentials: 'same-origin'
            });
            if (!res.ok) return;
            const data = await res.json();
            const count = data.count || 0;

            if (count > 0) {
                badge.style.display = '';
                badge.textContent = count > 9 ? '9+' : count;
                badge.title = `${count} mensaje${count !== 1 ? 's' : ''} no leído${count !== 1 ? 's' : ''}`;
            } else {
                badge.style.display = 'none';
            }
        } catch (e) {
            console.error('Error actualizando badge:', e);
        }
    }

    // Exponer la función para que otros scripts puedan actualizar el badge
    window.updateTopbarBadge = updateUnreadBadge;

    // Suscribirse al canal privado del usuario para recibir notificaciones en tiempo real
    if (window.Echo) {
        window.Echo
            .private(`user.${currentUserId}`)
            .listen('.MessageSent', (e) => {
                // Solo actualizar si el mensaje no es del propio usuario
                if (String(e.sender_id) !== String(currentUserId)) {
                    updateUnreadBadge();
                }
            })
            .error((error) => {
                console.error('Error en canal de usuario:', error);
            });
    }

    // Actualizar al cargar la página
    updateUnreadBadge();

    // Actualizar cada 30 segundos como respaldo
    setInterval(updateUnreadBadge, 30000);
});
