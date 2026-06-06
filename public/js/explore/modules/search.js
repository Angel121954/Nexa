export function init(CSRF) {
    const form = document.getElementById('filter-form');
    const searchInput = document.getElementById('q');
    const searchBtn = document.getElementById('search-btn');
    const container = document.getElementById('cards-container');
    const deptSelect = document.getElementById('filter-department');
    const citySelect = document.getElementById('filter-city');
    const genderSelect = document.getElementById('filter-gender');
    const ageSelect = document.getElementById('age-range-select');
    const applyBtn = document.getElementById('apply-filters');

    if (!form || !container) return;

    async function fetchResults(page) {
        const params = new URLSearchParams(new FormData(form));
        if (page) params.set('page', page);

        container.style.opacity = '0.3';
        container.style.transition = 'opacity .2s';
        container.style.pointerEvents = 'none';

        try {
            const res = await fetch(`${form.action}?${params}`, {
                headers: { Accept: 'application/json' },
                credentials: 'same-origin',
            });

            if (!res.ok) throw new Error('Error');

            const json = await res.json();
            container.innerHTML = json.html;
            container.style.opacity = '1';
            container.style.pointerEvents = '';

            document.dispatchEvent(new CustomEvent('explore-cards-rendered'));
        } catch {
            container.style.opacity = '1';
            container.style.pointerEvents = '';
        }
    }

    searchBtn?.addEventListener('click', () => fetchResults());

    searchInput?.addEventListener('keydown', (e) => {
        if (e.key === 'Enter') {
            e.preventDefault();
            fetchResults();
        }
    });

    // Cascade: department → city options
    const allCityOptions = citySelect ? Array.from(citySelect.options) : [];

    function filterCities() {
        const selectedDept = deptSelect?.value;
        allCityOptions.forEach(opt => {
            if (!opt.value) return;
            opt.hidden = selectedDept ? opt.dataset.department !== selectedDept : false;
        });
        if (citySelect?.value) {
            const selected = citySelect.options[citySelect.selectedIndex];
            if (selected?.hidden) citySelect.value = '';
        }
    }

    deptSelect?.addEventListener('change', () => {
        filterCities();
        fetchResults();
    });

    if (deptSelect?.value) filterCities();

    citySelect?.addEventListener('change', () => fetchResults());
    genderSelect?.addEventListener('change', () => fetchResults());
    ageSelect?.addEventListener('change', () => {
        const val = ageSelect.value;
        const [min, max] = val ? val.split('-') : ['', ''];
        const ageMin = document.getElementById('age-min');
        const ageMax = document.getElementById('age-max');
        if (ageMin) ageMin.value = min || '';
        if (ageMax) ageMax.value = max || '';
        fetchResults();
    });

    applyBtn?.addEventListener('click', () => fetchResults());

    container.addEventListener('click', (e) => {
        const link = e.target.closest('.pagination-wrap a');
        if (!link) return;
        e.preventDefault();

        const url = new URL(link.href);
        fetchResults(url.searchParams.get('page'));
    });
}
