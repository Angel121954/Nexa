// NexaMessages — API
const API = {
    async loadMessages(matchId) {
        try {
            const res = await fetch(`/api/matches/${matchId}/messages`, {
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': state.csrfToken,
                },
                credentials: 'same-origin'
            });
            if (!res.ok) { console.error('Error cargando mensajes:', res.status); return []; }
            const data = await res.json();

            const messages = data.data || [];
            return messages.reverse();
        } catch (e) {
            console.error('Error cargando mensajes:', e);
            return [];
        }
    },

    async sendMessage(matchId, body) {
        try {
            const socketId = window.Echo?.socketId() ?? null;

            const headers = {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': state.csrfToken,
            };
            if (socketId) headers['X-Socket-ID'] = socketId;

            const res = await fetch(`/api/matches/${matchId}/messages`, {
                method: 'POST',
                headers,
                body: JSON.stringify({ body }),
                credentials: 'same-origin'
            });
            return res.ok;
        } catch (e) {
            console.error('Error enviando:', e);
            return false;
        }
    },

    async updateMessage(messageId, body) {
        try {
            const res = await fetch(`/api/messages/${messageId}`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': state.csrfToken,
                },
                body: JSON.stringify({ body }),
                credentials: 'same-origin'
            });
            if (!res.ok) {
                const data = await res.json();
                throw new Error(data.error || 'Error al editar');
            }
            return await res.json();
        } catch (e) {
            console.error('Error editando mensaje:', e);
            throw e;
        }
    },

    async deleteMessageApi(messageId) {
        try {
            const res = await fetch(`/api/messages/${messageId}`, {
                method: 'DELETE',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': state.csrfToken,
                },
                credentials: 'same-origin'
            });
            if (!res.ok) {
                const data = await res.json();
                throw new Error(data.error || 'Error al eliminar');
            }
            return true;
        } catch (e) {
            console.error('Error eliminando mensaje:', e);
            throw e;
        }
    },

    async markAsRead(matchId) {
        try {
            await fetch(`/api/matches/${matchId}/messages/mark-read`, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': state.csrfToken,
                },
                credentials: 'same-origin'
            });
            const item = document.querySelector(`.msg-conv-item[data-conv-id="${matchId}"]`);
            if (item) {
                item.querySelector('.msg-conv-preview')?.classList.remove('unread');
                item.querySelector('.msg-unread-badge')?.remove();
            }
            window.updateTopbarBadge?.();
        } catch (e) {
            console.error('Error marcando como leído:', e);
        }
    }
};
