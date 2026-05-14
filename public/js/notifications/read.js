(function (ns) {
    'use strict';

    ns.$$('.notif-mark-form').forEach(form => {
        form.addEventListener('submit', async e => {
            e.preventDefault();
            const item = form.closest('.notif-item');
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

                if (!res.ok) throw new Error('Server error');

                item.classList.remove('unread');
                item.style.removeProperty('background');
                item.querySelector('.notif-mark-btn')?.remove();
                if (item.style.cssText.includes('border-left')) {
                    item.style.borderLeft = '';
                }

                ns.updateBadgeCount(-1);
                if (window.updateNotifBadge) {
                    const currentBadge = ns.$('.notif-count-badge');
                    const next = currentBadge ? parseInt(currentBadge.textContent, 10) || 0 : 0;
                    window.updateNotifBadge(next);
                }

            } catch (err) {
                console.error('[Nexa] mark-as-read failed', err);
            }
        });
    });

    const readAllForm = ns.$('.notif-readall-form');
    if (readAllForm) {
        readAllForm.addEventListener('submit', async e => {
            e.preventDefault();

            try {
                const res = await fetch(readAllForm.action, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': ns.csrf(),
                        'X-HTTP-Method-Override': 'PATCH',
                        'Accept': 'application/json',
                    },
                });

                if (!res.ok) throw new Error('Server error');

                ns.$$('.notif-item.unread').forEach(item => {
                    item.classList.remove('unread');
                    item.querySelector('.notif-mark-btn')?.remove();
                });

                readAllForm.closest('.notif-header-actions')
                    ?.querySelector('.btn-readall')?.remove();

                const badge = ns.$('.notif-count-badge');
                if (badge) badge.remove();

                const tabBadge = ns.$('.tab-count-pink');
                if (tabBadge) {
                    tabBadge.classList.remove('tab-count-pink');
                    tabBadge.textContent = '0';
                }

                if (window.updateNotifBadge) window.updateNotifBadge(0);

            } catch (err) {
                console.error('[Nexa] mark-all-read failed', err);
                readAllForm.submit();
            }
        });
    }
})(window.NexaNotif);
