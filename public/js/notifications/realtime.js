(function (ns) {
    'use strict';

    const userId = document.querySelector('meta[name="user-id"]')?.content;

    if (!window.Echo || !userId) return;

    window.Echo.private('user.' + userId)
        .listen('.NotificationCreated', (e) => {
            const notifList = document.getElementById('notif-list');
            if (!notifList) return;

            const html = ns.buildNotifHtml(e);

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
                                    'X-CSRF-TOKEN': ns.csrf(),
                                    'X-HTTP-Method-Override': 'PATCH',
                                    'Accept': 'application/json',
                                },
                            });
                            if (!res.ok) throw new Error();
                            newItem.classList.remove('unread');
                            newItem.querySelector('.notif-mark-btn')?.remove();
                            ns.updateBadgeCount(-1);
                        } catch (err) {
                            console.error('[Nexa] mark-as-read failed', err);
                        }
                    });
                }
            }

            const currentFilter = ns.$('.notif-tab.active')?.dataset?.filter || 'all';
            if (newItem) {
                const type = newItem.dataset.filter;
                const visible = currentFilter === 'all' || currentFilter === 'unread' || type === currentFilter;
                newItem.hidden = !visible;
            }

            if (window.updateNotifBadge) window.updateNotifBadge(e.unread_count);
            const pageBadge = ns.$('.notif-count-badge');
            if (pageBadge) {
                pageBadge.textContent = e.unread_count;
            } else if (e.unread_count > 0) {
                const headerLeft = ns.$('.notif-header-left');
                if (headerLeft) {
                    const span = document.createElement('span');
                    span.className = 'notif-count-badge';
                    span.textContent = e.unread_count;
                    headerLeft.appendChild(span);
                }
            }
            if (!ns.$('.notif-readall-form')) {
                const headerActions = ns.$('.notif-header-actions');
                if (headerActions) {
                    headerActions.insertAdjacentHTML('afterbegin', ns.buildReadAllFormHtml());
                    const newForm = ns.$('.notif-readall-form');
                    if (newForm) {
                        newForm.addEventListener('submit', async function (ev) {
                            ev.preventDefault();
                            try {
                                const res = await fetch(newForm.action, {
                                    method: 'POST',
                                    headers: {
                                        'X-CSRF-TOKEN': ns.csrf(),
                                        'X-HTTP-Method-Override': 'PATCH',
                                        'Accept': 'application/json',
                                    },
                                });
                                if (!res.ok) throw new Error();
                                ns.$$('.notif-item.unread').forEach(item => {
                                    item.classList.remove('unread');
                                    item.querySelector('.notif-mark-btn')?.remove();
                                });
                                ns.$('.notif-count-badge')?.remove();
                                ns.$('.tab-count-pink')?.remove();
                                ns.$('.notif-readall-form')?.remove();
                                if (window.updateNotifBadge) window.updateNotifBadge(0);
                            } catch (err) {
                                console.error('[Nexa] mark-all-read failed', err);
                                newForm.submit();
                            }
                        });
                    }
                }
            }

            const pinkTab = ns.$('.tab-count-pink');
            if (pinkTab) {
                pinkTab.textContent = e.unread_count;
            } else if (e.unread_count > 0) {
                const unreadTab = ns.$('.notif-tab[data-filter="unread"]');
                if (unreadTab) {
                    const span = document.createElement('span');
                    span.className = 'tab-count tab-count-pink';
                    span.textContent = e.unread_count;
                    unreadTab.appendChild(span);
                }
            }
        });
})(window.NexaNotif);
