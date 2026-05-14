/**
 * Auth JavaScript
 * Nexa App - Registration, Login
 */

document.addEventListener('DOMContentLoaded', () => {
    initPasswordToggle();
});

/**
 * Toggle password visibility (register.blade.php)
 */
function initPasswordToggle() {
    // This function can be called from onclick in the view
    window.togglePwd = function(inputId, iconId) {
        const input = document.getElementById(inputId);
        const icon = document.getElementById(iconId);
        if (!input || !icon) return;

        const show = input.type === 'password';
        input.type = show ? 'text' : 'password';
        icon.innerHTML = show ?
            `<path d="M13 10.5A5 5 0 018.5 6M3 3l10 10M1 8s2.5-5 7-5c1.2 0 2.3.3 3.3.8" stroke-linecap="round"/><line x1="2" y1="2" x2="14" y2="14" stroke-linecap="round"/>` :
            `<path d="M1 8s2.5-5 7-5 7 5 7 5-2.5 5-7 5-7-5-7-5z"/><circle cx="8" cy="8" r="2"/>`;
    };
}
