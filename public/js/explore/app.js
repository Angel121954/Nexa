import { init as initLikes } from './modules/likes.js';
import { initAdvancedFilters, initAgeRangeSelect } from './modules/filters.js';
import { init as initPremium } from './modules/premium.js';
import { init as initSearch } from './modules/search.js';

document.addEventListener('DOMContentLoaded', () => {
    const CSRF = document.querySelector('meta[name="csrf-token"]')?.content;

    initLikes(CSRF);
    initAdvancedFilters();
    initAgeRangeSelect();
    initPremium();
    initSearch();
});
