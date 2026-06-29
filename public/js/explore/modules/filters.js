export function initAdvancedFilters() {
    const toggleBtn = document.getElementById('toggle-adv');
    const panel = document.getElementById('adv-panel');
    if (!toggleBtn || !panel) return;

    toggleBtn.addEventListener('click', () => {
        panel.classList.toggle('open');
    });
}

export function initCountryCascade() {
    const countrySelect = document.getElementById('filter-country');
    const deptSelect = document.getElementById('filter-department');
    const citySelect = document.getElementById('filter-city');
    if (!countrySelect || !deptSelect || !citySelect) return;

    const labels = window.exploreRegionLabels || { colombia: 'Departamento', ecuador: 'Provincia' };

    function rebuild() {
        const country = countrySelect.value;
        if (!country || !window.exploreCountriesData?.[country]) {
            deptSelect.innerHTML = '<option value="">Región</option>';
            citySelect.innerHTML = '<option value="">Ciudad</option>';
            return;
        }

        const regions = window.exploreCountriesData[country];
        const label = labels[country] || 'Región';

        // Rebuild department options
        const currentDept = deptSelect.value;
        deptSelect.innerHTML = '<option value="">' + label + '</option>';
        Object.keys(regions).forEach(dept => {
            const opt = document.createElement('option');
            opt.value = dept;
            opt.textContent = dept;
            if (dept === currentDept) opt.selected = true;
            deptSelect.appendChild(opt);
        });

        // Rebuild city options with data attributes
        const currentCity = citySelect.value;
        citySelect.innerHTML = '<option value="">Ciudad</option>';
        Object.entries(regions).forEach(([dept, cities]) => {
            cities.forEach(city => {
                const opt = document.createElement('option');
                opt.value = city;
                opt.textContent = city;
                opt.dataset.country = country;
                opt.dataset.department = dept;
                if (city === currentCity) opt.selected = true;
                citySelect.appendChild(opt);
            });
        });

        filterCities();
    }

    function filterCities() {
        const country = countrySelect.value;
        const dept = deptSelect.value;

        Array.from(citySelect.options).forEach(opt => {
            if (!opt.value) return;
            opt.hidden = opt.dataset.country !== country || opt.dataset.department !== dept;
        });

        if (citySelect.value) {
            const selected = citySelect.options[citySelect.selectedIndex];
            if (selected && selected.hidden) {
                citySelect.value = '';
            }
        }
    }

    countrySelect.addEventListener('change', () => {
        rebuild();
        // Auto-submit filter when country changes
        document.getElementById('filter-form')?.requestSubmit();
    });

    deptSelect.addEventListener('change', filterCities);

    // Initialize on load if country is pre-selected
    if (countrySelect.value) {
        rebuild();
    }
}

export function initAgeRangeSelect() {
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
