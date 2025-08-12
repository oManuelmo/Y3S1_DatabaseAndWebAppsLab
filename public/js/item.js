document.addEventListener('DOMContentLoaded', function() {
    const carouselItems = document.querySelector('.carousel-items');
    const prevButton = document.getElementById('prevButton');
    const nextButton = document.getElementById('nextButton');
    const totalItems = document.querySelectorAll('.carousel-item').length;
    let currentIndex = 0;

    nextButton.addEventListener('click', function() {
        if (currentIndex < totalItems - 1) {
            currentIndex++;
        } else {
            currentIndex = 0;
        }
        updateCarousel();
    });

    prevButton.addEventListener('click', function() {
        if (currentIndex > 0) {
            currentIndex--;
        } else {
            currentIndex = totalItems - 1;
        }
        updateCarousel();
    });

    function updateCarousel() {
        const offset = -currentIndex * 100;
        carouselItems.style.transform = `translateX(${offset}%)`;
    }
});
