import { animateLike, showMatchToast } from '../ui.js';

let CSRF;

function attachLikeButtons() {
    document.querySelectorAll('.card-like-btn:not([data-likes-initialized])').forEach(btn => {
        btn.dataset.likesInitialized = '1';
        btn.addEventListener('click', async (e) => {
            e.stopPropagation();
            const userId = btn.dataset.user;
            const svg = btn.querySelector('svg');
            const wasLiked = btn.dataset.liked === '1';

            btn.disabled = true;

            const newLiked = !wasLiked;
            btn.dataset.liked = newLiked ? '1' : '0';
            btn.title = newLiked ? 'Quitar like' : 'Dar like';
            svg.setAttribute('fill', newLiked ? 'currentColor' : 'none');
            if (newLiked) {
                btn.classList.add('liked');
                animateLike(btn);
            } else {
                btn.classList.remove('liked');
            }

            try {
                const res = await fetch(`/explore/like/${userId}`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': CSRF,
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                    },
                });

                if (!res.ok) throw new Error('Server error');
                const data = await res.json();

                if (data.match) {
                    showMatchToast(data.matchName);
                }
            } catch (err) {
                console.error(err);
                btn.dataset.liked = wasLiked ? '1' : '0';
                btn.title = wasLiked ? 'Quitar like' : 'Dar like';
                svg.setAttribute('fill', wasLiked ? 'currentColor' : 'none');
                if (wasLiked) {
                    btn.classList.add('liked');
                } else {
                    btn.classList.remove('liked');
                }
            } finally {
                btn.disabled = false;
            }
        });
    });
}

export function init(csrf) {
    CSRF = csrf;
    attachLikeButtons();

    document.addEventListener('explore-cards-rendered', attachLikeButtons);
}
