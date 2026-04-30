const Toast = {
    container: null,
    toasts: [],

    icons: {
        success: '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 6L9 17l-5-5" stroke-linecap="round"/></svg>',
        error: '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 6L6 18M6 6l12 12" stroke-linecap="round"/></svg>',
        info: '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M12 16v-4M12 8h.01" stroke-linecap="round"/></svg>',
    },

    init() {
        this.container = document.getElementById('toast-container');
        if (this.container) return;

        this.container = document.createElement('div');
        this.container.id = 'toast-container';
        this.container.className = 'toast-container toast-bottom-center';
        document.body.appendChild(this.container);
    },

    show(message, type = 'success', duration = 3000) {
        if (!this.container) this.init();

        const toast = document.createElement('div');
        toast.className = `toast toast-${type}`;
        toast.innerHTML = `${this.icons[type] || this.icons.info}<span>${message}</span>`;
        this.container.appendChild(toast);

        requestAnimationFrame(() => toast.classList.add('show'));

        const timer = setTimeout(() => this.dismiss(toast), duration);

        toast.addEventListener('click', () => {
            clearTimeout(timer);
            this.dismiss(toast);
        });
    },

    dismiss(toast) {
        toast.classList.remove('show');
        toast.addEventListener('transitionend', () => toast.remove(), { once: true });
        if (!toast.classList.contains('show')) toast.remove();
    },
};

document.addEventListener('DOMContentLoaded', () => Toast.init());

window.showToast = (msg, type, duration) => Toast.show(msg, type, duration);
