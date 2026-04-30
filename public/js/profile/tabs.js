/**
 * Tabs Module - Profile Edit Form
 */
window.ProfileTabs = (() => {
    const tabButtons = document.querySelectorAll('.profile-tab-btn');
    const tabContents = document.querySelectorAll('.tab-content');

    function switchTab(tabName) {
        // Hide all contents
        tabContents.forEach(content => {
            content.classList.remove('active');
        });

        // Show selected content
        const targetContent = document.getElementById('tab-' + tabName);
        if (targetContent) {
            targetContent.classList.add('active');
        }

        // Update buttons state
        tabButtons.forEach(btn => {
            if (btn.dataset.tab === tabName) {
                btn.classList.add('active');
            } else {
                btn.classList.remove('active');
            }
        });
    }

    function init() {
        if (!tabButtons.length) return;

        tabButtons.forEach(btn => {
            btn.addEventListener('click', () => {
                switchTab(btn.dataset.tab);
            });
        });

        // Initialize first tab
        switchTab('info');
    }

    return { init, switchTab };
})();
