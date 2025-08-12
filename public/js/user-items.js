document.querySelectorAll('.filter-button').forEach(button => {
    button.addEventListener('click', function () {
        const state = this.getAttribute('data-state');
        const userId = this.getAttribute('data-user-id');
        const currentState = document.querySelector('.btn-primary')?.getAttribute('data-state') || null;

        const newState = state === currentState ? 'nostate' : state;

        document.querySelectorAll('.filter-button').forEach(btn => {
            btn.classList.remove('btn-primary');
            btn.classList.add('btn-secondary');
        });

        if (newState !== 'nostate') {
            this.classList.remove('btn-secondary');
            this.classList.add('btn-primary');
        }

        const url = newState === 'nostate' ? `/profile/items/${userId}` : `/profile/items/${userId}?state=${newState}`;


        fetch(url, {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
            }
        })
        .then(response => response.json())
        .then(data => {
            document.getElementById('items-container').innerHTML = data.html;
        })
        .catch(error => console.error('Error fetching items:', error));
    });
});

