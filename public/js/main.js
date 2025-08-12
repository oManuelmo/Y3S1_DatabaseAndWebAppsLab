document.addEventListener('DOMContentLoaded', function () {
    const carouselWrapper = document.querySelector('.carousel-wrapper');
    const prevButton = document.getElementById('prevButton');
    const nextButton = document.getElementById('nextButton');
    let carouselItems = document.querySelectorAll('.carousel-item');
    let totalItems = carouselItems.length;
    let currentIndex = 0;
    let autoChangeIntervalId;
    const autoChangeInterval = 10000;

    function startAutoChange() {
        if (autoChangeIntervalId) {
            clearInterval(autoChangeIntervalId);
        }
        autoChangeIntervalId = setInterval(nextItem, autoChangeInterval);
    }

    function nextItem() {
        currentIndex++;
        if (currentIndex >= totalItems) {
            currentIndex = 0;
        }
        updateCarousel();
    }

    function prevItem() {
        currentIndex--;
        if (currentIndex < 0) {
            currentIndex = totalItems - 1;
        }
        updateCarousel();
    }

    function updateCarousel() {
        const offset = -currentIndex * 100;
        carouselWrapper.style.transform = `translateX(${offset}%)`;
        startAutoChange();
    }

    function updateCountdown(index) {
        const countdownElement = carouselItems[index]?.querySelector('.countdown .time');
        const deadlineStr = carouselItems[index]?.querySelector('.countdown')?.dataset.deadline;

        if (!countdownElement || !deadlineStr) return;

        const deadline = new Date(deadlineStr).getTime();

        const interval = setInterval(function () {
            const now = new Date().getTime();
            const timeLeft = deadline - now;

            if (timeLeft <= 0) {
                countdownElement.innerHTML = "Auction ended";
                clearInterval(interval);

                handleEndedAuctions();
            } else {
                const hours = Math.floor((timeLeft % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                const minutes = Math.floor((timeLeft % (1000 * 60 * 60)) / (1000 * 60));
                const seconds = Math.floor((timeLeft % (1000 * 60)) / 1000);

                countdownElement.innerHTML = `${hours}h ${minutes}m ${seconds}s`;
            }
        }, 1000);
    }

    function handleEndedAuctions() {
        const endedItems = [];
        let latestDeadline = null;

        const displayedItemIds = Array.from(carouselItems).map(item =>
            item.querySelector('.check-auction-link a').href.split('/').pop()
        );

        carouselItems.forEach((item, index) => {
            const countdownElement = item.querySelector('.countdown .time');
            const deadlineStr = item.querySelector('.countdown')?.dataset.deadline;

            if (countdownElement && deadlineStr) {
                const deadline = new Date(deadlineStr).getTime();
                const now = new Date().getTime();

                if (deadline <= now) {
                    endedItems.push(item);

                    if (!latestDeadline || deadlineStr > latestDeadline) {
                        latestDeadline = deadlineStr;
                    }
                }
            }
        });

        endedItems.forEach(item => item.parentNode.removeChild(item));

        updateCarouselIndexes();
        if (latestDeadline) {
            fetchAndAddNewItems(latestDeadline, endedItems.length, displayedItemIds);
        }
    }

    function fetchAndAddNewItems(lastDeadline, count, excludedIds) {
        fetch(`/api/next-upcoming-items?lastDeadline=${encodeURIComponent(lastDeadline)}&count=${count}&excludedIds=${JSON.stringify(excludedIds)}`)
            .then(response => response.json())
            .then(newItems => {
                newItems.forEach(addNewItemToCarousel);
            })
            .catch(error => console.error("Error fetching new items:", error));
    }

    function addNewItemToCarousel(item) {
        const images = item.images.length > 0 ? item.images[0] : '/no-image.png';

        const newCarouselItem = document.createElement('div');
        newCarouselItem.classList.add('carousel-item');
        newCarouselItem.innerHTML = `
            <div class="carousel-item-content">
                <div class="carousel-item-left">
                    <img src="${images}" alt="Product Image" class="carousel-image">
                </div>
                <div class="carousel-item-right">
                    <h3 class="carousel-item-name">${item.name}</h3>
                    <p class="carousel-item-description">${item.description}</p>
                    <div class="countdown-container">
                        <p class="countdown" data-deadline="${item.deadline}">
                            Time left: <span class="time">Loading...</span>
                        </p>
                    </div>
                    <div class="check-auction-link">
                        <a href="/items/${item.itemid}" class="check-auction-link">Check Auction</a>
                    </div>
                </div>
            </div>
        `;

        carouselWrapper.appendChild(newCarouselItem);

        updateCarouselIndexes();

        updateCountdown(carouselItems.length - 1);
    }

    function updateCarouselIndexes() {
        carouselItems = document.querySelectorAll('.carousel-item');
        totalItems = carouselItems.length;

        if (totalItems === 0) {
            prevButton.style.display = 'none';
            nextButton.style.display = 'none';
        } else {
            prevButton.style.display = 'block';
            nextButton.style.display = 'block';
        }
    }

    nextButton.addEventListener('click', nextItem);
    prevButton.addEventListener('click', prevItem);

    carouselItems.forEach((_, index) => updateCountdown(index));

    startAutoChange();
});
