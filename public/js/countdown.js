function updateCountdown() {
    const countdownElements = document.querySelectorAll('.countdown-container .countdown');

    countdownElements.forEach(countdownElement => {
        const deadlineStr = countdownElement.dataset.deadline;
        if (!deadlineStr) return;

        const deadline = new Date(deadlineStr).getTime();
        const timeLeftElement = countdownElement.querySelector('.time');

        if (!countdownElement._interval) {
            countdownElement._interval = setInterval(function () {
                const now = new Date().getTime();
                const timeLeft = deadline - now;

                if (timeLeft <= 0) {
                    timeLeftElement.innerHTML = "Auction ended";
                    clearInterval(countdownElement._interval); 
                    countdownElement._interval = null;
                } else {
                    const days = Math.floor(timeLeft / (1000 * 60 * 60 * 24));
                    const hours = Math.floor((timeLeft % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                    const minutes = Math.floor((timeLeft % (1000 * 60 * 60)) / (1000 * 60));
                    const seconds = Math.floor((timeLeft % (1000 * 60)) / 1000);

                    timeLeftElement.innerHTML = `${days}d ${hours}h ${minutes}m ${seconds}s`;
                }
            }, 1000);
        }
    });
}

document.addEventListener('DOMContentLoaded', function () {
    updateCountdown();

    const form = document.querySelector('#bid-form');
    const messageContainer = document.getElementById('message-container');
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute("content");

    if (form) {
        form.addEventListener("submit", async (event) => {
            event.preventDefault();
            const formData = new FormData(form);

            try {
                const response = await fetch(form.action, {
                    method: "POST",
                    headers: {
                        "X-CSRF-TOKEN": csrfToken,
                    },
                    body: formData,
                });

                if (!response.ok) {
                    const error = await response.json();
                    throw new Error(error.message || "Place bid failed.");
                }

                const result = await response.json();
                if (result.success) {
                    const priceElement = document.getElementById("current-price");
                    const deadlineElement = document.getElementById("deadline");

                    if (priceElement) {
                        const soldPrice = parseFloat(result.item.soldprice);
                        if (!isNaN(soldPrice)) {
                            priceElement.textContent = `$${soldPrice.toFixed(2)}`;
                        }
                    }

                    if (deadlineElement) {

                        deadlineElement.setAttribute("data-deadline", result.item.deadline);
                        const oldTimeLeftElement = deadlineElement.querySelector('.time');
                        if (oldTimeLeftElement) {
                            oldTimeLeftElement.innerHTML = "";
                        }

                        if (deadlineElement._interval) {
                            clearInterval(deadlineElement._interval);
                            deadlineElement._interval = null;
                        }

                        updateCountdown(); // Reinicia os contadores
                    }
                    messageContainer.innerHTML = `<div class="alert alert-success mt-2"><strong>${result.message}</strong></div>`;
                } else {
                    messageContainer.innerHTML = `<div class="alert alert-danger mt-2"><strong>${result.message || 'Erro ao fazer o bid.'}</strong></div>`;
                }
            } catch (error) {
                messageContainer.innerHTML = `<div class="alert alert-danger mt-2"><strong>${error.message}</strong></div>`;
            }
        });
    }
});