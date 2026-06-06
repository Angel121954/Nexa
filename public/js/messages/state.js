// NexaMessages — State
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
