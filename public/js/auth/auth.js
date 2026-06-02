/**
 * Auth JavaScript
 * Nexa App - Registration, Login
 */

document.addEventListener('DOMContentLoaded', () => {
    initPasswordToggle();
    initSendCode();
});

/**
 * Toggle password visibility (register.blade.php)
 */
function initPasswordToggle() {
    window.togglePwd = function (inputId, iconId) {
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

/**
 * Enviar código de verificación al correo (AJAX)
 */
function initSendCode() {
    const btnSend = document.getElementById('btnSendCode');
    const emailInput = document.getElementById('email');
    const codeInput = document.getElementById('verification_code');
    const codeField = document.getElementById('codeField');
    const codeStatus = document.getElementById('codeStatus');

    if (!btnSend || !emailInput) return;

    let resendTimer = 0;
    let timerInterval = null;

    function getCsrfToken() {
        const meta = document.querySelector('meta[name="csrf-token"]');
        return meta ? meta.getAttribute('content') : '';
    }

    function setStatus(el, message, type) {
        if (!el) return;
        el.textContent = message;
        el.className = 'code-status';
        if (type) el.classList.add('code-status--' + type);
    }

    function clearStatus(el) {
        if (el) {
            el.textContent = '';
            el.className = 'code-status';
        }
    }

    function getErrorMessage(data) {
        if (data.message && data.message !== 'The given data was invalid.') {
            return data.message;
        }
        if (data.errors) {
            const firstKey = Object.keys(data.errors)[0];
            if (firstKey && data.errors[firstKey].length) {
                return data.errors[firstKey][0];
            }
        }
        return null;
    }

    function startResendTimer(seconds) {
        resendTimer = seconds;
        btnSend.disabled = true;
        if (timerInterval) clearInterval(timerInterval);
        timerInterval = setInterval(() => {
            resendTimer--;
            btnSend.textContent = `Reenviar en ${resendTimer}s`;
            if (resendTimer <= 0) {
                clearInterval(timerInterval);
                timerInterval = null;
                btnSend.disabled = false;
                btnSend.textContent = 'Reenviar código';
            }
        }, 1000);
    }

    btnSend.addEventListener('click', () => {
        const email = emailInput.value.trim();

        if (!email || !email.includes('@')) {
            setStatus(codeStatus, 'Ingresa un correo válido.', 'error');
            return;
        }

        clearStatus(codeStatus);
        btnSend.disabled = true;
        btnSend.textContent = 'Enviando...';

        fetch('/register/send-code', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': getCsrfToken(),
                'Accept': 'application/json',
            },
            body: JSON.stringify({ email }),
        })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    setStatus(codeStatus, 'Código enviado. Revisa tu correo.', 'success');
                    if (codeField) codeField.style.display = 'block';
                    if (codeInput) codeInput.focus();
                    startResendTimer(60);
                } else {
                    setStatus(codeStatus, data.message || 'Error al enviar el código.', 'error');
                    btnSend.disabled = false;
                    btnSend.textContent = 'Enviar código';
                }
            })
            .catch(() => {
                setStatus(codeStatus, 'Error de conexión. Intenta de nuevo.', 'error');
                btnSend.disabled = false;
                btnSend.textContent = 'Enviar código';
            });
    });
}
