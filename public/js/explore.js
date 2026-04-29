/**
 * Explore Page JavaScript
 * Nexa App - Likes, Filters, Premium Modal
 */

document.addEventListener('DOMContentLoaded', () => {
    const CSRF = document.querySelector('meta[name="csrf-token"]')?.content;

    initLikeButtons(CSRF);
    initAdvancedFilters();
    initAgeRangeSelect();
    initPremiumModal();
    initSearchDebounce();
});

/**
 * Initialize like buttons functionality
 */
function initLikeButtons(CSRF) {
    document.querySelectorAll('.card-like-btn').forEach(btn => {
        btn.addEventListener('click', async (e) => {
            e.stopPropagation();
            const userId = btn.dataset.user;
            btn.disabled = true;

            try {
                const res = await fetch(`/explore/like/${userId}`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': CSRF,
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                    },
                });

                if (!res.ok) throw new Error('Server error');
                const data = await res.json();

                const svg = btn.querySelector('svg');

                if (data.liked) {
                    btn.classList.add('liked');
                    btn.dataset.liked = '1';
                    svg.setAttribute('fill', 'currentColor');
                    btn.title = 'Quitar like';
                    animateLike(btn);
                } else {
                    btn.classList.remove('liked');
                    btn.dataset.liked = '0';
                    svg.setAttribute('fill', 'none');
                    btn.title = 'Dar like';
                }

                if (data.match) {
                    showMatchToast(data.matchName);
                }
            } catch (err) {
                console.error(err);
            } finally {
                btn.disabled = false;
            }
        });
    });
}

/**
 * Animate like button
 */
function animateLike(btn) {
    btn.style.transform = 'scale(1.35)';
    setTimeout(() => btn.style.transform = '', 300);
}

/**
 * Show match toast notification
 */
function showMatchToast(name) {
    const toast = document.getElementById('match-toast');
    const toastText = document.getElementById('match-toast-text');
    if (!toast || !toastText) return;

    toastText.textContent = `¡Es un match con ${name}! 🎉`;
    toast.classList.add('show');
    setTimeout(() => toast.classList.remove('show'), 4000);
}

/**
 * Initialize advanced filters panel toggle
 */
function initAdvancedFilters() {
    const toggleBtn = document.getElementById('toggle-adv');
    const panel = document.getElementById('adv-panel');
    if (!toggleBtn || !panel) return;

    toggleBtn.addEventListener('click', () => {
        panel.classList.toggle('open');
    });
}

/**
 * Initialize age range select synchronization
 */
function initAgeRangeSelect() {
    const ageSelect = document.getElementById('age-range-select');
    if (!ageSelect) return;

    ageSelect.addEventListener('change', () => {
        const val = ageSelect.value;
        const [min, max] = val ? val.split('-') : ['', ''];
        const ageMin = document.getElementById('age-min');
        const ageMax = document.getElementById('age-max');
        
        if (ageMin) ageMin.value = min || '';
        if (ageMax) ageMax.value = max || '';
    });
}

/**
 * Initialize premium modal functionality
 */
function initPremiumModal() {
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

/**
 * Initialize search input with debounce
 */
function initSearchDebounce() {
    const searchInput = document.getElementById('q');
    if (!searchInput) return;

    let timer;
    searchInput.addEventListener('input', () => {
        clearTimeout(timer);
        timer = setTimeout(() => {
            document.getElementById('filter-form')?.submit();
        }, 600);
    });
}
