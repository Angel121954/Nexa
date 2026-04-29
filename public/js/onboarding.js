/**
 * Onboarding JavaScript
 * Nexa App - Basic info, Preferences, Photos
 */

document.addEventListener('DOMContentLoaded', () => {
    initBirthDateCalculator();
    initInterestTags();
    initPreferenceOptions();
    initAvatarPreview();
    initGalleryPreview();
});

/**
 * Calculate age from birth date (basic.blade.php)
 */
function initBirthDateCalculator() {
    const birthDateInput = document.getElementById('birth_date');
    if (!birthDateInput) return;

    birthDateInput.addEventListener('change', function() {
        const birth = new Date(this.value);
        const today = new Date();
        let age = today.getFullYear() - birth.getFullYear();
        const m = today.getMonth() - birth.getMonth();
        if (m < 0 || (m === 0 && today.getDate() < birth.getDate())) age--;
        const ageDisplay = document.getElementById('age-display');
        if (ageDisplay) {
            ageDisplay.value = isNaN(age) || age < 0 ? '' : age;
        }
    });
}

/**
 * Toggle interest tag selection (preferences.blade.php)
 */
function initInterestTags() {
    // This function can be called from onclick in the view
    window.toggleTag = function(checkbox) {
        checkbox.closest('.interest-tag')?.classList.toggle('selected', checkbox.checked);
    };
}

/**
 * Toggle preference options (preferences.blade.php)
 */
function initPreferenceOptions() {
    // This function can be called from onclick in the view
    window.toggleOption = function(label, value) {
        const input = document.getElementById('input-' + value);
        const check = document.getElementById('check-' + value);
        const checkmark = document.getElementById('checkmark-' + value);
        if (!input || !check || !checkmark) return;

        const selected = !input.checked;
        input.checked = selected;
        label.classList.toggle('selected', selected);
        check.style.background = selected ? 'var(--pink)' : '';
        check.style.borderColor = selected ? 'var(--pink)' : '';
        checkmark.style.display = selected ? 'block' : 'none';
    };
}

/**
 * Preview avatar image (photos.blade.php)
 */
function initAvatarPreview() {
    const avatarInput = document.getElementById('avatar');
    if (!avatarInput) return;

    avatarInput.addEventListener('change', function() {
        const file = this.files[0];
        if (!file) return;
        
        const reader = new FileReader();
        reader.onload = e => {
            const img = document.getElementById('avatar-img');
            const placeholder = document.getElementById('avatar-placeholder');
            if (img) {
                img.src = e.target.result;
                img.style.display = 'block';
            }
            if (placeholder) placeholder.style.display = 'none';
        };
        reader.readAsDataURL(file);
    });
}

/**
 * Preview gallery images (photos.blade.php)
 */
function initGalleryPreview() {
    // This function can be called from onchange in the view
    window.previewGallery = function(input) {
        const grid = document.getElementById('gallery-grid');
        if (!grid) return;

        Array.from(input.files).forEach(file => {
            const reader = new FileReader();
            reader.onload = e => {
                const div = document.createElement('div');
                div.className = 'gallery-item';
                div.innerHTML = `<img src="${e.target.result}" alt="Preview">`;
                grid.appendChild(div);
            };
            reader.readAsDataURL(file);
        });
    };
}
