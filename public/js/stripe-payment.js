const stripe = Stripe(window.stripeConfig.key);
const elements = stripe.elements();
const form = document.getElementById('payment-form');
const submitButton = document.getElementById('submit-button');
const errorElement = document.getElementById('card-errors');
const spinner = document.getElementById('spinner');
const buttonText = document.getElementById('button-text');

// Create card element
const card = elements.create('card', {
    style: {
        base: {
            fontSize: '16px',
            color: '#32325d',
            fontFamily: '-apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif'
        }
    },
    hidePostalCode: true
});

// Mount the card element
card.mount('#card-element');

// Handle real-time validation errors
card.addEventListener('change', function(event) {
    if (event.error) {
        errorElement.textContent = event.error.message;
    } else {
        errorElement.textContent = '';
    }
});

// Handle discount code application
const applyDiscountButton = document.getElementById('apply-discount');
const discountInput = document.getElementById('discount-code');
const discountMessage = document.getElementById('discount-message');
const discountAmountDiv = document.getElementById('discount-amount');
const discountValue = document.getElementById('discount-value');
const vatAmount = document.getElementById('vat-amount');
const totalPrice = document.getElementById('total-price');
let originalPrice = parseFloat(window.stripeConfig.coursePrice);
let currentDiscount = null;

applyDiscountButton?.addEventListener('click', async () => {
    const code = discountInput.value.trim();
    if (!code) {
        discountMessage.textContent = 'Please enter a discount code';
        discountMessage.className = 'mt-2 text-sm text-red-600';
        return;
    }

    try {
        const response = await fetch(window.stripeConfig.validateDiscountUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': window.stripeConfig.csrfToken,
                'Accept': 'application/json'
            },
            body: JSON.stringify({ code })
        });

        const result = await response.json();

        if (response.ok && result.valid) {
            // Store the current discount
            currentDiscount = {
                code: code,
                percentage: result.discount_percentage
            };

            // Calculate amounts
            const discountAmount = (originalPrice * result.discount_percentage / 100);
            const newPrice = originalPrice - discountAmount;
            const newVat = newPrice * 0.2;
            const newTotal = newPrice + newVat;

            // Update display
            discountMessage.textContent = result.message;
            discountMessage.className = 'mt-2 text-sm text-green-600';
            discountAmountDiv.classList.remove('hidden');
            discountValue.textContent = discountAmount.toFixed(2);
            vatAmount.textContent = newVat.toFixed(2);
            totalPrice.textContent = `£${newTotal.toFixed(2)}`;
            buttonText.textContent = `Pay £${newTotal.toFixed(2)}`;

            // Apply discount
            const applyResponse = await fetch(window.stripeConfig.applyDiscountUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': window.stripeConfig.csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ code })
            });

            if (!applyResponse.ok) {
                throw new Error('Failed to apply discount code');
            }
        } else {
            currentDiscount = null;
            discountMessage.textContent = result.message || 'Invalid discount code';
            discountMessage.className = 'mt-2 text-sm text-red-600';
            discountAmountDiv.classList.add('hidden');
            
            // Reset to original prices
            const originalVat = originalPrice * 0.2;
            const originalTotal = originalPrice + originalVat;
            vatAmount.textContent = originalVat.toFixed(2);
            totalPrice.textContent = `£${originalTotal.toFixed(2)}`;
            buttonText.textContent = `Pay £${originalTotal.toFixed(2)}`;
        }
    } catch (error) {
        console.error('Discount error:', error);
        currentDiscount = null;
        discountMessage.textContent = 'Error applying discount code. Please try again.';
        discountMessage.className = 'mt-2 text-sm text-red-600';
    }
});

// Handle form submission
form.addEventListener('submit', async (e) => {
    e.preventDefault();
    
    // Disable submit button and show spinner
    submitButton.disabled = true;
    spinner.classList.remove('hidden');
    buttonText.textContent = 'Processing...';
    errorElement.textContent = '';

    try {
        // Create payment method
        const { paymentMethod, error: stripeError } = await stripe.createPaymentMethod({
            type: 'card',
            card: card,
            billing_details: {
                email: window.stripeConfig.userEmail
            }
        });

        if (stripeError) {
            console.error('Stripe createPaymentMethod error:', stripeError);
            throw new Error(stripeError.message);
        }

        console.log('Payment method created:', paymentMethod.id);

        // Send to server
        const response = await fetch(window.stripeConfig.processUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': window.stripeConfig.csrfToken,
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                payment_method_id: paymentMethod.id,
                discount_code: currentDiscount?.code || null
            })
        });

        console.log('Server response status:', response.status);
        const result = await response.json();
        console.log('Server response:', result);

        if (result.success) {
            window.location.href = result.redirect || window.stripeConfig.successUrl;
        } else {
            throw new Error(result.error || 'An error occurred while processing your payment. Please check your card details and try again.');
        }

    } catch (error) {
        console.error('Payment error:', error);
        errorElement.textContent = error.message || 'An error occurred while processing your payment. Please try again.';
        submitButton.disabled = false;
        spinner.classList.add('hidden');
        buttonText.textContent = `Pay £${(currentDiscount ? (originalPrice - (originalPrice * currentDiscount.percentage / 100)) : originalPrice).toFixed(2)}`;
    }
});
