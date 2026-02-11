function formatCount(count) {
    if (count >= 1000) {
        const k = count / 1000;
        return k % 1 === 0 ? `${k}K` : `${k.toFixed(1)}K`;
    }
    return count.toString();
}

function updateAllButtonsForUser(userId, isFollowing) {
    document.querySelectorAll(`.follow-button[data-user-id="${userId}"]`).forEach((btn) => {
        btn.dataset.following = isFollowing ? 'true' : 'false';
        const textEl = btn.querySelector('.follow-text');

        if (isFollowing) {
            btn.classList.remove('btn-outline');
            btn.classList.add('btn-primary');
            if (textEl) textEl.textContent = 'Following';
        } else {
            btn.classList.add('btn-outline');
            btn.classList.add('btn-primary');
            if (textEl) textEl.textContent = 'Follow';
        }
    });
}

function updateFollowerCounts(userId, count) {
    document.querySelectorAll(`.followers-count[data-user-id="${userId}"]`).forEach((el) => {
        el.textContent = formatCount(count);
    });
}

// Hover effect: "Following" -> "Unfollow"
document.addEventListener('mouseenter', (e) => {
    if (!e.target || !e.target.closest) return;
    const button = e.target.closest('.follow-button');
    if (!button || button.dataset.following !== 'true') return;

    const textEl = button.querySelector('.follow-text');
    if (textEl) {
        textEl.textContent = 'Unfollow';
        button.classList.remove('btn-primary');
        button.classList.add('btn-error');
    }
}, true);

document.addEventListener('mouseleave', (e) => {
    if (!e.target || !e.target.closest) return;
    const button = e.target.closest('.follow-button');
    if (!button || button.dataset.following !== 'true') return;

    const textEl = button.querySelector('.follow-text');
    if (textEl) {
        textEl.textContent = 'Following';
        button.classList.remove('btn-error');
        button.classList.add('btn-primary');
    }
}, true);

document.addEventListener('click', async (e) => {
    const button = e.target.closest('.follow-button');
    if (!button || button.disabled) return;

    const userId = button.dataset.userId;
    const isFollowing = button.dataset.following === 'true';
    const method = isFollowing ? 'DELETE' : 'POST';
    const url = `/users/${userId}/follow`;

    button.disabled = true;

    try {
        const response = await axios({ method, url });
        const { followers_count, is_following } = response.data;

        updateAllButtonsForUser(userId, is_following);
        updateFollowerCounts(userId, followers_count);
    } catch (error) {
        console.error('Follow action failed:', error);
    } finally {
        button.disabled = false;
    }
});
