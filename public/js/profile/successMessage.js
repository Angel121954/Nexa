window.SuccessMessage = (() => {
    let message;

    function init() {
        message = document.getElementById('successMessage');

        if (!message) return;

        setTimeout(() => {
            message.style.transition = 'opacity 0.5s ease';
            message.style.opacity = '0';
            setTimeout(() => {
                message.remove();
            }, 500);
        }, 2000);
    }

    return { init };
})();
