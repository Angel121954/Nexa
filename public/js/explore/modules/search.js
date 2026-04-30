export function init() {
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
