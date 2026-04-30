/**
 * Main JavaScript - Funcionalidades comunes
 * Nexa App
 */

document.addEventListener('DOMContentLoaded', () => {
    initHamburgerMenu();
    initDropdowns();
    initDeleteModal();
});

/**
 * Initialize hamburger menu for mobile navigation
 */
function initHamburgerMenu() {
    const hamburger = document.getElementById('nav-hamburger');
    const mobileMenu = document.getElementById('mobile-menu');
    const hamburgerOpen = hamburger?.querySelector('.hamburger-open');
    const hamburgerClose = hamburger?.querySelector('.hamburger-close');

    if (!hamburger || !mobileMenu) return;

    hamburger.addEventListener('click', () => {
        const isOpen = !mobileMenu.classList.contains('hidden');
        mobileMenu.classList.toggle('hidden');

        if (hamburgerOpen && hamburgerClose) {
            hamburgerOpen.classList.toggle('hidden', isOpen);
            hamburgerClose.classList.toggle('hidden', !isOpen);
        }
    });

    // Close menu when clicking a link
    mobileMenu.querySelectorAll('a').forEach(link => {
        link.addEventListener('click', () => {
            mobileMenu.classList.add('hidden');
            if (hamburgerOpen && hamburgerClose) {
                hamburgerOpen.classList.remove('hidden');
                hamburgerClose.classList.add('hidden');
            }
        });
    });
}

/**
 * Initialize dropdowns
 */
function initDropdowns() {
    document.querySelectorAll('.dropdown-trigger').forEach(trigger => {
        const dropdown = trigger.closest('.relative');
        const content = dropdown?.querySelector('.dropdown-content');

        if (!content) return;

        trigger.addEventListener('click', (e) => {
            e.stopPropagation();
            const isOpen = content.style.display !== 'none';
            content.style.display = isOpen ? 'none' : 'block';
        });

        // Close on click outside
        document.addEventListener('click', (e) => {
            if (!dropdown.contains(e.target)) {
                content.style.display = 'none';
            }
        });

        // Close on click inside content
        content.addEventListener('click', (e) => {
            if (e.target.closest('a, button')) {
                content.style.display = 'none';
            }
        });
    });
}

/**
 * Initialize delete account confirmation modal
 */
function initDeleteModal() {
    const deleteBtn = document.getElementById('deleteAccountBtn');
    const cancelBtn = document.getElementById('cancelDeleteBtn');
    const modal = document.getElementById('modal-confirm-user-deletion');

    if (!deleteBtn || !modal) return;

    deleteBtn.addEventListener('click', () => {
        modal.style.display = 'block';
        document.body.classList.add('overflow-hidden');
    });

    if (cancelBtn) {
        cancelBtn.addEventListener('click', () => {
            modal.style.display = 'none';
            document.body.classList.remove('overflow-hidden');
        });
    }

    // Close on click outside modal content (on overlay or backdrop)
    modal.addEventListener('click', (e) => {
        if (e.target === modal || e.target.classList.contains('modal-backdrop')) {
            modal.style.display = 'none';
            document.body.classList.remove('overflow-hidden');
        }
    });
}
