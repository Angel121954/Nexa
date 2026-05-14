(function () {
    'use strict';

    const badge = document.getElementById('nav-notif-badge');
    if (!badge) return;

    const userIdMeta = document.querySelector('meta[name="user-id"]');
    if (!userIdMeta) return;
    const currentUserId = userIdMeta.content;

    function updateBadge(count) {
        if (count > 0) {
            badge.textContent = count > 99 ? '99+' : count;
            badge.style.display = '';
        } else {
            badge.style.display = 'none';
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
