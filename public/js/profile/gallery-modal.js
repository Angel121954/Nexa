(function () {
    'use strict';

    let modal = null;
    let images = [];
    let currentIndex = 0;
    let startX = 0;
    let isSwiping = false;

    function buildModal() {
        const div = document.createElement('div');
        div.className = 'photo-modal';
        div.innerHTML = `
            <div class="photo-modal-backdrop"></div>
            <button class="photo-modal-close" type="button" aria-label="Cerrar">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round">
                    <line x1="18" y1="6" x2="6" y2="18" />
                    <line x1="6" y1="6" x2="18" y2="18" />
                </svg>
            </button>
            <button class="photo-modal-nav photo-modal-prev" type="button" aria-label="Anterior">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="15 18 9 12 15 6" />
                </svg>
            </button>
            <div class="photo-modal-container">
                <img class="photo-modal-img" src="" alt="Foto">
            </div>
            <button class="photo-modal-nav photo-modal-next" type="button" aria-label="Siguiente">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="9 18 15 12 9 6" />
                </svg>
            </button>
            <span class="photo-modal-counter"></span>
        `;
        return div;
    }

    function update() {
        const img = modal.querySelector('.photo-modal-img');
        const counter = modal.querySelector('.photo-modal-counter');
        const prev = modal.querySelector('.photo-modal-prev');
        const next = modal.querySelector('.photo-modal-next');

        if (!images.length) return close();

        const item = images[currentIndex];
        img.src = item.src;

        counter.textContent = images.length > 1
            ? (currentIndex + 1) + ' / ' + images.length
            : '';

        prev.disabled = currentIndex === 0;
        next.disabled = currentIndex === images.length - 1;
    }

    function open(index) {
        if (!modal) {
            modal = buildModal();
            document.body.appendChild(modal);

            modal.querySelector('.photo-modal-backdrop').addEventListener('click', close);
            modal.querySelector('.photo-modal-close').addEventListener('click', close);
            modal.querySelector('.photo-modal-prev').addEventListener('click', () => navigate(-1));
            modal.querySelector('.photo-modal-next').addEventListener('click', () => navigate(1));

            document.addEventListener('keydown', onKeyDown);

            modal.addEventListener('touchstart', onTouchStart, { passive: true });
            modal.addEventListener('touchend', onTouchEnd, { passive: true });
        }

        currentIndex = index;
        update();
        requestAnimationFrame(() => modal.classList.add('open'));
        document.body.style.overflow = 'hidden';
    }

    function close() {
        if (!modal) return;
        modal.classList.remove('open');
        document.body.style.overflow = '';
        const img = modal.querySelector('.photo-modal-img');
        img.src = '';
    }

    function navigate(dir) {
        const next = currentIndex + dir;
        if (next < 0 || next >= images.length) return;
        currentIndex = next;
        update();
    }

    function onKeyDown(e) {
        if (!modal || !modal.classList.contains('open')) return;
        if (e.key === 'Escape') close();
        if (e.key === 'ArrowLeft') navigate(-1);
        if (e.key === 'ArrowRight') navigate(1);
    }

    function onTouchStart(e) {
        if (e.touches.length === 1) {
            startX = e.touches[0].clientX;
            isSwiping = true;
        }
    }

    function onTouchEnd(e) {
        if (!isSwiping) return;
        isSwiping = false;
        const endX = e.changedTouches[0].clientX;
        const diff = startX - endX;
        if (Math.abs(diff) > 50) {
            navigate(diff > 0 ? 1 : -1);
        }
    }

    document.addEventListener('click', function (e) {
        const item = e.target.closest('.gallery-item');
        if (!item) return;
        if (e.target.closest('.gallery-item-delete') || e.target.closest('.gallery-item-actions')) return;

        const grid = item.closest('.gallery-grid');
        if (!grid) return;

        const allItems = Array.from(grid.querySelectorAll('.gallery-item'));
        const idx = allItems.indexOf(item);
        if (idx === -1) return;

        images = allItems.map(function (el) {
            const img = el.querySelector('img');
            return { src: img ? img.src : '' };
        });

        open(idx);
    });
})();
