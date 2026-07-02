(function () {
    'use strict';

    const badge = document.getElementById('nav-notif-badge');
    const bottomBadge = document.getElementById('bottom-nav-notif-badge');

    const userIdMeta = document.querySelector('meta[name="user-id"]');
    if (!userIdMeta) return;
    const currentUserId = userIdMeta.content;

    function updateBadge(count) {
        const text = count > 99 ? '99+' : count;
        const show = count > 0;
        if (badge) {
            badge.style.display = show ? '' : 'none';
            badge.textContent = text;
        }
        if (bottomBadge) {
            bottomBadge.style.display = show ? '' : 'none';
            bottomBadge.textContent = text;
        }
    }

    function fetchUnreadCount() {
        fetch('/notifications/unread-count', {
            headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content ?? '' },
            credentials: 'same-origin',
        })
            .then(r => r.json())
            .then(data => updateBadge(data.count))
            .catch(() => {});
    }

    fetchUnreadCount();

    setInterval(fetchUnreadCount, 30000);

    if (window.Echo) {
        window.Echo.private(`user.${currentUserId}`)
            .listen('.NotificationCreated', (e) => {
                updateBadge(e.unread_count);
            });
    }

    window.updateNotifBadge = updateBadge;
})();
