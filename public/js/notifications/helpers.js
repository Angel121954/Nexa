window.NexaNotif = window.NexaNotif || {};

(function (ns) {
    'use strict';

    ns.$ = (sel, ctx) => (ctx || document).querySelector(sel);
    ns.$$ = (sel, ctx) => [...(ctx || document).querySelectorAll(sel)];
    ns.csrf = () => document.querySelector('meta[name="csrf-token"]')?.content ?? '';

    ns.updateBadgeCount = function (delta) {
        const badge = ns.$('.notif-count-badge');
        if (!badge) return;
        const current = parseInt(badge.textContent, 10) || 0;
        const next = Math.max(0, current + delta);
        if (next === 0) {
            badge.remove();
        } else {
            badge.textContent = next;
        }
    };

    ns.escapeHtml = function (text) {
        const d = document.createElement('div');
        d.textContent = text;
        return d.innerHTML;
    };

    ns.getIconSvg = function (type) {
        if (type === 'match') {
            return '<svg width="11" height="11" viewBox="0 0 24 24" fill="currentColor"><path d="M20.84 4.61a5.5 5.5 0 00-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 00-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 000-7.78z"/></svg>';
        } else if (type === 'like') {
            return '<svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M14 9V5a3 3 0 00-3-3l-4 9v11h11.28a2 2 0 002-1.7l1.38-9a2 2 0 00-2-2.3H14z" stroke-linecap="round" stroke-linejoin="round"/></svg>';
        } else if (type === 'message') {
            return '<svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M21 15a2 2 0 01-2 2H7l-4 4V5a2 2 0 012-2h14a2 2 0 012 2z" stroke-linecap="round" stroke-linejoin="round"/></svg>';
        }
        return '<svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12" stroke-linecap="round"/><line x1="12" y1="16" x2="12.01" y2="16" stroke-linecap="round"/></svg>';
    };

    ns.getActionLabel = function (type) {
        if (type === 'match') return 'Enviar mensaje';
        if (type === 'message') return 'Ver mensaje';
        if (type === 'like') return 'Ver perfil';
        return 'Ver';
    };

    ns.getReadUrl = function (id) {
        return '/notifications/' + id + '/read';
    };

    ns.buildNotifHtml = function (n) {
        const data = n.data;
        const avatar = data.actor_avatar || 'https://ui-avatars.com/api/?name=' + encodeURIComponent(data.actor_name || 'U') + '&background=E8375A&color=fff&size=80';
        const preview = data.preview ? '<p class="notif-preview">"' + ns.escapeHtml(String(data.preview).substring(0, 80)) + '"</p>' : '';
        const actionUrl = data.action_url || '';
        const actionLabel = ns.getActionLabel(n.type);
        const ts = n.timestamp || Math.floor(Date.now() / 1000);

        return '<div class="notif-item unread" data-id="' + n.id + '" data-type="' + n.type + '" data-filter="' + n.type + '">'
            + '<div class="notif-avatar-wrap">'
            + '<img class="notif-avatar" src="' + avatar + '" alt="' + ns.escapeHtml(data.actor_name || '') + '">'
            + '<div class="notif-type-icon notif-icon-' + n.type + '">' + ns.getIconSvg(n.type) + '</div>'
            + '</div>'
            + '<div class="notif-content">'
            + '<p class="notif-text"><span class="notif-actor">' + ns.escapeHtml(data.actor_name || 'Alguien') + '</span> ' + ns.escapeHtml(data.message || '') + '</p>'
            + preview
            + '<span class="notif-time" data-timestamp="' + ts + '">' + ns.timeAgo(ts) + '</span>'
            + '</div>'
            + '<div class="notif-actions">'
            + '<form method="POST" action="' + ns.getReadUrl(n.id) + '" class="notif-mark-form">'
            + '<input type="hidden" name="_token" value="' + ns.csrf() + '">'
            + '<input type="hidden" name="_method" value="PATCH">'
            + '<button type="submit" class="notif-mark-btn" title="Marcar como leída"><span class="unread-dot"></span></button>'
            + '</form>'
            + (actionUrl ? '<a href="' + actionUrl + '" class="notif-action-link">' + actionLabel + '<svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M5 12h14M12 5l7 7-7 7" stroke-linecap="round" stroke-linejoin="round"/></svg></a>' : '')
            + '</div>'
            + '</div>';
    };

    ns.filterNotifications = function (filter) {
        const items = ns.$$('.notif-item');
        const labels = ns.$$('.notif-group-label');
        const emptyState = document.getElementById('notif-empty');

        items.forEach(item => {
            const type = item.dataset.filter;
            const unread = item.classList.contains('unread');
            let visible = false;
            if (filter === 'all') visible = true;
            else if (filter === 'unread') visible = unread;
            else visible = type === filter;
            item.hidden = !visible;
        });

        labels.forEach(label => {
            const siblings = [];
            let next = label.nextElementSibling;
            while (next && !next.classList.contains('notif-group-label')) {
                if (next.classList.contains('notif-item')) siblings.push(next);
                next = next.nextElementSibling;
            }
            label.hidden = siblings.every(s => s.hidden);
        });

        const anyVisible = items.some(i => !i.hidden);
        if (emptyState) emptyState.hidden = anyVisible;
    };

    ns.timeAgo = function (ts) {
        const now = Math.floor(Date.now() / 1000);
        const diff = now - ts;

        if (diff < 5) return 'ahora mismo';
        if (diff < 60) return 'hace ' + diff + ' segundos';
        if (diff < 3600) {
            const m = Math.floor(diff / 60);
            return 'hace ' + m + (m === 1 ? ' minuto' : ' minutos');
        }
        if (diff < 86400) {
            const h = Math.floor(diff / 3600);
            return 'hace ' + h + (h === 1 ? ' hora' : ' horas');
        }
        if (diff < 604800) {
            const d = Math.floor(diff / 86400);
            return 'hace ' + d + (d === 1 ? ' día' : ' días');
        }
        const date = new Date(ts * 1000);
        return date.toLocaleDateString('es-ES', { day: 'numeric', month: 'short' });
    };

    ns.updateTimes = function () {
        document.querySelectorAll('.notif-time[data-timestamp]').forEach(el => {
            const ts = parseInt(el.dataset.timestamp, 10);
            if (ts) el.textContent = ns.timeAgo(ts);
        });
    };

    ns.updateTimes();
    setInterval(ns.updateTimes, 1000);
})(window.NexaNotif);
