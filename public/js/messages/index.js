// messages/index.js
document.addEventListener('DOMContentLoaded', () => {

    // ═══ STATE ═══
    const state = {
        currentMatchId: null,
        currentUserId: null,
        currentChannel: null,
        csrfToken: null,
        elements: {},

        init() {
            this.csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
            const userMeta = document.querySelector('meta[name="user-id"]');
            if (userMeta) this.currentUserId = String(userMeta.content);
            this.cacheElements();
        },

        cacheElements() {
            this.elements = {
                convList: document.getElementById('msg-conv-list'),
                chatPlaceholder: document.getElementById('msg-chat-placeholder'),
                activeChat: document.getElementById('msg-active-chat'),
                chatBody: document.getElementById('msg-chat-body'),
                textInput: document.getElementById('msg-text-input'),
                sendBtn: document.getElementById('msg-send-btn'),
                backBtn: document.getElementById('msg-back-btn'),
                searchInput: document.getElementById('msg-search-input'),
                tabs: document.querySelectorAll('.msg-tab'),
                chatHeaderAvatar: document.getElementById('chat-header-avatar'),
                chatHeaderDot: document.getElementById('chat-header-dot'),
                chatHeaderName: document.getElementById('chat-header-name'),
                chatHeaderBadge: document.getElementById('chat-header-badge'),
                chatHeaderStatus: document.getElementById('chat-header-status'),
            };
        },

        get(elem) { return this.elements[elem]; }
    };

    // ═══ UI ═══
    const UI = {
        updateChatHeader(userName, userAvatar, isOnline) {
            if (state.get('chatHeaderAvatar')) state.get('chatHeaderAvatar').src = userAvatar;
            if (state.get('chatHeaderDot')) state.get('chatHeaderDot').style.display = isOnline ? 'block' : 'none';
            if (state.get('chatHeaderName')) state.get('chatHeaderName').textContent = userName;
            if (state.get('chatHeaderBadge')) state.get('chatHeaderBadge').style.display = isOnline ? 'inline' : 'none';
            if (state.get('chatHeaderStatus')) state.get('chatHeaderStatus').textContent = isOnline ? 'En línea' : 'Desconectado';
        },

        renderMessages(messages) {
            const chatBody = state.get('chatBody');
            if (!chatBody) return;
            if (messages.length === 0) {
                chatBody.innerHTML = `<div class="msg-empty-state" style="margin-top:2rem;"><p class="msg-empty-title">Aún no hay mensajes</p><p class="msg-empty-sub">¡Envía el primero!</p></div>`;
                return;
            }
            let lastType = null;
            chatBody.innerHTML = messages.map(msg => {
                const isSent = String(msg.sender_id) === String(state.currentUserId);
                const currentType = isSent ? 'sent' : 'received';
                const isGrouped = lastType === currentType;
                lastType = currentType;
                return this.createMessageHTML(msg, isGrouped);
            }).join('');
            chatBody.scrollTop = chatBody.scrollHeight;
        },

        appendMessage(msg) {
            const chatBody = state.get('chatBody');
            if (!chatBody) return;

            // Eliminar el estado vacío si existe
            const emptyState = chatBody.querySelector('.msg-empty-state');
            if (emptyState) emptyState.remove();

            const div = document.createElement('div');
            div.innerHTML = this.createMessageHTML(msg, false);
            chatBody.appendChild(div.firstElementChild);
            chatBody.scrollTop = chatBody.scrollHeight;
        },

        // Actualiza el preview de la conversación en el sidebar
        updateConversationPreview(matchId, body) {
            const item = document.querySelector(`.msg-conv-item[data-conv-id="${matchId}"]`);
            if (!item) return;
            const preview = item.querySelector('.msg-conv-preview');
            const time = item.querySelector('.msg-conv-time');
            if (preview) {
                preview.textContent = body.length > 35 ? body.slice(0, 35) + '...' : body;
                preview.classList.remove('unread');
            }
            if (time) time.textContent = 'ahora';
        },

        createMessageHTML(msg, isGrouped = false) {
            const isSent = String(msg.sender_id) === String(state.currentUserId);
            const rowClass = `msg-row ${isSent ? 'sent' : 'received'}${isGrouped ? ' grouped' : ''}`;
            return `<div class="${rowClass}"><div><div class="msg-bubble"><div class="msg-bubble-content">${this.escapeHtml(msg.body)}</div><div class="msg-bubble-footer"><span class="msg-bubble-time">${this.formatTime(msg.created_at)}</span>${isSent ? '<span class="msg-bubble-ticks"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg></span>' : ''}</div></div></div></div>`;
        },

        escapeHtml(text) {
            const d = document.createElement('div');
            d.textContent = text;
            return d.innerHTML;
        },

        formatTime(dateString) {
            return new Date(dateString).toLocaleTimeString('es-ES', { hour: '2-digit', minute: '2-digit' });
        },

        toggleChatPanels(showChat) {
            const chatPlaceholder = state.get('chatPlaceholder');
            const activeChat = state.get('activeChat');
            if (chatPlaceholder) chatPlaceholder.style.display = showChat ? 'none' : 'flex';
            if (activeChat) activeChat.style.display = showChat ? 'flex' : 'none';
            if (window.innerWidth < 768) {
                const sidebar = document.getElementById('msg-sidebar');
                const chatPanel = document.getElementById('msg-chat-panel');
                if (sidebar) sidebar.classList.toggle('hidden-mobile', showChat);
                if (chatPanel) chatPanel.classList.toggle('hidden-mobile', !showChat);
            }
        },

        clearChatBody() {
            const chatBody = state.get('chatBody');
            if (chatBody) chatBody.innerHTML = '';
        },

        setActiveConversation(item) {
            document.querySelectorAll('.msg-conv-item').forEach(i => i.classList.remove('active'));
            item.classList.add('active');
        },

        updateSendButtonState() {
            const textInput = state.get('textInput');
            const sendBtn = state.get('sendBtn');
            if (sendBtn && textInput) sendBtn.disabled = !textInput.value.trim();
        }
    };

    // ═══ API ═══
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
                return data.data || [];
            } catch (e) {
                console.error('Error cargando mensajes:', e);
                return [];
            }
        },

        async sendMessage(matchId, body) {
            try {
                // X-Socket-ID le dice a Laravel Reverb quién es el sender
                // para que toOthers() lo excluya y no recibas tu propio mensaje por WS
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
        }
    };

    // ═══ WEBSOCKET ═══
    const WS = {
        subscribeToMatch(matchId) {
            if (!window.Echo) { console.error('Echo no disponible'); return; }

            // Desuscribirse del canal anterior
            if (state.currentChannel && state.currentMatchId) {
                window.Echo.leave(`match.${state.currentMatchId}`);
                state.currentChannel = null;
            }

            state.currentChannel = window.Echo
                .private(`match.${matchId}`)
                // IMPORTANTE: el punto (.) es obligatorio cuando el evento usa broadcastAs()
                // sin el punto, Echo busca 'App\\Events\\MessageSent' en vez de 'MessageSent'
                .listen('.MessageSent', (e) => {
                    // Solo renderizar mensajes de otros (el propio ya tiene optimistic update)
                    if (String(e.sender_id) !== String(state.currentUserId)) {
                        UI.appendMessage(e);
                        UI.updateConversationPreview(matchId, e.body);
                    }
                })
                .error((error) => {
                    console.error('Error en canal WebSocket:', error);
                });

            console.log(`[Reverb] Suscrito a match.${matchId}`);
        },

        unsubscribeFromCurrentMatch() {
            if (state.currentMatchId && state.currentChannel) {
                try {
                    window.Echo.leave(`match.${state.currentMatchId}`);
                    state.currentChannel = null;
                    console.log(`[Reverb] Desuscrito de match.${state.currentMatchId}`);
                } catch (e) {
                    console.error('Error desuscribiéndose:', e);
                }
            }
        }
    };

    // ═══ EVENTS ═══
    const Events = {
        bindConversationItems() {
            document.querySelectorAll('.msg-conv-item').forEach(item => {
                item.addEventListener('click', () => this.openChat(item));
            });
        },

        setupTabs() {
            const tabs = state.get('tabs');
            tabs.forEach(tab => {
                tab.addEventListener('click', () => {
                    tabs.forEach(t => t.classList.remove('active'));
                    tab.classList.add('active');
                    this.filterByTab(tab.dataset.tab);
                });
            });
        },

        filterByTab(tabType) {
            document.querySelectorAll('.msg-conv-item').forEach(item => {
                const attr = `tab${tabType.charAt(0).toUpperCase() + tabType.slice(1)}`;
                const show = tabType === 'all' || item.dataset[attr] === 'true';
                item.style.display = show ? 'flex' : 'none';
            });
        },

        setupSearch() {
            const searchInput = state.get('searchInput');
            if (searchInput) {
                searchInput.addEventListener('input', () => {
                    const q = searchInput.value.toLowerCase();
                    document.querySelectorAll('.msg-conv-item').forEach(item => {
                        const name = item.dataset.userName?.toLowerCase() || '';
                        item.style.display = name.includes(q) ? 'flex' : 'none';
                    });
                });
            }
        },

        setupBackButton() {
            const backBtn = state.get('backBtn');
            if (backBtn) backBtn.addEventListener('click', () => this.closeChat());
        },

        openChat(item) {
            const matchId = item.dataset.convId;
            const userName = item.dataset.userName;
            const userAvatar = item.dataset.userAvatar;
            const isOnline = item.dataset.online === 'true';

            state.currentMatchId = matchId;

            UI.toggleChatPanels(true);
            UI.updateChatHeader(userName, userAvatar, isOnline);
            UI.setActiveConversation(item);
            UI.clearChatBody();

            API.loadMessages(matchId).then(messages => UI.renderMessages(messages));
            WS.subscribeToMatch(matchId);

            if (window.innerWidth < 768) {
                document.getElementById('msg-sidebar')?.classList.add('hidden-mobile');
                document.getElementById('msg-chat-panel')?.classList.remove('hidden-mobile');
            }
        },

        closeChat() {
            WS.unsubscribeFromCurrentMatch();
            state.currentMatchId = null;
            UI.toggleChatPanels(false);
            if (window.innerWidth < 768) {
                document.getElementById('msg-sidebar')?.classList.remove('hidden-mobile');
                document.getElementById('msg-chat-panel')?.classList.add('hidden-mobile');
            }
        },

        setupMessageInput() {
            const sendBtn = state.get('sendBtn');
            const textInput = state.get('textInput');
            if (sendBtn) sendBtn.addEventListener('click', () => this.handleSendMessage());
            if (textInput) {
                textInput.addEventListener('input', () => UI.updateSendButtonState());
                textInput.addEventListener('keypress', (e) => {
                    if (e.key === 'Enter' && !e.shiftKey) {
                        e.preventDefault();
                        this.handleSendMessage();
                    }
                });
            }
        },

        async handleSendMessage() {
            const textInput = state.get('textInput');
            const body = textInput?.value.trim();
            if (!body || !state.currentMatchId) return;

            // 1. Optimistic update: el sender ve su mensaje de inmediato
            const optimisticMsg = {
                sender_id: state.currentUserId,
                body,
                created_at: new Date().toISOString(),
            };
            UI.appendMessage(optimisticMsg);
            UI.updateConversationPreview(state.currentMatchId, body);

            // 2. Limpiar input
            textInput.value = '';
            UI.updateSendButtonState();

            // 3. Enviar al servidor con X-Socket-ID para evitar duplicado WS
            const success = await API.sendMessage(state.currentMatchId, body);
            if (!success) {
                console.error('Error enviando mensaje al servidor');
            }
        }
    };

    // ═══ INIT ═══
    state.init();
    Events.bindConversationItems();
    Events.setupTabs();
    Events.setupSearch();
    Events.setupBackButton();
    Events.setupMessageInput();
});
