/* ═══════════════════════════════════════════════════
   NEXA — notifications.js
   Responsabilidades:
     1. Filtrado por tabs (all / unread / match / like / message)
     2. Panel de preferencias (slide-in)
     3. Marcar como leída vía fetch sin recargar
   ═══════════════════════════════════════════════════ */

(function () {
    'use strict';

    /* ── Helpers ── */
    const $ = (sel, ctx = document) => ctx.querySelector(sel);
    const $$ = (sel, ctx = document) => [...ctx.querySelectorAll(sel)];
    const csrf = () => $('meta[name="csrf-token"]')?.content ?? '';

    /* ─────────────────────────────────────────────
       1. TAB FILTERING
    ───────────────────────────────────────────── */
    const tabs = $$('.notif-tab');
    const items = $$('.notif-item');
    const labels = $$('.notif-group-label');
    const emptyState = $('#notif-empty');

    function filterNotifications(filter) {
        // Show/hide items
        items.forEach(item => {
            const type = item.dataset.filter;
            const unread = item.classList.contains('unread');

            let visible = false;
            if (filter === 'all') visible = true;
            else if (filter === 'unread') visible = unread;
            else visible = type === filter;

            item.hidden = !visible;
        });

        // Show/hide group labels (hide if no visible item follows in its group)
        labels.forEach((label, i) => {
            // Collect items until next label
            const siblings = [];
            let next = label.nextElementSibling;
            while (next && !next.classList.contains('notif-group-label')) {
                if (next.classList.contains('notif-item')) siblings.push(next);
                next = next.nextElementSibling;
            }
            label.hidden = siblings.every(s => s.hidden);
        });

        // Empty state
        const anyVisible = items.some(i => !i.hidden);
        if (emptyState) emptyState.hidden = anyVisible;
    }

    tabs.forEach(tab => {
        tab.addEventListener('click', () => {
            tabs.forEach(t => t.classList.remove('active'));
            tab.classList.add('active');
            filterNotifications(tab.dataset.filter);
        });
    });

    /* ─────────────────────────────────────────────
       2. MARK AS READ (single)
    ───────────────────────────────────────────── */
    $$('.notif-mark-form').forEach(form => {
        form.addEventListener('submit', async e => {
            e.preventDefault();
            const item = form.closest('.notif-item');
            const url = form.action;

            try {
                const res = await fetch(url, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrf(),
                        'X-HTTP-Method-Override': 'PATCH',
                        'Accept': 'application/json',
                    },
                });

                if (!res.ok) throw new Error('Server error');

                // Visual feedback
                item.classList.remove('unread');
                item.style.removeProperty('background');
                item.querySelector('.notif-mark-btn')?.remove();
                item.classList.remove('unread');
                if (item.style.cssText.includes('border-left')) {
                    item.style.borderLeft = '';
                }

                // Update unread count in header badge
                updateBadgeCount(-1);

            } catch (err) {
                console.error('[Nexa] mark-as-read failed', err);
            }
        });
    });

    /* ─────────────────────────────────────────────
       3. MARK ALL AS READ
    ───────────────────────────────────────────── */
    const readAllForm = $('.notif-readall-form');
    if (readAllForm) {
        readAllForm.addEventListener('submit', async e => {
            e.preventDefault();

            try {
                const res = await fetch(readAllForm.action, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrf(),
                        'X-HTTP-Method-Override': 'PATCH',
                        'Accept': 'application/json',
                    },
                });

                if (!res.ok) throw new Error('Server error');

                // Update all items
                $$('.notif-item.unread').forEach(item => {
                    item.classList.remove('unread');
                    item.querySelector('.notif-mark-btn')?.remove();
                });

                // Hide the button
                readAllForm.closest('.notif-header-actions')
                    ?.querySelector('.btn-readall')?.remove();

                // Clear badge
                const badge = $('.notif-count-badge');
                if (badge) badge.remove();

                const tabBadge = $('.tab-count-pink');
                if (tabBadge) {
                    tabBadge.classList.remove('tab-count-pink');
                    tabBadge.textContent = '0';
                }

            } catch (err) {
                console.error('[Nexa] mark-all-read failed', err);
                // Fallback: submit normally
                readAllForm.submit();
            }
        });
    }

    /* ─────────────────────────────────────────────
       4. BADGE COUNTER HELPER
    ───────────────────────────────────────────── */
    function updateBadgeCount(delta) {
        const badge = $('.notif-count-badge');
        if (!badge) return;

        const current = parseInt(badge.textContent, 10) || 0;
        const next = Math.max(0, current + delta);

        if (next === 0) {
            badge.remove();
        } else {
            badge.textContent = next;
        }
    }

    /* ─────────────────────────────────────────────
       5. SETTINGS PANEL
    ───────────────────────────────────────────── */
    const settingsBtn = $('#notif-settings-btn');
    const settingsPanel = $('#notif-settings-panel');
    const settingsOverlay = $('.notif-settings-overlay');
    const nspClose = $('#nsp-close');

    function openSettings() {
        settingsPanel?.classList.add('open');
        settingsOverlay?.classList.add('open');
        settingsPanel?.setAttribute('aria-hidden', 'false');
        document.body.style.overflow = 'hidden';
    }

    function closeSettings() {
        settingsPanel?.classList.remove('open');
        settingsOverlay?.classList.remove('open');
        settingsPanel?.setAttribute('aria-hidden', 'true');
        document.body.style.overflow = '';
    }

    settingsBtn?.addEventListener('click', openSettings);
    nspClose?.addEventListener('click', closeSettings);
    settingsOverlay?.addEventListener('click', closeSettings);

    document.addEventListener('keydown', e => {
        if (e.key === 'Escape') closeSettings();
    });

    /* ─────────────────────────────────────────────
        6. REAL-TIME NOTIFICATIONS VIA ECHO
    ───────────────────────────────────────────── */
    const userId = document.querySelector('meta[name="user-id"]')?.content;
    const notifList = $('#notif-list');

    function getIconSvg(type) {
        if (type === 'match') {
            return '<svg width="11" height="11" viewBox="0 0 24 24" fill="currentColor"><path d="M20.84 4.61a5.5 5.5 0 00-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 00-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 000-7.78z"/></svg>';
        } else if (type === 'like') {
            return '<svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M14 9V5a3 3 0 00-3-3l-4 9v11h11.28a2 2 0 002-1.7l1.38-9a2 2 0 00-2-2.3H14z" stroke-linecap="round" stroke-linejoin="round"/></svg>';
        } else if (type === 'message') {
            return '<svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M21 15a2 2 0 01-2 2H7l-4 4V5a2 2 0 012-2h14a2 2 0 012 2z" stroke-linecap="round" stroke-linejoin="round"/></svg>';
        } else {
            return '<svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12" stroke-linecap="round"/><line x1="12" y1="16" x2="12.01" y2="16" stroke-linecap="round"/></svg>';
        }
    }

    function getActionLabel(type) {
        if (type === 'match') return 'Enviar mensaje';
        if (type === 'message') return 'Ver mensaje';
        if (type === 'like') return 'Ver perfil';
        return 'Ver';
    }

    function getReadUrl(id) {
        return '/notifications/' + id + '/read';
    }

    function buildNotifHtml(n) {
        const data = n.data;
        const avatar = data.actor_avatar || 'https://ui-avatars.com/api/?name=' + encodeURIComponent(data.actor_name || 'U') + '&background=E8375A&color=fff&size=80';
        const preview = data.preview ? '<p class="notif-preview">"' + escapeHtml(String(data.preview).substring(0, 80)) + '"</p>' : '';
        const actionUrl = data.action_url || '';
        const actionLabel = getActionLabel(n.type);

        return '<div class="notif-item unread" data-id="' + n.id + '" data-type="' + n.type + '" data-filter="' + n.type + '">'
            + '<div class="notif-avatar-wrap">'
            + '<img class="notif-avatar" src="' + avatar + '" alt="' + escapeHtml(data.actor_name || '') + '">'
            + '<div class="notif-type-icon notif-icon-' + n.type + '">' + getIconSvg(n.type) + '</div>'
            + '</div>'
            + '<div class="notif-content">'
            + '<p class="notif-text"><span class="notif-actor">' + escapeHtml(data.actor_name || 'Alguien') + '</span> ' + escapeHtml(data.message || '') + '</p>'
            + preview
            + '<span class="notif-time">' + n.created_at + '</span>'
            + '</div>'
            + '<div class="notif-actions">'
            + '<form method="POST" action="' + getReadUrl(n.id) + '" class="notif-mark-form">'
            + '<input type="hidden" name="_token" value="' + csrf() + '">'
            + '<input type="hidden" name="_method" value="PATCH">'
            + '<button type="submit" class="notif-mark-btn" title="Marcar como leída"><span class="unread-dot"></span></button>'
            + '</form>'
            + (actionUrl ? '<a href="' + actionUrl + '" class="notif-action-link">' + actionLabel + '<svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M5 12h14M12 5l7 7-7 7" stroke-linecap="round" stroke-linejoin="round"/></svg></a>' : '')
            + '</div>'
            + '</div>';
    }

    function escapeHtml(text) {
        const d = document.createElement('div');
        d.textContent = text;
        return d.innerHTML;
    }

    if (window.Echo && userId) {
        window.Echo.private('user.' + userId)
            .listen('.NotificationCreated', (e) => {
                const notifList = document.getElementById('notif-list');
                if (!notifList) return;

                const html = buildNotifHtml(e);

                let hoyLabel = notifList.querySelector('.notif-group-label[data-group="Hoy"]');

                if (hoyLabel) {
                    hoyLabel.insertAdjacentHTML('afterend', html);
                } else {
                    const empty = document.getElementById('notif-empty');
                    if (empty) empty.remove();
                    notifList.insertAdjacentHTML('afterbegin', '<div class="notif-group-label" data-group="Hoy">Hoy</div>' + html);
                }

                const newItem = notifList.querySelector('.notif-item[data-id="' + e.id + '"]');
                if (newItem) {
                    const form = newItem.querySelector('.notif-mark-form');
                    if (form) {
                        form.addEventListener('submit', async function (ev) {
                            ev.preventDefault();
                            const url = form.action;
                            try {
                                const res = await fetch(url, {
                                    method: 'POST',
                                    headers: {
                                        'X-CSRF-TOKEN': csrf(),
                                        'X-HTTP-Method-Override': 'PATCH',
                                        'Accept': 'application/json',
                                    },
                                });
                                if (!res.ok) throw new Error();
                                newItem.classList.remove('unread');
                                newItem.querySelector('.notif-mark-btn')?.remove();
                                updateBadgeCount(-1);
                            } catch (err) {
                                console.error('[Nexa] mark-as-read failed', err);
                            }
                        });
                    }
                }

                const currentFilter = $('.notif-tab.active')?.dataset?.filter || 'all';
                if (newItem) {
                    const type = newItem.dataset.filter;
                    const visible = currentFilter === 'all' || currentFilter === 'unread' || type === currentFilter;
                    newItem.hidden = !visible;
                }

                updateBadgeCount(1);
            });
    }

    /* ─────────────────────────────────────────────
        7. SETTINGS FORM — ASYNC SAVE
    ───────────────────────────────────────────── */
    const nspForm = $('.nsp-form');
    if (nspForm) {
        nspForm.addEventListener('submit', async e => {
            e.preventDefault();
            const btn = nspForm.querySelector('.btn-nsp-save');
            const originalText = btn.textContent;

            btn.textContent = 'Guardando…';
            btn.disabled = true;

            try {
                const formData = new FormData(nspForm);
                const res = await fetch(nspForm.action, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrf(),
                        'X-HTTP-Method-Override': 'PATCH',
                        'Accept': 'application/json',
                    },
                    body: formData,
                });

                if (!res.ok) throw new Error();

                btn.textContent = '¡Guardado!';
                setTimeout(() => {
                    btn.textContent = originalText;
                    btn.disabled = false;
                    closeSettings();
                }, 1200);

            } catch {
                btn.textContent = 'Error al guardar';
                btn.disabled = false;
                setTimeout(() => { btn.textContent = originalText; }, 2000);
            }
        });
    }

})();