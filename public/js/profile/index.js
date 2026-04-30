document.addEventListener('DOMContentLoaded', () => {
    if (window.ProfileModal) window.ProfileModal.init();
    if (window.ProfileTabs) window.ProfileTabs.init();
    if (window.BioCounter) window.BioCounter.init();
    if (window.SuccessMessage) window.SuccessMessage.init();
    initAvatarUpload();
});

function initAvatarUpload() {
    const input = document.getElementById('avatar-input');
    const form = document.getElementById('avatar-form');
    const img = document.getElementById('profile-avatar-img');

    if (!input || !form || !img) return;

    const originalSrc = img.src;

    input.addEventListener('change', async () => {
        const file = input.files[0];
        if (!file || !file.type.startsWith('image/')) return;

        // Optimistic preview
        const preview = URL.createObjectURL(file);
        img.src = preview;
        img.style.opacity = '0.6';

        const formData = new FormData();
        formData.append('avatar', file);
        formData.append('_token', document.querySelector('meta[name="csrf-token"]').content);

        try {
            const res = await fetch(form.action, {
                method: 'POST',
                body: formData,
            });

            if (!res.ok) throw new Error('Server error');

            const result = await res.json().catch(() => null);

            if (result?.avatar) {
                img.src = result.avatar;
            }

            showToast('Foto de perfil actualizada', 'success');
        } catch (err) {
            console.error(err);
            img.src = originalSrc;
            showToast('Error al actualizar la foto', 'error');
        } finally {
            img.style.opacity = '1';
            input.value = '';
        }
    });
}
