export function initAdvancedFilters() {
    const toggleBtn = document.getElementById('toggle-adv');
    const panel = document.getElementById('adv-panel');
    if (!toggleBtn || !panel) return;

    toggleBtn.addEventListener('click', () => {
        panel.classList.toggle('open');
    });
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
