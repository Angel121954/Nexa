// messages/index.js
document.addEventListener('DOMContentLoaded', () => {

    // ═══ STATE ═══
    const state = {
        currentMatchId: null,
        currentUserId: null,
        currentChannel: null,
        presenceChannel: null,
        presenceMembers: new Set(),
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

    // ═══ HELPERS ═══
    function formatTimeAgo(isoString) {
        if (!isoString) return '';
        const now = Date.now();
        const then = new Date(isoString).getTime();
        const diff = Math.floor((now - then) / 1000);

        if (diff < 60 && diff >= 1) return `hace ${diff} seg`;
        if (diff < 120) return 'hace 1 min';
        if (diff < 3600) return `hace ${Math.floor(diff / 60)} min`;
        if (diff < 7200) return 'hace 1 h';
        if (diff < 86400) return `hace ${Math.floor(diff / 3600)} h`;
        if (diff < 172800) return 'ayer';
        return new Date(isoString).toLocaleDateString('es-ES', { day: '2-digit', month: '2-digit' });
    }

    function updateAllConversationTimes() {
        document.querySelectorAll('.msg-conv-item').forEach(item => {
            const timeEl = item.querySelector('.msg-conv-time');
            const lastTime = item.dataset.lastTime;
            if (timeEl && lastTime) {
                timeEl.textContent = formatTimeAgo(lastTime);
            }
        });
    }

    // ═══ UI ═══
    const UI = {
        updateChatHeader(userName, userAvatar, isOnline) {
            if (state.get('chatHeaderAvatar')) state.get('chatHeaderAvatar').src = userAvatar;
            if (state.get('chatHeaderDot')) state.get('chatHeaderDot').style.display = isOnline ? 'block' : 'none';
            if (state.get('chatHeaderName')) state.get('chatHeaderName').textContent = userName;
            if (state.get('chatHeaderBadge')) state.get('chatHeaderBadge').style.display = isOnline ? 'inline' : 'none';
            if (state.get('chatHeaderStatus')) state.get('chatHeaderStatus').textContent = isOnline ? 'En línea' : 'Desconectado';

            const activeItem = document.querySelector('.msg-conv-item.active');
            if (activeItem && state.get('chatHeaderName')) {
                const userId = activeItem.dataset.userId;
                state.get('chatHeaderName').dataset.userId = userId;
                const profileUrl = `/profile/${userId}`;
                const avatarLink = document.getElementById('chat-header-avatar-link');
                const nameLink = document.getElementById('chat-header-name-link');
                if (avatarLink) avatarLink.href = profileUrl;
                if (nameLink) nameLink.href = profileUrl;
            }
        },

        renderMessages(messages) {
            const chatBody = state.get('chatBody');
            if (!chatBody) return;

            if (messages.length === 0) {
                chatBody.innerHTML = `<div class="msg-empty-state" style="margin-top:2rem;"><p class="msg-empty-title">Aún no hay mensajes</p><p class="msg-empty-sub">¡Envía el primero!</p></div>`;
                return;
            }

            let lastType = null;
            let lastDate = null;

            const html = messages.map(msg => {
                const isSent = String(msg.sender_id) === String(state.currentUserId);
                const currentType = isSent ? 'sent' : 'received';

                const msgDate = new Date(msg.created_at).toDateString();
                let dateSeparator = '';

                if (msgDate !== lastDate) {
                    dateSeparator = `<div class="msg-date-separator"><span>${this.formatDateSeparator(msg.created_at)}</span></div>`;
                    lastDate = msgDate;
                    lastType = null; // resetear agrupación al cambiar de día
                }

                const isGrouped = lastType === currentType;
                lastType = currentType;

                return dateSeparator + this.createMessageHTML(msg, isGrouped);
            }).join('');

            chatBody.innerHTML = html;
            chatBody.scrollTop = chatBody.scrollHeight;
        },

        appendMessage(msg) {
            const chatBody = state.get('chatBody');
            if (!chatBody) return;

            const emptyState = chatBody.querySelector('.msg-empty-state');
            if (emptyState) emptyState.remove();

            const msgDate = new Date(msg.created_at).toDateString();
            const today = new Date().toDateString();
            const yesterday = new Date();
            yesterday.setDate(yesterday.getDate() - 1);
            const yesterdayStr = yesterday.toDateString();

            let dateLabel = '';
            if (msgDate === today) {
                dateLabel = 'Hoy';
            } else if (msgDate === yesterdayStr) {
                dateLabel = 'Ayer';
            } else {
                dateLabel = new Date(msg.created_at).toLocaleDateString('es-ES', {
                    day: '2-digit',
                    month: '2-digit',
                    year: 'numeric'
                });
            }

            const existingSeparators = chatBody.querySelectorAll('.msg-date-separator span');
            let needsSeparator = true;
            if (existingSeparators.length > 0) {
                const lastSeparator = existingSeparators[existingSeparators.length - 1];
                if (lastSeparator.textContent === dateLabel) {
                    needsSeparator = false;
                }
            }

            const div = document.createElement('div');
            let html = '';
            if (needsSeparator) {
                html += `<div class="msg-date-separator"><span>${dateLabel}</span></div>`;
            }
            html += this.createMessageHTML(msg, false);
            div.innerHTML = html;

            while (div.firstChild) {
                chatBody.appendChild(div.firstChild);
            }
            chatBody.scrollTop = chatBody.scrollHeight;
        },

        updateConversationPreview(matchId, body, isoTime = null) {
            const item = document.querySelector(`.msg-conv-item[data-conv-id="${matchId}"]`);
            if (!item) return;
            const preview = item.querySelector('.msg-conv-preview');
            const time = item.querySelector('.msg-conv-time');
            if (preview) {
                preview.textContent = body.length > 35 ? body.slice(0, 35) + '...' : body;
                preview.classList.remove('unread');
            }
            if (time) {
                const now = isoTime || new Date().toISOString();
                item.dataset.lastTime = now;
                time.textContent = 'hace unos segundos';
            }
            item.querySelector('.msg-unread-badge')?.remove();
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

        formatDateSeparator(dateString) {
            const date = new Date(dateString);
            const today = new Date();
            const yesterday = new Date();
            yesterday.setDate(today.getDate() - 1);

            if (date.toDateString() === today.toDateString()) {
                return 'Hoy';
            } else if (date.toDateString() === yesterday.toDateString()) {
                return 'Ayer';
            } else {
                return date.toLocaleDateString('es-ES', { day: '2-digit', month: '2-digit', year: 'numeric' });
            }
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
        },

        updateBlockState(isBlocked, isBlockedBy) {
            const textInput = state.get('textInput');
            const sendBtn = state.get('sendBtn');
            const blockNotice = document.getElementById('msg-block-notice');
            const blockNoticeText = document.getElementById('msg-block-notice-text');
            const blockBtnText = document.getElementById('msg-block-btn-text');

            if (isBlocked) {
                if (textInput) { textInput.disabled = true; textInput.placeholder = ''; }
                if (sendBtn) sendBtn.disabled = true;
                if (blockNotice) {
                    blockNotice.style.display = 'flex';
                    blockNotice.className = 'msg-block-notice blocked-by-me';
                }
                if (blockNoticeText) blockNoticeText.textContent = 'Has bloqueado a este usuario. Desbloquéalo para seguir enviando mensajes.';
                if (blockBtnText) blockBtnText.textContent = 'Desbloquear usuario';
            } else if (isBlockedBy) {
                if (textInput) { textInput.disabled = true; textInput.placeholder = ''; }
                if (sendBtn) sendBtn.disabled = true;
                if (blockNotice) {
                    blockNotice.style.display = 'flex';
                    blockNotice.className = 'msg-block-notice blocked-by-other';
                }
                if (blockNoticeText) blockNoticeText.textContent = 'Este usuario te ha bloqueado.';
                if (blockBtnText) blockBtnText.textContent = 'Bloquear usuario';
            } else {
                if (textInput) { textInput.disabled = false; textInput.placeholder = 'Escribe un mensaje...'; }
                if (sendBtn) sendBtn.disabled = true;
                if (blockNotice) {
                    blockNotice.style.display = 'none';
                    blockNotice.className = 'msg-block-notice';
                }
                if (blockBtnText) blockBtnText.textContent = 'Bloquear usuario';
            }
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

                // FIX: el controller devuelve latest() (DESC) para traer los 50 más
                // recientes. Se invierten aquí para que el chat los muestre de
                // más antiguo (arriba) a más nuevo (abajo).
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

    // ═══ WEBSOCKET ═══
    const WS = {
        userChannel: null,

        subscribeToUserChannel() {
            if (!window.Echo || !state.currentUserId) return;
            this.userChannel = window.Echo
                .private(`user.${state.currentUserId}`)
                .listen('.UserBlocked', (e) => {
                    BlockUI.handleExternalBlock(e.blocker_id, e.is_blocked);
                });
        },

        subscribeToMatchesChannel() {
            if (!window.Echo || !state.currentUserId) return;
            window.Echo.channel('matches')
                .listen('.MatchDeleted', (e) => {
                    if (String(e.deleted_by_user_id) !== String(state.currentUserId)) {
                        DeleteUI.removeMatchFromUI(e.match_id);
                        showToast('Un match ha sido eliminado.', 'info');
                    }
                });
        },

        subscribeToMatch(matchId) {
            if (!window.Echo) { console.error('Echo no disponible'); return; }

            if (state.currentChannel && state.currentMatchId) {
                window.Echo.leave(`match.${state.currentMatchId}`);
                if (state.presenceChannel) {
                    window.Echo.leave(`presence-match.${state.currentMatchId}`);
                    state.presenceChannel = null;
                    state.presenceMembers.clear();
                }
                state.currentChannel = null;
            }

            state.currentChannel = window.Echo
                .private(`match.${matchId}`)
                .listen('.MessageSent', (e) => {
                    if (String(e.sender_id) !== String(state.currentUserId)) {
                        UI.appendMessage(e);
                        UI.updateConversationPreview(matchId, e.body, e.created_at);
                        API.markAsRead(matchId);
                    }
                })
                .error((error) => {
                    console.error('Error en canal WebSocket:', error);
                });

            state.presenceChannel = window.Echo
                .join(`presence-match.${matchId}`)
                .here((users) => {
                    state.presenceMembers = new Set(users.map(u => String(u.id)));
                    if (state.presenceMembers.size >= 2) {
                        API.markAsRead(matchId);
                    }
                })
                .joining((user) => {
                    state.presenceMembers.add(String(user.id));
                    if (state.presenceMembers.size >= 2) {
                        API.markAsRead(matchId);
                    }
                })
                .leaving((user) => {
                    state.presenceMembers.delete(String(user.id));
                })
                .error((error) => {
                    console.error('Error en presence channel:', error);
                });

            console.log(`[Reverb] Suscrito a match.${matchId}`);
        },

        unsubscribeFromCurrentMatch() {
            if (state.currentMatchId) {
                try {
                    window.Echo.leave(`match.${state.currentMatchId}`);
                    if (state.presenceChannel) {
                        window.Echo.leave(`presence-match.${state.currentMatchId}`);
                        state.presenceChannel = null;
                        state.presenceMembers.clear();
                    }
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
            const userId = item.dataset.userId;

            state.currentMatchId = matchId;
            window.currentChatMatchId = matchId;

            const isOnline = window.isUserOnline ? window.isUserOnline(userId) : false;
            UI.updateChatHeader(userName, userAvatar, isOnline);
            UI.toggleChatPanels(true);
            UI.setActiveConversation(item);
            UI.clearChatBody();

            item.querySelector('.msg-conv-preview')?.classList.remove('unread');
            item.querySelector('.msg-unread-badge')?.remove();

            API.markAsRead(matchId);
            API.loadMessages(matchId).then(messages => UI.renderMessages(messages));
            WS.subscribeToMatch(matchId);

            const isBlocked = item.dataset.blocked === 'true';
            const isBlockedBy = item.dataset.blockedBy === 'true';
            UI.updateBlockState(isBlocked, isBlockedBy);

            if (window.innerWidth < 768) {
                document.getElementById('msg-sidebar')?.classList.add('hidden-mobile');
                document.getElementById('msg-chat-panel')?.classList.remove('hidden-mobile');
            }
        },

        closeChat() {
            WS.unsubscribeFromCurrentMatch();
            state.currentMatchId = null;
            window.currentChatMatchId = null;
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
            const nowISO = new Date().toISOString();
            const optimisticMsg = {
                sender_id: state.currentUserId,
                body,
                created_at: nowISO,
            };
            UI.appendMessage(optimisticMsg);
            UI.updateConversationPreview(state.currentMatchId, body, nowISO);

            // 2. Limpiar input
            textInput.value = '';
            UI.updateSendButtonState();

            // 3. Enviar al servidor
            const success = await API.sendMessage(state.currentMatchId, body);
            if (!success) {
                console.error('Error enviando mensaje al servidor');
                return;
            }

            window.updateTopbarBadge?.();
        }
    };

    // ═══ BLOCK USER ═══
    const BlockUI = {
        dropdown: document.getElementById('msg-dropdown-menu'),
        blockBtn: document.getElementById('msg-block-btn'),

        init() {
            document.querySelector('.msg-dropdown-toggle')?.addEventListener('click', (e) => {
                e.stopPropagation();
                this.toggleDropdown();
            });

            this.blockBtn?.addEventListener('click', () => this.toggleBlock());

            document.addEventListener('click', (e) => {
                if (!e.target.closest('.msg-chat-dropdown')) {
                    this.closeDropdown();
                }
            });
        },

        toggleDropdown() {
            if (!this.dropdown) return;
            this.dropdown.style.display = this.dropdown.style.display === 'none' ? 'block' : 'none';
        },

        closeDropdown() {
            if (this.dropdown) this.dropdown.style.display = 'none';
        },

        async toggleBlock() {
            const userId = document.getElementById('chat-header-name')?.dataset.userId;
            if (!userId) return;

            this.closeDropdown();

            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;

            try {
                const res = await fetch(`/profile/${userId}/block`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                    },
                    credentials: 'same-origin',
                });

                if (res.ok) {
                    const activeItem = document.querySelector('.msg-conv-item.active');
                    if (!activeItem) return;

                    const wasBlocked = activeItem.dataset.blocked === 'true';
                    const isBlocked = !wasBlocked;
                    const isBlockedBy = activeItem.dataset.blockedBy === 'true';

                    activeItem.dataset.blocked = isBlocked ? 'true' : 'false';
                    UI.updateBlockState(isBlocked, isBlockedBy);
                }
            } catch (e) {
                console.error('Error al bloquear usuario:', e);
            }
        },

        handleExternalBlock(blockerId, isBlocked) {
            const activeItem = document.querySelector('.msg-conv-item.active');
            if (!activeItem) return;
            if (String(activeItem.dataset.userId) !== String(blockerId)) return;

            activeItem.dataset.blockedBy = isBlocked ? 'true' : 'false';
            const isBlockedByMe = activeItem.dataset.blocked === 'true';
            UI.updateBlockState(isBlockedByMe, isBlocked);
        }
    };

    // ═══ REPORT USER ═══
    const ReportUI = {
        modal: document.getElementById('report-modal'),
        form: document.getElementById('report-form'),
        reportBtn: document.getElementById('msg-report-btn'),
        closeBtn: document.getElementById('report-modal-close'),
        cancelBtn: document.getElementById('report-cancel-btn'),
        userIdInput: document.getElementById('report-user-id'),
        reasonSelect: document.getElementById('report-reason'),

        init() {
            this.reportBtn?.addEventListener('click', () => this.open());
            this.closeBtn?.addEventListener('click', () => this.close());
            this.cancelBtn?.addEventListener('click', () => this.close());
            this.modal?.addEventListener('click', (e) => {
                if (e.target === this.modal || e.target.classList.contains('modal-backdrop')) {
                    this.close();
                }
            });
            this.form?.addEventListener('submit', (e) => this.submit(e));
            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape' && this.modal?.style.display !== 'none') this.close();
            });
        },

        open() {
            BlockUI.closeDropdown();
            const activeItem = document.querySelector('.msg-conv-item.active');
            if (!activeItem) return;
            const userId = activeItem.dataset.userId;
            if (!userId) return;
            this.userIdInput.value = userId;
            this.reasonSelect.value = '';
            document.getElementById('report-description').value = '';
            if (this.modal) this.modal.style.display = 'flex';
        },

        close() {
            if (this.modal) this.modal.style.display = 'none';
        },

        async submit(e) {
            e.preventDefault();
            const userId = this.userIdInput.value;
            const reason = this.reasonSelect.value;
            const description = document.getElementById('report-description').value;

            if (!reason) {
                this.reasonSelect.style.borderColor = '#ef4444';
                return;
            }
            this.reasonSelect.style.borderColor = '';

            const submitBtn = this.form.querySelector('button[type="submit"]');
            submitBtn.disabled = true;
            submitBtn.textContent = 'Enviando...';

            try {
                const res = await fetch(`/profile/${userId}/report`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content,
                    },
                    body: JSON.stringify({ reason, description }),
                    credentials: 'same-origin',
                });

                const data = await res.json();

                if (res.ok) {
                    this.close();
                    showToast('Reporte enviado. Gracias por ayudarnos a mantener la comunidad segura.', 'success');
                } else {
                    showToast(data.error || 'Error al enviar el reporte.', 'error');
                }
            } catch (e) {
                console.error('Error al reportar:', e);
                showToast('Error de conexión. Intenta de nuevo.', 'error');
            } finally {
                submitBtn.disabled = false;
                submitBtn.textContent = 'Enviar reporte';
            }
        }
    };

    // ═══ DELETE MATCH ═══
    const DeleteUI = {
        modal: document.getElementById('delete-modal'),
        deleteBtn: document.getElementById('msg-delete-btn'),
        confirmBtn: document.getElementById('delete-confirm-btn'),
        cancelBtn: document.getElementById('delete-cancel-btn'),
        closeBtn: document.getElementById('delete-modal-close'),

        init() {
            this.deleteBtn?.addEventListener('click', () => this.open());
            this.cancelBtn?.addEventListener('click', () => this.close());
            this.closeBtn?.addEventListener('click', () => this.close());
            this.modal?.addEventListener('click', (e) => {
                if (e.target === this.modal || e.target.classList.contains('modal-backdrop')) {
                    this.close();
                }
            });
            this.confirmBtn?.addEventListener('click', () => this.delete());
            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape' && this.modal?.style.display !== 'none') this.close();
            });
        },

        open() {
            BlockUI.closeDropdown();
            if (this.modal) this.modal.style.display = 'flex';
        },

        close() {
            if (this.modal) this.modal.style.display = 'none';
        },

        async delete() {
            const activeItem = document.querySelector('.msg-conv-item.active');
            if (!activeItem) return;
            const matchId = activeItem.dataset.convId;
            if (!matchId) return;

            this.confirmBtn.disabled = true;
            this.confirmBtn.textContent = 'Eliminando...';

            try {
                const res = await fetch(`/api/matches/${matchId}`, {
                    method: 'DELETE',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content,
                    },
                    credentials: 'same-origin',
                });

                if (res.ok) {
                    this.close();
                    showToast('Conversación eliminada.', 'success');
                    Events.closeChat();
                    activeItem.remove();
                    if (!document.querySelector('.msg-conv-item')) {
                        document.getElementById('msg-conv-list').innerHTML = `
                            <div class="msg-empty-state">
                                <div class="msg-empty-icon">
                                    <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                        <path d="M21 15a2 2 0 01-2 2H7l-4 4V5a2 2 0 012-2h14a2 2 0 012 2z" stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                </div>
                                <p class="msg-empty-title">Aún no hay mensajes</p>
                                <p class="msg-empty-sub">Cuando hagas match con alguien podrán escribirse aquí.</p>
                            </div>`;
                    }
                } else {
                    const data = await res.json();
                    showToast(data.error || 'Error al eliminar.', 'error');
                }
            } catch {
                showToast('Error de conexión.', 'error');
            } finally {
                this.confirmBtn.disabled = false;
                this.confirmBtn.textContent = 'Sí, eliminar';
            }
        },

        removeMatchFromUI(matchId) {
            const item = document.querySelector(`.msg-conv-item[data-conv-id="${matchId}"]`);
            if (!item) return;
            const wasActive = item.classList.contains('active');
            if (wasActive) Events.closeChat();
            item.remove();
            if (!document.querySelector('.msg-conv-item')) {
                document.getElementById('msg-conv-list').innerHTML = `
                    <div class="msg-empty-state">
                        <div class="msg-empty-icon">
                            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                <path d="M21 15a2 2 0 01-2 2H7l-4 4V5a2 2 0 012-2h14a2 2 0 012 2z" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                        </div>
                        <p class="msg-empty-title">Aún no hay mensajes</p>
                        <p class="msg-empty-sub">Cuando hagas match con alguien podrán escribirse aquí.</p>
                    </div>`;
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
    BlockUI.init();
    ReportUI.init();
    DeleteUI.init();
    WS.subscribeToUserChannel();
    WS.subscribeToMatchesChannel();

    setInterval(updateAllConversationTimes, 1000);
    updateAllConversationTimes();
});
