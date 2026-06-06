// NexaMessages — Entry point
document.addEventListener('DOMContentLoaded', () => {

    // Close message action menus on outside click
    document.addEventListener('click', (e) => {
        if (!e.target.closest('.msg-bubble-actions-btn') && !e.target.closest('.msg-bubble-actions-menu')) {
            MessageEditUI.closeAllMenus();
        }
    });

    // Init
    state.init();
    Events.bindConversationItems();
    Events.setupTabs();
    Events.setupSearch();
    Events.setupBackButton();
    Events.setupMessageInput();
    BlockUI.init();
    ReportUI.init();
    DeleteUI.init();
    WS.subscribeToUserChannel();
    WS.subscribeToMatchesChannel();

    setInterval(updateAllConversationTimes, 1000);
    updateAllConversationTimes();
});
