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

            ns.updateBadgeCount(1);
            if (window.updateNotifBadge) window.updateNotifBadge(e.unread_count);
        });
})(window.NexaNotif);
