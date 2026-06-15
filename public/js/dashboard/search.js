document.addEventListener('DOMContentLoaded', () => {
    const input = document.getElementById('dash-user-search');
    if (!input) return;

    input.addEventListener('input', () => {
        const q = input.value.toLowerCase().trim();
        const rows = document.querySelectorAll('#dash-users-table tbody tr');

        rows.forEach(row => {
            const name = row.dataset.name ?? '';
            const email = row.dataset.email ?? '';
            row.style.display = (name.includes(q) || email.includes(q)) ? '' : 'none';
        });
    });
});
