/**
 * legal.js — Nexa Legal Pages
 * Handles: tab switching + sticky TOC active state
 */

document.addEventListener('DOMContentLoaded', () => {

    /* ── Tab switching ── */
    const tabs = document.querySelectorAll('.legal-tab');
    const panels = document.querySelectorAll('.legal-panel');

    tabs.forEach(tab => {
        tab.addEventListener('click', () => {
            const target = tab.dataset.panel;

            tabs.forEach(t => {
                t.classList.remove('active');
                t.setAttribute('aria-selected', 'false');
            });
            panels.forEach(p => p.classList.remove('active'));

            tab.classList.add('active');
            tab.setAttribute('aria-selected', 'true');

            const panel = document.getElementById(`panel-${target}`);
            if (panel) panel.classList.add('active');

            // Scroll to top of body on tab change
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });
    });

    /* ── TOC active state on scroll ── */
    const updateToc = () => {
        // Find which panel is active
        const activePanel = document.querySelector('.legal-panel.active');
        if (!activePanel) return;

        const sections = activePanel.querySelectorAll('.legal-section');
        const tocLinks = activePanel.querySelectorAll('.legal-toc-list a');

        if (!sections.length || !tocLinks.length) return;

        let current = null;
        const scrollY = window.scrollY + 120; // offset for sticky nav

        sections.forEach(section => {
            if (section.offsetTop <= scrollY) {
                current = section.id;
            }
        });

        tocLinks.forEach(link => {
            link.classList.remove('toc-active');
            if (link.getAttribute('href') === `#${current}`) {
                link.classList.add('toc-active');
            }
        });
    };

    window.addEventListener('scroll', updateToc, { passive: true });
    updateToc();
});