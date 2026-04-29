/**
 * Main JavaScript - Funcionalidades comunes
 * Nexa App
 */

document.addEventListener('DOMContentLoaded', () => {
    initHamburgerMenu();
});

/**
 * Initialize hamburger menu for mobile navigation
 */
function initHamburgerMenu() {
    const hamburger = document.getElementById('nav-hamburger');
    const navLinks = document.getElementById('nav-links');
    const navOverlay = document.getElementById('nav-overlay');

    if (!hamburger || !navLinks) return;

    hamburger.addEventListener('click', () => {
        navLinks.classList.toggle('open');
        if (navOverlay) {
            navOverlay.classList.toggle('open');
        }
    });

    // Close menu when clicking a link
    navLinks.querySelectorAll('.nav-link').forEach(link => {
        link.addEventListener('click', () => {
            navLinks.classList.remove('open');
            if (navOverlay) {
                navOverlay.classList.remove('open');
            }
        });
    });

    // Close menu when clicking overlay
    if (navOverlay) {
        navOverlay.addEventListener('click', () => {
            navLinks.classList.remove('open');
            navOverlay.classList.remove('open');
        });
    }

    // Close menu when clicking outside
    document.addEventListener('click', (e) => {
        if (!hamburger.contains(e.target) && !navLinks.contains(e.target)) {
            navLinks.classList.remove('open');
            if (navOverlay) {
                navOverlay.classList.remove('open');
            }
        }
    });
}
