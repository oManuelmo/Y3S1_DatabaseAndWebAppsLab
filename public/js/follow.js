const followBtn = document.getElementById('followBtn');
let itemId = followBtn.dataset.itemId;
let isFollowing = followBtn.dataset.isFollowing;
let toggleFollowUrl = followBtn.dataset.toggleFollowUrl;

function updateButton() {
    if (isFollowing) {
        followBtn.textContent = 'Unfollow';
        followBtn.classList.remove('follow');
        followBtn.classList.add('unfollow');
    } else {
        followBtn.textContent = 'Follow';
        followBtn.classList.remove('unfollow');
        followBtn.classList.add('follow');
    }
}

followBtn.addEventListener('click', () => {
    isFollowing = !isFollowing;

    fetch(toggleFollowUrl, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
        },
        body: JSON.stringify({ 
            isFollowing: isFollowing,
            itemId: itemId,
        }),
        
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        return response.json();
    })
    .then(data => {
        console.log(data.message);
    })
    .catch(error => console.error('Fetch error:', error));
    updateButton();
});

updateButton();