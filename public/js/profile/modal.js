/**
 * Modal Module - Profile Edit
 */
window.ProfileModal = (() => {
    const modal = document.getElementById('profileModal');
    const cancelBtn = document.getElementById('cancelBtn');
    const closeBtn = document.getElementById('closeModalBtn');

    function open() {
        if (!modal) return;
        modal.classList.add('active');
        document.body.style.overflow = 'hidden';
    }

    function close() {
        if (!modal) return;
        modal.classList.remove('active');
        document.body.style.overflow = '';
    }

    function init() {
        if (!modal) return;

        // Expose global functions for onclick attributes
        window.openModal = open;
        window.closeModal = close;

        // Close on click outside modal content (on overlay or backdrop)
        modal.addEventListener('click', (e) => {
            if (e.target === modal || e.target.classList.contains('modal-backdrop')) {
                close();
            }
        });

        // Close on cancel button
        if (cancelBtn) {
            cancelBtn.addEventListener('click', close);
        }

        // Close on X button
        if (closeBtn) {
            closeBtn.addEventListener('click', close);
        }

        // Close on Escape key
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && modal.classList.contains('active')) {
                close();
            }
        });
    }

    return { init, open, close };
})();
