(function (ns) {
    'use strict';

    ns.updateTimes();
    setInterval(ns.updateTimes, 30000);
})(window.NexaNotif);
