/**
 * Live search — actualiza el grid de cards via fetch sin recargar la página.
 * Soporta búsqueda por nombre/interés/ciudad con debounce de 400ms.
 */
export function init() {
    const searchInput = document.getElementById('q');
    if (!searchInput) return;

    const grid        = document.getElementById('cards-grid');
    const paginWrap   = document.getElementById('pagination-wrap');
    const loadingEl   = document.getElementById('search-loading');
    const filterForm  = document.getElementById('filter-form');
    const CSRF        = document.querySelector('meta[name="csrf-token"]')?.content;

    let timer;
    let controller; // AbortController para cancelar requests anteriores

    searchInput.addEventListener('input', () => {
        clearTimeout(timer);
        timer = setTimeout(() => doSearch(), 400);
    });

    async function doSearch() {
        // Cancelar request anterior si aún está en vuelo
        if (controller) controller.abort();
        controller = new AbortController();

        const params = new URLSearchParams(new FormData(filterForm));

        // Mostrar indicador de carga
        loadingEl?.style.setProperty('display', 'flex');
        grid?.classList.add('is-loading');

        try {
            const res = await fetch(`${filterForm.action}?${params.toString()}`, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': CSRF,
                    'Accept': 'application/json',
                },
                signal: controller.signal,
            });

            if (!res.ok) throw new Error(`HTTP ${res.status}`);

            const data = await res.json();

            // Actualizar grid de cards
            if (grid) grid.innerHTML = data.cards;

            // Actualizar paginación
            if (paginWrap) paginWrap.innerHTML = data.pagination;

            // Re-inicializar likes en las cards nuevas
            reinitLikes(CSRF);

        } catch (err) {
            if (err.name === 'AbortError') return; // Request cancelado intencionalmente
            console.error('[live-search] Error:', err);
        } finally {
            loadingEl?.style.setProperty('display', 'none');
            grid?.classList.remove('is-loading');
        }
    }
}

/**
 * Re-engancha los event listeners de like en las cards recién renderizadas.
 * Solo se activa sobre botones que aún no tienen listener (data-bound).
 */
function reinitLikes(CSRF) {
    document.querySelectorAll('.card-like-btn:not([data-bound])').forEach(btn => {
        btn.setAttribute('data-bound', '1');
        btn.addEventListener('click', async () => {
            const userId = btn.dataset.user;
            try {
                const res = await fetch(`/explore/like/${userId}`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': CSRF,
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                    },
                });
                const data = await res.json();

                // Actualizar estado visual del botón
                btn.dataset.liked = data.liked ? '1' : '0';
                btn.classList.toggle('liked', data.liked);
                const svg = btn.querySelector('svg');
                if (svg) svg.setAttribute('fill', data.liked ? 'currentColor' : 'none');

                // Mostrar toast de match si corresponde
                if (data.match) {
                    showMatchToast(data.matchName);
                }
            } catch (e) {
                console.error('[like]', e);
            }
        });
    });
}

function showMatchToast(name) {
    const toast = document.getElementById('match-toast');
    const text  = document.getElementById('match-toast-text');
    if (!toast) return;
    if (text) text.textContent = `¡Es un match con ${name}!`;
    toast.classList.add('show');
    setTimeout(() => toast.classList.remove('show'), 4000);
}
