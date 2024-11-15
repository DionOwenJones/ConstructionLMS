const stripe = Stripe(window.stripeConfig.key);
const elements = stripe.elements();
const form = document.getElementById('payment-form');
const submitButton = document.getElementById('submit-button');
const errorElement = document.getElementById('card-errors');

// Create card element
const card = elements.create('card', {
    style: {
        base: {
            fontSize: '16px',
            color: '#32325d',
            fontFamily: '-apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif'
        }
    }
});

// Mount the card element
card.mount('#card-element');

// Handle form submission
form.addEventListener('submit', async (e) => {
    e.preventDefault();
    submitButton.disabled = true;
    submitButton.textContent = 'Processing...';
    errorElement.textContent = '';

    try {
        // Create payment method
        const { paymentMethod, error } = await stripe.createPaymentMethod({
            type: 'card',
            card: card
        });

        if (error) {
            throw new Error(error.message);
        }

        // Send to server
        const response = await fetch(window.stripeConfig.processUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': window.stripeConfig.csrfToken
            },
            body: JSON.stringify({
                payment_method_id: paymentMethod.id
            })
        });

        const result = await response.json();

        if (result.success) {
            window.location.href = window.stripeConfig.successUrl;
        } else {
            throw new Error(result.message || 'Payment failed');
        }

    } catch (error) {
        errorElement.textContent = error.message;
        submitButton.disabled = false;
        submitButton.textContent = `Pay $${window.stripeConfig.coursePrice}`;
    }
});
