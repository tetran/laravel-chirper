document.addEventListener('click', async (e) => {
    const button = e.target.closest('.like-button');
    if (!button || button.disabled) return;

    const chirpId = button.dataset.chirpId;
    const isLiked = button.dataset.liked === 'true';
    const method = isLiked ? 'DELETE' : 'POST';
    const url = `/chirps/${chirpId}/likes`;

    button.disabled = true;

    try {
        const response = await axios({ method, url });
        const { likes_count, is_liked } = response.data;

        button.dataset.liked = is_liked ? 'true' : 'false';
        button.setAttribute('aria-label', is_liked ? 'Unlike this chirp' : 'Like this chirp');

        const heartFilled = button.querySelector('.heart-filled');
        const heartOutline = button.querySelector('.heart-outline');

        if (is_liked) {
            heartFilled.classList.remove('hidden');
            heartOutline.classList.add('hidden');
            button.classList.remove('text-base-content/40');
            button.classList.add('text-error');
            button.classList.add('animate-like-pop');
        } else {
            heartFilled.classList.add('hidden');
            heartOutline.classList.remove('hidden');
            button.classList.add('text-base-content/40');
            button.classList.remove('text-error');
        }

        button.addEventListener('animationend', () => {
            button.classList.remove('animate-like-pop');
        }, { once: true });

        const countEl = document.querySelector(`.likes-count[data-chirp-id="${chirpId}"]`);
        if (countEl) {
            countEl.textContent = likes_count;
        }
    } catch (error) {
        console.error('Like action failed:', error);
    } finally {
        button.disabled = false;
    }
});
