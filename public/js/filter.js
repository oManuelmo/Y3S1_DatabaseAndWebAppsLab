document.addEventListener('DOMContentLoaded', function() {
    const filtersBtn = document.getElementById('filters-btn');
    const filtersDropdown = document.getElementById('filters-dropdown');
    if (filtersBtn && filtersDropdown) {
        filtersBtn.addEventListener('click', function() {
            if (filtersDropdown.style.display === 'none' || filtersDropdown.style.display === '') {
                filtersDropdown.style.display = 'block';
            } else {
                filtersDropdown.style.display = 'none';
            }
        });

        document.addEventListener('click', function(event) {
            if (!filtersBtn.contains(event.target) && !filtersDropdown.contains(event.target)) {
                filtersDropdown.style.display = 'none';
            }
        });
    }
    
});
