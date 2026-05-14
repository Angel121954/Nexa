(function (ns) {
    'use strict';

    const settingsBtn = ns.$('#notif-settings-btn');
    const settingsPanel = ns.$('#notif-settings-panel');
    const settingsOverlay = ns.$('.notif-settings-overlay');
    const nspClose = ns.$('#nsp-close');

    function openSettings() {
        settingsPanel?.classList.add('open');
        settingsOverlay?.classList.add('open');
        settingsPanel?.setAttribute('aria-hidden', 'false');
        document.body.style.overflow = 'hidden';
    }

    function closeSettings() {
        settingsPanel?.classList.remove('open');
        settingsOverlay?.classList.remove('open');
        settingsPanel?.setAttribute('aria-hidden', 'true');
        document.body.style.overflow = '';
    }

    settingsBtn?.addEventListener('click', openSettings);
    nspClose?.addEventListener('click', closeSettings);
    settingsOverlay?.addEventListener('click', closeSettings);

    document.addEventListener('keydown', e => {
        if (e.key === 'Escape') closeSettings();
    });

    const nspForm = ns.$('.nsp-form');
    if (nspForm) {
        nspForm.addEventListener('submit', async e => {
            e.preventDefault();
            const btn = nspForm.querySelector('.btn-nsp-save');
            const originalText = btn.textContent;

            btn.textContent = 'Guardando…';
            btn.disabled = true;

            try {
                const formData = new FormData(nspForm);
                const res = await fetch(nspForm.action, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': ns.csrf(),
                        'X-HTTP-Method-Override': 'PATCH',
                        'Accept': 'application/json',
                    },
                    body: formData,
                });

                if (!res.ok) throw new Error();

                btn.textContent = '¡Guardado!';
                setTimeout(() => {
                    btn.textContent = originalText;
                    btn.disabled = false;
                    closeSettings();
                }, 1200);

            } catch {
                btn.textContent = 'Error al guardar';
                btn.disabled = false;
                setTimeout(() => { btn.textContent = originalText; }, 2000);
            }
        });
    }
})(window.NexaNotif);
