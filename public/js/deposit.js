document.addEventListener('DOMContentLoaded', function () {
    const stripe = Stripe('pk_test_51QTaH5DCAMytGxoznnBGILTQShtMiCPgl4hmbItqCXI9PSlDpA9df2dhhxcPFeYQUdnbINVnuLfR7q4QomFik12l004aJzLOQt');
    const elements = stripe.elements();

    const card = elements.create('card', {});

    card.mount('#card-element');

    const form = document.getElementById('deposit-form');
    const depositButton = document.getElementById('deposit-button');

    form.addEventListener('submit', async function (event) {
        event.preventDefault();

        depositButton.disabled = true;

        const { paymentMethod, error } = await stripe.createPaymentMethod({
            type: 'card',
            card: card,
        });

        if (error) {
            const errorElement = document.getElementById('card-errors');
            errorElement.textContent = error.message;
            depositButton.disabled = false;
        } else {
            const hiddenInput = document.createElement('input');
            hiddenInput.type = 'hidden';
            hiddenInput.name = 'payment_method_id';
            hiddenInput.value = paymentMethod.id;
            form.appendChild(hiddenInput);

            form.submit();
        }
    });
});