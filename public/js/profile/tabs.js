/**
 * Tabs Module - Profile Edit Form
 */
window.ProfileTabs = (() => {
    const tabButtons = document.querySelectorAll('.profile-tab-btn');
    const tabContents = document.querySelectorAll('.tab-content');
    const formActions = document.querySelector('.form-actions');

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

        // Hide form actions (cancel/save) on seguridad tab
        if (formActions) {
            formActions.style.display = tabName === 'twofactor' ? 'none' : '';
        }

        // Update buttons state
        tabButtons.forEach(btn => {
            if (btn.dataset.tab === tabName) {
                btn.classList.add('active');
            } else {
                btn.classList.remove('active');
            }
        });

        // Load 2FA state when switching to seguridad tab
        if (tabName === 'twofactor' && window.TwoFactorAuth) {
            window.TwoFactorAuth.init();
        }
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
