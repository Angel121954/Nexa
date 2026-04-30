window.Dropdown = (() => {
    function init() {
        document.querySelectorAll('.dropdown-trigger').forEach(trigger => {
            const dropdown = trigger.closest('.relative');
            const content = dropdown.querySelector('.dropdown-content');

            if (!content) return;

            trigger.addEventListener('click', (e) => {
                e.stopPropagation();
                const isOpen = content.style.display !== 'none';
                content.style.display = isOpen ? 'none' : 'block';
            });

            // Close on click outside
            document.addEventListener('click', (e) => {
                if (!dropdown.contains(e.target)) {
                    content.style.display = 'none';
                }
            });

            // Close on click inside content
            content.addEventListener('click', (e) => {
                if (e.target.closest('a, button')) {
                    content.style.display = 'none';
                }
            });
        });
    }

    return { init };
})();
