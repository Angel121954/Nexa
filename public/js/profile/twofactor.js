window.TwoFactorAuth = (() => {
    const container = document.getElementById('twofactor-app');
    const statusText = document.getElementById('twofactor-status-text');

    let state = { enabled: false, confirmed: false };

    function loading(msg) {
        if (!container) return;
        container.innerHTML = `<div class="text-center py-8">
            <div class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-pink-100 mb-4">
                <svg class="w-6 h-6 text-pink-500 animate-spin" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </div>
            <p class="text-gray-500 text-sm">${msg}</p>
        </div>`;
    }

    function render() {
        if (!container) return;

        if (!state.enabled) {
            container.innerHTML = `
                <div class="text-center pt-4 pb-2 px-1">
                    <div class="inline-flex items-center justify-center w-14 h-14 rounded-full bg-pink-50 mb-4">
                        <svg class="w-7 h-7 text-pink-400" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                            <path d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </div>
                    <h4 class="text-base sm:text-lg font-semibold text-gray-800 mb-1">Autenticación en dos pasos</h4>
                    <p class="text-sm text-gray-500 px-2">Protege tu cuenta con una capa adicional de seguridad.</p>
                    <button type="button" class="btn-save mt-5" data-action="setup" style="display:inline-flex">
                        Activar 2FA
                    </button>
                </div>
            `;
            return;
        }

        if (!state.confirmed) {
            container.innerHTML = `
                <div class="pt-2 pb-1">
                    <div class="text-center mb-4">
                        <h4 class="text-base font-semibold text-gray-800 mb-1">Escanea el código QR</h4>
                        <p class="text-sm text-gray-500">Usa tu aplicación autenticadora (Google Authenticator, Authy, etc.)</p>
                    </div>
                    <div class="flex justify-center mb-4 bg-gray-50 rounded-xl p-2 sm:p-4 overflow-hidden qr-wrapper">
                        ${state.qr_code || ''}
                    </div>
                    <div class="text-center mb-4 px-1">
                        <p class="text-xs text-gray-400 mb-1">O ingresa esta clave manualmente:</p>
                        <p class="text-xs sm:text-sm font-mono text-gray-600 bg-gray-50 rounded-lg px-3 py-2 select-all break-all border border-gray-200">${state.secret || ''}</p>
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="twofactor-code">Código de verificación</label>
                        <input type="text" id="twofactor-code" maxlength="6" inputmode="numeric"
                            class="form-input text-center text-lg tracking-[0.25em]"
                            placeholder="000000">
                    </div>
                    <div id="twofactor-error" class="text-red-500 text-xs text-center mb-3 hidden"></div>
                    <button type="button" class="btn-save" data-action="confirm" style="display:inline-flex;width:100%;justify-content:center">
                        Confirmar
                    </button>
                    <div class="text-center mt-3">
                        <button type="button" class="btn-cancel" data-action="cancel-setup" style="display:inline-flex">
                            Cancelar
                        </button>
                    </div>
                </div>
            `;
            return;
        }

        container.innerHTML = `
            <div class="text-center pt-5 pb-4 px-1">
                <div class="w-14 h-14 rounded-full bg-green-100 flex items-center justify-center mx-auto mb-4">
                    <svg class="w-7 h-7 text-green-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </div>
                <h4 class="text-base sm:text-lg font-bold text-gray-800 mb-1">2FA activado</h4>
                <p class="text-sm text-emerald-600 font-medium mb-5">Tu cuenta está protegida con autenticación en dos pasos.</p>
                <button type="button" class="btn-save text-sm sm:text-base" data-action="recovery"
                    style="display:inline-flex;width:100%;justify-content:center;background:#f3f4f6;color:#374151;box-shadow:none;border:1px solid #e5e7eb">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    Ver códigos de recuperación
                </button>
                <div class="mt-3 pt-4 border-t border-gray-100">
                    <button type="button" class="btn-cancel" data-action="disable" style="display:inline-flex;color:#ef4444;font-size:0.8rem">
                        Desactivar 2FA
                    </button>
                </div>
            </div>
        `;
    }

    function handleAction(action) {
        switch (action) {
            case 'setup': setup(); break;
            case 'confirm': confirmCode(); break;
            case 'cancel-setup': disable(); break;
            case 'disable': disable(); break;
            case 'recovery': showRecoveryCodes(); break;
            case 'back': init(); break;
        }
    }

    container.addEventListener('click', e => {
        const btn = e.target.closest('[data-action]');
        if (btn) handleAction(btn.dataset.action);
    });

    function setup() {
        loading('Generando clave...');
        const token = document.querySelector('meta[name="csrf-token"]')?.content;
        fetch('/profile/two-factor/setup', { method: 'POST', headers: { 'X-CSRF-TOKEN': token } })
            .then(r => r.json())
            .then(data => {
                state = { enabled: true, confirmed: false, qr_code: data.qr_code, secret: data.secret, recovery_codes: data.recovery_codes };
                render();
            })
            .catch(() => {
                if (statusText) statusText.textContent = 'Error al generar la clave 2FA.';
            });
    }

    function confirmCode() {
        const code = document.getElementById('twofactor-code')?.value?.trim();
        if (!code || code.length < 6) {
            showError('Ingresa un código de 6 dígitos.');
            return;
        }
        hideError();

        const token = document.querySelector('meta[name="csrf-token"]')?.content;
        fetch('/profile/two-factor/confirm', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': token },
            body: JSON.stringify({ code })
        })
            .then(r => {
                if (!r.ok) return r.json().then(d => { throw new Error(d.message || 'Código inválido.'); });
                return r.json();
            })
            .then(() => {
                state.confirmed = true;
                render();
            })
            .catch(err => showError(err.message));
    }

    function disable() {
        if (!window.confirm('¿Desactivar la autenticación en dos pasos?')) return;

        const token = document.querySelector('meta[name="csrf-token"]')?.content;
        fetch('/profile/two-factor/disable', {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': token }
        })
            .then(r => {
                if (!r.ok) throw new Error('Error del servidor');
                return r.json();
            })
            .then(() => {
                state = { enabled: false, confirmed: false };
                render();
            })
            .catch(() => showError('Error al desactivar 2FA.'));
    }

    function showRecoveryCodes() {
        const token = document.querySelector('meta[name="csrf-token"]')?.content;
        fetch('/profile/two-factor/recovery-codes', {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': token }
        })
            .then(r => r.json())
            .then(data => {
                const codes = data.recovery_codes?.join('\n') || 'Sin códigos';
                container.innerHTML = `
                    <div class="pt-2 pb-1">
                        <div class="text-center mb-4">
                            <div class="inline-flex items-center justify-center w-14 h-14 rounded-full bg-blue-50 mb-3">
                                <svg class="w-7 h-7 text-blue-500" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                    <path d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </div>
                            <h4 class="text-base font-semibold text-gray-800 mb-1">Códigos de recuperación</h4>
                            <p class="text-sm text-gray-500">Guarda estos códigos en un lugar seguro. Cada uno solo puede usarse una vez.</p>
                        </div>
                        <div class="bg-gray-50 rounded-xl p-4 border border-gray-200">
                            <pre class="text-sm font-mono text-gray-700 whitespace-pre-wrap text-center leading-relaxed">${codes.replace(/,/g, '\n')}</pre>
                        </div>
                        <div class="text-center mt-4">
                            <button type="button" class="btn-cancel" data-action="back" style="display:inline-flex">
                                Volver
                            </button>
                        </div>
                    </div>
                `;
            })
            .catch(() => showError('Error al obtener códigos de recuperación.'));
    }

    function showError(msg) {
        const el = document.getElementById('twofactor-error');
        if (el) { el.textContent = msg; el.classList.remove('hidden'); }
    }

    function hideError() {
        const el = document.getElementById('twofactor-error');
        if (el) el.classList.add('hidden');
    }

    function init() {
        fetch('/profile/two-factor')
            .then(r => r.json())
            .then(data => {
                state = data;
                render();
                if (statusText) statusText.textContent = '';
            })
            .catch(() => {
                if (statusText) statusText.textContent = 'Error al cargar el estado de seguridad.';
            });
    }

    return { init };
})();
