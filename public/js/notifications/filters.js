(function (ns) {
    'use strict';

    ns.$$('.notif-tab').forEach(tab => {
        tab.addEventListener('click', () => {
            ns.$$('.notif-tab').forEach(t => t.classList.remove('active'));
            tab.classList.add('active');
            ns.filterNotifications(tab.dataset.filter);
        });
    });
})(window.NexaNotif);
