// NexaMessages — Block, Report, Delete Conversation, Message Edit/Delete

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

// ═══ MESSAGE EDIT/DELETE ═══
const MessageEditUI = {
    editingId: null,

    toggleActions(btn) {
        const row = btn.closest('.msg-row');
        if (!row) return;
        const menu = row.querySelector('.msg-bubble-actions-menu');
        if (!menu) return;
        const isVisible = menu.style.display === 'block';
        document.querySelectorAll('.msg-bubble-actions-menu').forEach(m => m.style.display = 'none');
        menu.style.display = isVisible ? 'none' : 'block';
    },

    closeAllMenus() {
        document.querySelectorAll('.msg-bubble-actions-menu').forEach(m => m.style.display = 'none');
    },

    startEditing(msgId) {
        this.closeAllMenus();
        const row = document.querySelector(`.msg-row[data-msg-id="${msgId}"]`);
        if (!row) return;
        const bubble = row.querySelector('.msg-bubble');
        const content = bubble?.querySelector('.msg-bubble-content');
        const footer = bubble?.querySelector('.msg-bubble-footer');
        if (!content || !footer) return;

        const originalText = content.textContent;
        bubble.dataset.originalBody = originalText;

        content.innerHTML = `<textarea class="msg-edit-textarea" maxlength="5000">${this.escapeHtml(originalText)}</textarea>`;
        footer.style.display = 'none';

        const actionsMenu = row.querySelector('.msg-bubble-actions-menu');
        if (actionsMenu) actionsMenu.style.display = 'none';

        const saveBtn = document.createElement('button');
        saveBtn.className = 'msg-edit-save-btn';
        saveBtn.textContent = 'Guardar';
        saveBtn.type = 'button';

        const cancelBtn = document.createElement('button');
        cancelBtn.className = 'msg-edit-cancel-btn';
        cancelBtn.textContent = 'Cancelar';
        cancelBtn.type = 'button';

        const btnWrap = document.createElement('div');
        btnWrap.className = 'msg-edit-actions';
        btnWrap.appendChild(saveBtn);
        btnWrap.appendChild(cancelBtn);

        bubble.appendChild(btnWrap);

        const textarea = content.querySelector('textarea');
        textarea.focus();
        textarea.setSelectionRange(textarea.value.length, textarea.value.length);

        this.editingId = msgId;

        const saveHandler = async () => {
            saveBtn.disabled = true;
            saveBtn.textContent = 'Guardando...';
            try {
                await this.saveEdit(msgId, textarea.value);
            } finally {
                saveBtn.disabled = false;
                saveBtn.textContent = 'Guardar';
            }
        };

        saveBtn.addEventListener('click', saveHandler);

        cancelBtn.addEventListener('click', () => this.cancelEdit(msgId));

        textarea.addEventListener('keydown', (e) => {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                saveHandler();
            }
            if (e.key === 'Escape') {
                this.cancelEdit(msgId);
            }
        });

        document.addEventListener('click', this._outsideClickListener = (e) => {
            if (!row.contains(e.target) && this.editingId === msgId) {
                this.cancelEdit(msgId);
            }
        });
    },

    async saveEdit(msgId, newBody) {
        const trimmed = newBody.trim();
        if (!trimmed) return;

        const row = document.querySelector(`.msg-row[data-msg-id="${msgId}"]`);
        if (!row) return;
        const bubble = row.querySelector('.msg-bubble');
        const content = bubble?.querySelector('.msg-bubble-content');
        if (!content) return;

        const originalBody = bubble.dataset.originalBody || '';
        if (trimmed === originalBody) {
            this.cancelEdit(msgId);
            return;
        }

        content.textContent = trimmed;
        this._restoreBubbleView(msgId, true);

        try {
            await API.updateMessage(msgId, trimmed);
        } catch (e) {
            content.textContent = originalBody;
            this._restoreBubbleView(msgId, false);
            showToast(e.message || 'Error al editar el mensaje.', 'error');
        }
    },

    cancelEdit(msgId) {
        const row = document.querySelector(`.msg-row[data-msg-id="${msgId}"]`);
        if (!row) return;
        const bubble = row.querySelector('.msg-bubble');
        const content = bubble?.querySelector('.msg-bubble-content');
        if (!content) return;

        const originalBody = bubble.dataset.originalBody || '';
        content.textContent = originalBody;
        this._restoreBubbleView(msgId, true);
        this.editingId = null;

        if (this._outsideClickListener) {
            document.removeEventListener('click', this._outsideClickListener);
            this._outsideClickListener = null;
        }
    },

    _restoreBubbleView(msgId, showActions) {
        const row = document.querySelector(`.msg-row[data-msg-id="${msgId}"]`);
        if (!row) return;
        const bubble = row.querySelector('.msg-bubble');
        if (!bubble) return;

        const footer = bubble.querySelector('.msg-bubble-footer');
        if (footer) footer.style.display = '';

        const btnWrap = bubble.querySelector('.msg-edit-actions');
        if (btnWrap) btnWrap.remove();

        const actionsMenu = row.querySelector('.msg-bubble-actions-menu');
        if (actionsMenu) actionsMenu.style.display = showActions ? '' : 'none';

        const content = bubble.querySelector('.msg-bubble-content');
        if (content) {
            const textarea = content.querySelector('textarea');
            if (textarea) {
                content.textContent = textarea.value;
            }
        }

        this.editingId = null;

        if (this._outsideClickListener) {
            document.removeEventListener('click', this._outsideClickListener);
            this._outsideClickListener = null;
        }
    },

    confirmDelete(msgId) {
        this.closeAllMenus();
        const modal = document.getElementById('delete-msg-modal');
        const confirmBtn = document.getElementById('delete-msg-confirm-btn');
        const cancelBtn = document.getElementById('delete-msg-cancel-btn');
        const closeBtn = document.getElementById('delete-msg-modal-close');
        if (!modal) return;

        modal.style.display = 'flex';

        const cleanup = () => {
            modal.style.display = 'none';
            confirmBtn.disabled = false;
            confirmBtn.textContent = 'Sí, eliminar';
            confirmBtn.removeEventListener('click', deleteHandler);
            cancelBtn.removeEventListener('click', cancelHandler);
            closeBtn.removeEventListener('click', cancelHandler);
            document.removeEventListener('keydown', keyHandler);
        };

        const deleteHandler = async () => {
            confirmBtn.disabled = true;
            confirmBtn.textContent = 'Eliminando...';
            try {
                await this.deleteMessage(msgId);
                cleanup();
            } catch {
                confirmBtn.disabled = false;
                confirmBtn.textContent = 'Sí, eliminar';
            }
        };

        const cancelHandler = () => cleanup();

        const keyHandler = (e) => {
            if (e.key === 'Escape') cleanup();
        };

        confirmBtn.addEventListener('click', deleteHandler);
        cancelBtn.addEventListener('click', cancelHandler);
        closeBtn.addEventListener('click', cancelHandler);
        document.addEventListener('keydown', keyHandler);

        modal.addEventListener('click', (e) => {
            if (e.target === modal || e.target.classList.contains('modal-backdrop')) {
                cleanup();
            }
        }, { once: true });
    },

    async deleteMessage(msgId) {
        const row = document.querySelector(`.msg-row[data-msg-id="${msgId}"]`);
        if (!row) return;

        const isSent = row.classList.contains('sent');
        const isGrouped = row.classList.contains('grouped');
        const rowClass = `msg-row ${isSent ? 'sent' : 'received'}${isGrouped ? ' grouped' : ''}`;
        row.outerHTML = `<div class="${rowClass}" data-msg-id="${msgId}"><div><div class="msg-bubble msg-deleted-bubble"><div class="msg-bubble-content msg-deleted-content">Este mensaje fue eliminado</div></div></div></div>`;

        try {
            await API.deleteMessageApi(msgId);
            showToast('Mensaje eliminado.', 'success');
        } catch (e) {
            showToast(e.message || 'Error al eliminar el mensaje.', 'error');
            await API.loadMessages(state.currentMatchId).then(messages => UI.renderMessages(messages));
        }
    },

    handleMessageEdited(data) {
        if (String(data.sender_id) === String(state.currentUserId)) return;

        const row = document.querySelector(`.msg-row[data-msg-id="${data.id}"]`);
        if (!row) return;

        const bubble = row.querySelector('.msg-bubble');
        const content = bubble?.querySelector('.msg-bubble-content');
        if (!bubble || !content) return;

        content.textContent = data.body;

        let editedTag = bubble.querySelector('.msg-edited');
        if (!editedTag) {
            const footer = bubble.querySelector('.msg-bubble-footer');
            if (footer) {
                editedTag = document.createElement('span');
                editedTag.className = 'msg-edited';
                editedTag.textContent = 'editado';
                const time = footer.querySelector('.msg-bubble-time');
                if (time) {
                    time.after(editedTag);
                } else {
                    footer.prepend(editedTag);
                }
            }
        }
    },

    handleMessageDeleted(data) {
        const row = document.querySelector(`.msg-row[data-msg-id="${data.id}"]`);
        if (!row) return;

        const isSent = row.classList.contains('sent');
        const isGrouped = row.classList.contains('grouped');
        const rowClass = `msg-row ${isSent ? 'sent' : 'received'}${isGrouped ? ' grouped' : ''}`;
        row.outerHTML = `<div class="${rowClass}" data-msg-id="${data.id}"><div><div class="msg-bubble msg-deleted-bubble"><div class="msg-bubble-content msg-deleted-content">Este mensaje fue eliminado</div></div></div></div>`;

        if (state.currentMatchId) {
            const lastMsg = document.querySelector(`.msg-row[data-msg-id]`);
            if (!lastMsg || lastMsg.dataset.msgId === data.id) {
                const chatBody = state.get('chatBody');
                const allMsgs = chatBody?.querySelectorAll('.msg-row[data-msg-id]');
                if (allMsgs && allMsgs.length > 0) {
                    const last = allMsgs[allMsgs.length - 1];
                    const lastContent = last.querySelector('.msg-bubble-content');
                    if (lastContent) {
                        UI.updateConversationPreview(data.match_id, lastContent.textContent);
                    }
                } else {
                    UI.updateConversationPreview(data.match_id, '…');
                }
            }
        }
    },

    escapeHtml(text) {
        const d = document.createElement('div');
        d.textContent = text;
        return d.innerHTML;
    }
};

window.MessageEditUI = MessageEditUI;
