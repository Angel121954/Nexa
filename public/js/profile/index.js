document.addEventListener('DOMContentLoaded', () => {
    if (window.ProfileModal) window.ProfileModal.init();
    if (window.ProfileTabs) window.ProfileTabs.init();
    if (window.BioCounter) window.BioCounter.init();
    if (window.SuccessMessage) window.SuccessMessage.init();
    initAvatarUpload();
    initGalleryUpload();
});

function initGalleryUpload() {
    const input = document.getElementById('gallery-upload-input');
    const form = document.getElementById('gallery-upload-form');
    const grid = document.querySelector('.gallery-grid');

    if (!input || !form) return;

    input.addEventListener('change', async () => {
        const file = input.files[0];
        if (!file || !file.type.startsWith('image/')) return;

        const preview = URL.createObjectURL(file);
        const token = document.querySelector('meta[name="csrf-token"]').content;
        const placeholder = grid?.querySelector('.gallery-empty');
        if (placeholder) placeholder.remove();

        const item = document.createElement('div');
        item.className = 'gallery-item';
        item.innerHTML = `
            <img src="${preview}">
            <div class="gallery-item-overlay"></div>
            <div class="gallery-item-spinner">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2" opacity=".25"/>
                    <path d="M4 12a8 8 0 018-8" stroke="currentColor" stroke-width="2"/>
                </svg>
            </div>
        `;
        grid?.insertBefore(item, grid?.querySelector('.gallery-item') || null);
        input.value = '';

        const formData = new FormData();
        formData.append('photo', file);
        formData.append('_token', token);

        try {
            const res = await fetch(form.action, {
                method: 'POST',
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
                body: formData,
            });

            const data = await res.json();

            if (!res.ok) {
                item.remove();
                showToast(data.message, 'error');
                return;
            }

            item.querySelector('img').src = data.url;
            item.querySelector('.gallery-item-spinner')?.remove();
            item.insertAdjacentHTML('beforeend', `
                <form action="/profile/photo/${data.id}" method="POST" class="gallery-item-delete">
                    <input type="hidden" name="_token" value="${token}">
                    <input type="hidden" name="_method" value="DELETE">
                    <button type="submit" class="bg-white/90 hover:bg-white p-2 rounded-full shadow border-0">
                        <svg class="w-4 h-4 text-red-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M6 6l12 12M18 6L6 18" />
                        </svg>
                    </button>
                </form>
            `);

            showToast(data.message, 'success');
        } catch (err) {
            item.remove();
            console.error(err);
            showToast('Error subiendo foto', 'error');
        }
    });
}

function initAvatarUpload() {
    const input = document.getElementById('avatarInput');
    const form = document.getElementById('avatar-form');
    const img = document.getElementById('avatar-img');
    const topbarImg = document.getElementById('topbar-avatar');

    if (!input || !form || !img) return;

    const originalSrc = img.src;
    const originalTopbarSrc = topbarImg ? topbarImg.src : null;

    input.addEventListener('change', async () => {
        const file = input.files[0];
        if (!file || !file.type.startsWith('image/')) return;

        // Optimistic UI: preview instantánea
        const preview = URL.createObjectURL(file);
        img.src = preview;
        img.style.transition = 'opacity 0.15s ease';
        img.style.opacity = '0.5';

        if (topbarImg) {
            topbarImg.src = preview;
            topbarImg.style.transition = 'opacity 0.15s ease';
            topbarImg.style.opacity = '0.5';
        }

        // Spinner overlay
        const style = document.createElement('style');
        style.textContent = `@keyframes avatarSpin{0%{transform:translate(-50%,-50%) rotate(0deg)}100%{transform:translate(-50%,-50%) rotate(360deg)}}`;
        document.head.appendChild(style);

        const spinner = document.createElement('div');
        spinner.id = 'avatar-spinner';
        spinner.innerHTML = `
            <svg style="position:absolute;top:50%;left:50%;width:32px;height:32px;animation:avatarSpin 0.8s linear infinite;" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle style="opacity:0.25;" cx="12" cy="12" r="10" stroke="white" stroke-width="4"></circle>
                <path style="opacity:0.75;" fill="white" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
        `;
        spinner.style.cssText = 'position:absolute;top:0;left:0;width:100%;height:100%;display:flex;align-items:center;justify-content:center;border-radius:50%;';
        form.appendChild(spinner);

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
                if (topbarImg) topbarImg.src = result.avatar;
            }

            showToast('Foto de perfil actualizada', 'success');
        } catch (err) {
            console.error(err);
            img.src = originalSrc;
            if (topbarImg) topbarImg.src = originalTopbarSrc;
            showToast('Error al actualizar la foto', 'error');
        } finally {
            img.style.opacity = '1';
            if (topbarImg) topbarImg.style.opacity = '1';
            input.value = '';
            const s = document.getElementById('avatar-spinner');
            if (s) s.remove();
            if (style) style.remove();
        }
    });
}
