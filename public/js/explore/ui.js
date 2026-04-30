export function animateLike(btn) {
    btn.style.transform = 'scale(1.35)';
    setTimeout(() => btn.style.transform = '', 300);
}

export function showMatchToast(name) {
    const toast = document.getElementById('match-toast');
    const toastText = document.getElementById('match-toast-text');
    if (!toast || !toastText) return;

    toastText.textContent = `¡Es un match con ${name}! 🎉`;
    toast.classList.add('show');
    setTimeout(() => toast.classList.remove('show'), 4000);
}
