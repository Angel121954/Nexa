// NexaMessages — WebSocket + Chat Events

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
            .listen('.MessageEdited', (e) => {
                MessageEditUI.handleMessageEdited(e);
            })
            .listen('.MessageDeleted', (e) => {
                MessageEditUI.handleMessageDeleted(e);
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

        const nowISO = new Date().toISOString();
        const optimisticMsg = {
            sender_id: state.currentUserId,
            body,
            created_at: nowISO,
        };
        UI.appendMessage(optimisticMsg);
        UI.updateConversationPreview(state.currentMatchId, body, nowISO);

        textInput.value = '';
        UI.updateSendButtonState();

        const success = await API.sendMessage(state.currentMatchId, body);
        if (!success) {
            console.error('Error enviando mensaje al servidor');
            return;
        }

        window.updateTopbarBadge?.();
    }
};
