export function init() {
    const overlay = document.getElementById('premium-modal');
    const openBtn = document.getElementById('open-premium-modal');
    const closeBtn = document.getElementById('close-premium-modal');
    if (!overlay) return;

    function openModal() {
        overlay.hidden = false;
        document.body.style.overflow = 'hidden';
    }

    function closeModal() {
        overlay.hidden = true;
        document.body.style.overflow = '';
    }

    openBtn?.addEventListener('click', openModal);
    closeBtn?.addEventListener('click', closeModal);

    overlay.addEventListener('click', (e) => {
        if (e.target === overlay) closeModal();
    });

    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') closeModal();
    });

    // Billing toggle
    document.querySelectorAll('.pm-toggle-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            document.querySelectorAll('.pm-toggle-btn').forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
            const period = btn.dataset.period;
            document.querySelectorAll('.pm-price').forEach(el => {
                el.textContent = el.dataset[period];
            });
        });
    });
}
