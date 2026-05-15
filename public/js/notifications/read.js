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

    const notifList = document.getElementById('notif-list');
    if (notifList) {
        notifList.addEventListener('submit', async e => {
            const form = e.target.closest('.notif-delete-form');
            if (!form) return;
            e.preventDefault();

            const item = form.closest('.notif-item');
            const url = form.action;
            const btn = form.querySelector('.notif-delete-btn');
            const originalHtml = btn.innerHTML;

            btn.innerHTML = '<span class="notif-spinner"></span>';
            btn.disabled = true;

            try {
                const res = await fetch(url, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': ns.csrf(),
                        'X-HTTP-Method-Override': 'DELETE',
                        'Accept': 'application/json',
                    },
                });

                if (!res.ok) throw new Error('Server error');

                const json = await res.json();

                const label = item.previousElementSibling;
                const isLabel = label && label.classList.contains('notif-group-label');

                item.style.transition = 'opacity .25s, transform .25s';
                item.style.opacity = '0';
                item.style.transform = 'translateX(-20px)';

                setTimeout(() => {
                    item.remove();

                    if (isLabel) {
                        const next = label.nextElementSibling;
                        if (!next || next.classList.contains('notif-group-label') || next.id === 'notif-empty') {
                            label.remove();
                        }
                    }

                    const remaining = ns.$$('.notif-item:not([hidden])').length;
                    if (remaining === 0) {
                        ns.$$('.notif-group-label').forEach(el => el.remove());
                        const empty = ns.$('#notif-empty');
                        if (empty) {
                            empty.hidden = false;
                        } else {
                            notifList.insertAdjacentHTML('beforeend', ns.buildEmptyHtml());
                        }
                    }
                }, 260);

                if (json.unread_count !== undefined && window.updateNotifBadge) {
                    window.updateNotifBadge(json.unread_count);
                }

                if (json.unread_count === 0) {
                    ns.$('.notif-count-badge')?.remove();
                    ns.$('.tab-count-pink')?.remove();
                    ns.$('.notif-readall-form')?.remove();
                    const activeTab = ns.$('.notif-tab.active');
                    if (activeTab) activeTab.querySelector('.tab-count')?.remove();
                }

            } catch (err) {
                btn.innerHTML = originalHtml;
                btn.disabled = false;
                console.error('[Nexa] delete notification failed', err);
            }
        });
    }

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

                ns.$('.notif-count-badge')?.remove();
                ns.$('.tab-count-pink')?.remove();
                ns.$('.notif-readall-form')?.remove();
                const activeTab = ns.$('.notif-tab.active');
                if (activeTab) activeTab.querySelector('.tab-count')?.remove();

                if (window.updateNotifBadge) window.updateNotifBadge(0);

            } catch (err) {
                console.error('[Nexa] mark-all-read failed', err);
                readAllForm.submit();
            }
        });
    }
})(window.NexaNotif);
