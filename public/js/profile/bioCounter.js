window.BioCounter = (() => {
    let bioTextarea, bioCounter;

    function updateCounter() {
        if (bioTextarea && bioCounter) {
            bioCounter.textContent = `${bioTextarea.value.length}/500`;
        }
    }

    function init() {
        bioTextarea = document.getElementById('bio');
        bioCounter = document.getElementById('bioCounter');

        if (!bioTextarea || !bioCounter) return;

        bioTextarea.addEventListener('input', updateCounter);
        updateCounter();
    }

    return { init, updateCounter };
})();
