/**
 * messages/index.js — Nexa
 * Maneja la interactividad de la página /messages:
 *  - Selección de conversación
 *  - Carga de mensajes vía fetch
 *  - Envío de mensaje
 *  - Tabs (Todas / Coincidencias / No leídos)
 *  - Búsqueda en sidebar
 *  - Responsive back-button (mobile)
 */
(function () {
    'use strict';

    /* ── Estado ──────────────────────────────────────────── */
    const state = {
        activeConvId: null,
        currentTab: 'all',
        messages: {}        // { [convId]: Array<MsgObj> }
    };

    /* ── Elementos DOM ───────────────────────────────────── */
    const sidebar = document.getElementById('msg-sidebar');
    const chatPanel = document.getElementById('msg-chat-panel');
    const convList = document.getElementById('msg-conv-list');
    const chatBody = document.getElementById('msg-chat-body');
    const textInput = document.getElementById('msg-text-input');
    const sendBtn = document.getElementById('msg-send-btn');
    const activeChat = document.getElementById('msg-active-chat');
    const placeholder = document.getElementById('msg-chat-placeholder');
    const backBtn = document.getElementById('msg-back-btn');
    const searchInput = document.getElementById('msg-search-input');
    const tabs = document.querySelectorAll('.msg-tab');

    const headerAvatar = document.getElementById('chat-header-avatar');
    const headerName = document.getElementById('chat-header-name');
    const headerStatus = document.getElementById('chat-header-status');
    const headerDot = document.getElementById('chat-header-dot');
    const headerBadge = document.getElementById('chat-header-badge');

    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content ?? '';

    /* ── Helpers ─────────────────────────────────────────── */
    function nowTime() {
        const d = new Date();
        return d.getHours().toString().padStart(2, '0') + ':' +
            d.getMinutes().toString().padStart(2, '0');
    }

    function scrollBottom() {
        if (chatBody) chatBody.scrollTop = chatBody.scrollHeight;
    }

    /* ── Seleccionar conversación ────────────────────────── */
    function selectConversation(item) {
        document.querySelectorAll('.msg-conv-item').forEach(i => i.classList.remove('active'));
        item.classList.add('active');

        const convId = item.dataset.convId;
        const name = item.dataset.userName ?? '';
        const avatar = item.dataset.userAvatar ?? '';
        const isOnline = item.dataset.online === 'true';

        state.activeConvId = convId;

        /* Actualizar header del chat */
        if (headerAvatar) { headerAvatar.src = avatar; headerAvatar.alt = name; }
        if (headerName) headerName.textContent = name;
        if (headerStatus) headerStatus.textContent = isOnline ? 'En línea' : 'Desconectado/a';
        if (headerDot) headerDot.style.display = isOnline ? 'block' : 'none';
        if (headerBadge) headerBadge.style.display = isOnline ? 'inline-block' : 'none';

        /* Mostrar panel activo */
        if (placeholder) placeholder.style.display = 'none';
        if (activeChat) { activeChat.style.display = 'flex'; }

        /* Limpiar badge de no leídos */
        const badge = item.querySelector('.msg-unread-badge');
        const preview = item.querySelector('.msg-conv-preview');
        if (badge) badge.remove();
        if (preview) preview.classList.remove('unread');

        /* Renderizar mensajes en caché o cargar del servidor */
        renderMessages(convId);
        loadMessages(convId);

        /* Mobile: ocultar sidebar */
        if (window.innerWidth <= 768) {
            sidebar?.classList.add('hidden-mobile');
            chatPanel?.classList.remove('hidden-mobile');
        }
    }

    /* ── Cargar mensajes (fetch) ─────────────────────────── */
    function loadMessages(convId) {
        fetch(`/messages/${convId}/load`, {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': csrfToken
            }
        })
            .then(r => r.ok ? r.json() : null)
            .then(data => {
                if (!data || !Array.isArray(data.messages)) return;
                state.messages[convId] = data.messages;
                renderMessages(convId);
            })
            .catch(() => { /* red / servidor no disponible */ });
    }

    /* ── Renderizar lista de mensajes ────────────────────── */
    function renderMessages(convId) {
        if (!chatBody) return;

        chatBody.innerHTML = '';

        /* Divider de fecha */
        const divider = document.createElement('div');
        divider.className = 'msg-date-divider';
        divider.innerHTML = '<span>Hoy</span>';
        chatBody.appendChild(divider);

        const msgs = state.messages[convId] ?? [];
        msgs.forEach(msg => chatBody.appendChild(buildBubble(msg)));

        scrollBottom();
    }

    /* ── Construir burbuja DOM ───────────────────────────── */
    function buildBubble({ body, sent, time, ticks }) {
        const convItem = document.querySelector(`.msg-conv-item[data-conv-id="${state.activeConvId}"]`);
        const userAvatar = convItem?.dataset.userAvatar ?? '';

        const row = document.createElement('div');
        row.className = `msg-row ${sent ? 'sent' : 'received'}`;

        /* Avatar (solo mensajes recibidos) */
        if (!sent) {
            const avatarWrap = document.createElement('div');
            avatarWrap.className = 'msg-row-avatar';
            const img = document.createElement('img');
            img.src = userAvatar;
            img.alt = '';
            avatarWrap.appendChild(img);
            row.appendChild(avatarWrap);
        }

        /* Bubble */
        const bubble = document.createElement('div');
        bubble.className = 'msg-bubble';
        bubble.appendChild(document.createTextNode(body));

        /* Footer: hora + ticks */
        const footer = document.createElement('div');
        footer.className = 'msg-bubble-footer';

        const timeSpan = document.createElement('span');
        timeSpan.className = 'msg-bubble-time';
        timeSpan.textContent = time ?? '';
        footer.appendChild(timeSpan);

        if (sent) {
            const tickSpan = document.createElement('span');
            tickSpan.className = 'msg-bubble-ticks';
            /* Double tick = leído, single = enviado */
            tickSpan.innerHTML = (ticks === 2)
                ? `<svg width="18" height="11" viewBox="0 0 18 11" fill="none">
                       <path d="M1 5.5L5 9.5 13 1" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                       <path d="M6 5.5L10 9.5 18 1" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                   </svg>`
                : `<svg width="13" height="10" viewBox="0 0 13 10" fill="none">
                       <path d="M1 5l3.5 4L12 1" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                   </svg>`;
            footer.appendChild(tickSpan);
        }

        bubble.appendChild(footer);
        row.appendChild(bubble);
        return row;
    }

    /* ── Enviar mensaje ──────────────────────────────────── */
    function sendMessage() {
        const body = textInput?.value.trim();
        if (!body || !state.activeConvId) return;

        const time = nowTime();
        const msgObj = { body, sent: true, time, ticks: 1 };

        if (!state.messages[state.activeConvId]) state.messages[state.activeConvId] = [];
        state.messages[state.activeConvId].push(msgObj);

        chatBody?.appendChild(buildBubble(msgObj));
        scrollBottom();

        /* Actualizar preview en sidebar */
        const item = document.querySelector(`.msg-conv-item[data-conv-id="${state.activeConvId}"]`);
        if (item) {
            const preview = item.querySelector('.msg-conv-preview');
            const timeEl = item.querySelector('.msg-conv-time');
            if (preview) preview.textContent = body.length > 35 ? body.slice(0, 35) + '…' : body;
            if (timeEl) timeEl.textContent = time;
        }

        if (textInput) textInput.value = '';
        if (sendBtn) sendBtn.disabled = true;

        /* POST al servidor */
        fetch(`/messages/${state.activeConvId}/send`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify({ body })
        })
            .then(r => r.ok ? r.json() : null)
            .catch(() => { });
    }

    /* ── Tabs ────────────────────────────────────────────── */
    function filterByTab(tab) {
        document.querySelectorAll('.msg-conv-item').forEach(item => {
            let visible = false;
            if (tab === 'all') visible = true;
            if (tab === 'matches') visible = item.dataset.tabMatches === 'true';
            if (tab === 'unread') visible = item.dataset.tabUnread === 'true';
            item.style.display = visible ? 'flex' : 'none';
        });
    }

    tabs.forEach(tab => {
        tab.addEventListener('click', () => {
            tabs.forEach(t => t.classList.remove('active'));
            tab.classList.add('active');
            state.currentTab = tab.dataset.tab;
            filterByTab(state.currentTab);
        });
    });

    /* ── Búsqueda ────────────────────────────────────────── */
    searchInput?.addEventListener('input', () => {
        const q = searchInput.value.toLowerCase().trim();
        document.querySelectorAll('.msg-conv-item').forEach(item => {
            const name = (item.dataset.userName ?? '').toLowerCase();
            item.style.display = name.includes(q) ? 'flex' : 'none';
        });
    });

    /* ── Eventos del input ───────────────────────────────── */
    textInput?.addEventListener('input', () => {
        if (sendBtn) sendBtn.disabled = textInput.value.trim().length === 0;
    });

    textInput?.addEventListener('keydown', e => {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            sendMessage();
        }
    });

    sendBtn?.addEventListener('click', sendMessage);

    /* ── Clic en lista de conversaciones ─────────────────── */
    convList?.addEventListener('click', e => {
        const item = e.target.closest('.msg-conv-item');
        if (item) selectConversation(item);
    });

    /* ── Back button (mobile) ────────────────────────────── */
    backBtn?.addEventListener('click', () => {
        sidebar?.classList.remove('hidden-mobile');
        chatPanel?.classList.add('hidden-mobile');
        state.activeConvId = null;
    });

    /* ── Auto-seleccionar primera conversación ───────────── */
    const firstConv = document.querySelector('.msg-conv-item');
    if (firstConv) selectConversation(firstConv);

})();