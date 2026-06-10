// NexaMessages — UI
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
                lastType = null;
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
        const msgId = msg.id ? `data-msg-id="${msg.id}"` : '';

        if (msg.deleted_at) {
            return `<div class="${rowClass}" ${msgId}><div><div class="msg-bubble msg-deleted-bubble"><div class="msg-bubble-content msg-deleted-content">Este mensaje fue eliminado</div></div></div></div>`;
        }

        const editedTag = msg.edited_at ? '<span class="msg-edited">editado</span>' : '';
        const actionsBtn = isSent && msg.id ? `<button class="msg-bubble-actions-btn" onclick="event.stopPropagation();MessageEditUI.toggleActions(this)" type="button"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="5" r="1.5" fill="currentColor" stroke="none"/><circle cx="12" cy="12" r="1.5" fill="currentColor" stroke="none"/><circle cx="12" cy="19" r="1.5" fill="currentColor" stroke="none"/></svg></button>` : '';
        const actionsMenu = isSent && msg.id ? `<div class="msg-bubble-actions-menu" style="display:none;"><button type="button" class="msg-bubble-action-item" onclick="event.stopPropagation();MessageEditUI.startEditing(${msg.id})">Editar</button><button type="button" class="msg-bubble-action-item danger" onclick="event.stopPropagation();MessageEditUI.confirmDelete(${msg.id})">Eliminar</button></div>` : '';
        return `<div class="${rowClass}" ${msgId}><div><div class="msg-bubble">${actionsBtn}<div class="msg-bubble-content">${this.escapeHtml(msg.body)}</div><div class="msg-bubble-footer"><span class="msg-bubble-time">${this.formatTime(msg.created_at)}</span>${editedTag}</div></div>${actionsMenu}</div></div>`;
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
