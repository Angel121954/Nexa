/**
 * Onboarding JavaScript
 * Nexa App - Basic info, Preferences, Photos
 */

document.addEventListener('DOMContentLoaded', () => {
    initBirthDateCalculator();
    initCitySelects();
    initInterestTags();
    initPreferenceOptions();
    initGenderPrefAutoFill();
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
 * Preview & accumulate gallery images (photos.blade.php)
 * Acumula archivos seleccionados en múltiples aperturas y los envía
 * todos vía fetch al enviar el formulario.
 */
function initGalleryPreview() {
    const accumulated = [];

    window.previewGallery = function(input) {
        const grid = document.getElementById('gallery-grid');
        if (!grid) return;

        // Limpiar previsualizaciones anteriores
        grid.querySelectorAll('.gallery-item[data-preview]').forEach(el => el.remove());

        // Acumular nuevos archivos
        Array.from(input.files).forEach(file => {
            accumulated.push(file);
        });

        // Mostrar preview de TODOS los acumulados
        accumulated.forEach((file, i) => {
            const reader = new FileReader();
            reader.onload = e => {
                const div = document.createElement('div');
                div.className = 'gallery-item';
                div.setAttribute('data-preview', '1');
                div.innerHTML = `<img src="${e.target.result}" alt="Preview">`;
                grid.appendChild(div);
            };
            reader.readAsDataURL(file);
        });

        // Resetear el input para permitir seleccionar el mismo archivo de nuevo
        input.value = '';
    };

    // Interceptar el envío para incluir todos los archivos acumulados
    const form = document.querySelector('form[action*="photos"]');
    if (form) {
        let submitting = false;
        form.addEventListener('submit', async function(e) {
            if (submitting) return;
            submitting = true;
            e.preventDefault();

            const btn = form.querySelector('button[type="submit"]');
            if (btn) {
                btn.disabled = true;
                btn.textContent = 'Subiendo...';
                btn.style.opacity = '0.6';
                btn.style.pointerEvents = 'none';
            }

            const formData = new FormData(form);
            formData.delete('gallery[]');

            accumulated.forEach(file => {
                formData.append('gallery[]', file);
            });

            try {
                const res = await fetch(form.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'Accept': 'text/html,application/xhtml+xml',
                    },
                });

                if (res.redirected) {
                    window.location.href = res.url;
                } else {
                    document.open();
                    document.write(await res.text());
                    document.close();
                }
            } catch (err) {
                console.error('Error al enviar formulario:', err);
                alert('Error al enviar el formulario. Intenta de nuevo.');
                if (btn) {
                    btn.disabled = false;
                    btn.textContent = 'Continuar';
                    btn.style.opacity = '1';
                    btn.style.pointerEvents = 'auto';
                }
                submitting = false;
            }
        });
    }
}

/**
 * Toggle gender preference chips (preferences.blade.php)
 */
function initGenderPrefAutoFill() {
    const section = document.getElementById('gender-pref-section');
    if (!section) return;

    const userGender = section.dataset.userGender;

    window.toggleGenderPref = function(label, value) {
        const input = document.getElementById('gp-input-' + value);
        if (!input) return;
        input.checked = !input.checked;
        label.classList.toggle('selected', input.checked);
    };

    if (userGender === 'male') {
        autoCheckGenderPref('female');
    } else if (userGender === 'female') {
        autoCheckGenderPref('male');
    }
}

function autoCheckGenderPref(value) {
    const input = document.getElementById('gp-input-' + value);
    const label = document.getElementById('gp-opt-' + value);
    if (!input || !label) return;
    input.checked = true;
    label.classList.add('selected');
}

/**
 * Cascading city/department selects (basic.blade.php)
 */
function initCitySelects() {
    const deptSelect = document.getElementById('department');
    const citySelect = document.getElementById('city');
    if (!deptSelect || !citySelect) return;

    const allOptions = Array.from(citySelect.options);

    function filterCities() {
        const selectedDept = deptSelect.value;

        allOptions.forEach(opt => {
            if (!opt.value) return;
            opt.hidden = opt.dataset.department !== selectedDept;
        });

        if (citySelect.value) {
            const selected = citySelect.options[citySelect.selectedIndex];
            if (selected && selected.hidden) {
                citySelect.value = '';
            }
        }
    }

    deptSelect.addEventListener('change', filterCities);

    if (deptSelect.value) {
        filterCities();
    }
}
