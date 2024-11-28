const stripe = Stripe(window.stripeConfig.key);
const elements = stripe.elements();

// Track current discount
let currentDiscount = null;

// Function to format numbers with 2 decimal places
function formatNumber(num) {
    return (Math.round(num * 100) / 100).toFixed(2);
}

// Function to animate number changes
function animateValue(element, start, end, duration = 500) {
    if (!element) return;
    const startTime = performance.now();
    const updateValue = (currentTime) => {
        const elapsed = currentTime - startTime;
        const progress = Math.min(elapsed / duration, 1);
        
        // Easing function for smooth animation
        const easeOutQuad = 1 - Math.pow(1 - progress, 2);
        const current = start + (end - start) * easeOutQuad;
        
        element.textContent = formatNumber(current);
        
        if (progress < 1) {
            requestAnimationFrame(updateValue);
        }
    };
    
    requestAnimationFrame(updateValue);
}

// Function to update total price
function updateTotal() {
    const licensesInput = document.getElementById('licenses');
    const subtotalAmount = document.getElementById('subtotal-amount');
    const vatAmount = document.getElementById('vat-amount');
    const totalAmount = document.getElementById('total-amount');
    const discountAmountDiv = document.getElementById('discount-amount');
    const discountValue = document.getElementById('discount-value');
    const submitButton = document.getElementById('submit-button');

    if (!licensesInput || !subtotalAmount || !vatAmount || !totalAmount) return;

    const licenses = parseInt(licensesInput.value) || 1;
    const basePrice = parseFloat(window.stripeConfig.coursePrice);
    
    // Calculate subtotal
    let subtotal = basePrice * licenses;
    
    // Apply discount if available
    let discountAmount = 0;
    if (currentDiscount) {
        discountAmount = (subtotal * currentDiscount.percentage) / 100;
        subtotal -= discountAmount;
        
        // Show discount amount
        if (discountAmountDiv && discountValue) {
            discountAmountDiv.classList.remove('hidden');
            animateValue(discountValue, parseFloat(discountValue.textContent), discountAmount);
        }
    }
    
    // Calculate VAT
    const vat = subtotal * 0.2;
    
    // Calculate total
    const total = subtotal + vat;
    
    // Animate value changes
    animateValue(subtotalAmount, parseFloat(subtotalAmount.textContent), subtotal);
    animateValue(vatAmount, parseFloat(vatAmount.textContent), vat);
    animateValue(totalAmount, parseFloat(totalAmount.textContent), total);
    
    // Update stripe payment button text
    if (submitButton) {
        submitButton.textContent = `Pay £${formatNumber(total)}`;
    }

    // Update form hidden input
    const formLicenses = document.getElementById('form-licenses');
    if (formLicenses) {
        formLicenses.value = licenses;
    }
}

// Function to handle license quantity updates
function updateLicenses(action) {
    const licensesInput = document.getElementById('licenses');
    if (!licensesInput) return;

    const currentValue = parseInt(licensesInput.value) || 1;
    if (action === 'increase') {
        licensesInput.value = currentValue + 1;
    } else if (action === 'decrease' && currentValue > 1) {
        licensesInput.value = currentValue - 1;
    }
    
    updateTotal();
}

// Initialize after DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('payment-form');
    const submitButton = document.getElementById('submit-button');
    const errorElement = document.getElementById('card-errors');
    const applyDiscountButton = document.getElementById('apply-discount');
    const discountInput = document.getElementById('discount-code');
    const discountMessage = document.getElementById('discount-message');
    const licensesInput = document.getElementById('licenses');
    const decreaseBtn = document.querySelector('.license-btn-decrease');
    const increaseBtn = document.querySelector('.license-btn-increase');

    // Initialize Stripe elements
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
    const cardElement = document.getElementById('card-element');
    if (cardElement) {
        card.mount('#card-element');
    }

    // Handle real-time validation errors
    card.addEventListener('change', function(event) {
        if (event.error) {
            errorElement.textContent = event.error.message;
        } else {
            errorElement.textContent = '';
        }
    });

    // Add event listeners for license buttons
    if (decreaseBtn) {
        decreaseBtn.addEventListener('click', () => updateLicenses('decrease'));
    }
    if (increaseBtn) {
        increaseBtn.addEventListener('click', () => updateLicenses('increase'));
    }
    if (licensesInput) {
        licensesInput.addEventListener('change', () => {
            if (parseInt(licensesInput.value) < 1) {
                licensesInput.value = 1;
            }
            updateTotal();
        });
        
        // Prevent non-numeric input
        licensesInput.addEventListener('keypress', (e) => {
            if (!/[0-9]/.test(e.key)) {
                e.preventDefault();
            }
        });
    }

    // Handle discount code application
    if (applyDiscountButton && discountInput && discountMessage) {
        applyDiscountButton.addEventListener('click', async () => {
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

                if (!response.ok) {
                    throw new Error(result.message || 'Failed to validate discount code');
                }

                if (result.valid) {
                    currentDiscount = {
                        code: code,
                        percentage: result.discount_percentage
                    };
                    discountMessage.textContent = result.message;
                    discountMessage.className = 'mt-2 text-sm text-green-600';
                    updateTotal();
                } else {
                    discountMessage.textContent = result.message;
                    discountMessage.className = 'mt-2 text-sm text-red-600';
                }
            } catch (error) {
                discountMessage.textContent = error.message;
                discountMessage.className = 'mt-2 text-sm text-red-600';
            }
        });
    }

    // Handle form submission
    if (form) {
        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            if (submitButton) submitButton.disabled = true;

            try {
                const result = await stripe.createPaymentMethod({
                    type: 'card',
                    card: card,
                });

                if (result.error) {
                    throw new Error(result.error.message);
                }

                // Add payment method ID to form
                const paymentMethodInput = document.getElementById('form-payment-method-id');
                if (paymentMethodInput) {
                    paymentMethodInput.value = result.paymentMethod.id;
                }

                // Submit the form
                form.submit();

            } catch (error) {
                console.error('Payment error:', error);
                if (errorElement) {
                    errorElement.textContent = error.message || 'An error occurred while processing your payment. Please try again.';
                }
                if (submitButton) {
                    submitButton.disabled = false;
                }
            }
        });
    }

    // Initial total calculation
    updateTotal();
});

// Make functions globally available
window.updateLicenses = updateLicenses;
window.updateTotal = updateTotal;
