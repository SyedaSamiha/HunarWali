document.addEventListener('DOMContentLoaded', function() {
    // Get URL parameters to determine which payment method was selected
    const urlParams = new URLSearchParams(window.location.search);
    const paymentMethod = urlParams.get('method') || 'EasyPaisa'; // Default to EasyPaisa if not specified

    // Generate a random transaction ID
    const transactionId = generateTransactionId(paymentMethod);

    // Get current date and time
    const now = new Date();
    const dateTimeString = now.toLocaleString('en-PK', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
        second: '2-digit'
    });

    // Update the DOM with payment details
    document.getElementById('payment-method').textContent = paymentMethod;
    document.getElementById('transaction-id').textContent = transactionId;
    document.getElementById('datetime').textContent = dateTimeString;

    // Simulate loading for a more realistic experience
    simulateProcessing();
});

// Generate a fake transaction ID based on payment method
function generateTransactionId(method) {
    const prefix = method === 'JazzCash' ? 'JC' : 'EP';
    const randomDigits = Math.floor(Math.random() * 10000000000).toString().padStart(10, '0');
    return `${prefix}-${randomDigits}`;
}

// Simulate processing delay for a more realistic experience
function simulateProcessing() {
    // The details are already loaded, this is just for visual effect
    setTimeout(() => {
        const loadingElements = document.querySelectorAll('span[id$="-id"], span[id="datetime"]');
        loadingElements.forEach(element => {
            if (element.textContent === 'Loading...') {
                element.textContent = 'Error loading data';
                element.style.color = 'red';
            }
        });
    }, 800);
}


// Modal logic for View Receipt
document.addEventListener('DOMContentLoaded', function() {
    // Get the View Receipt button
    const viewReceiptBtn = document.querySelector('.btn.primary');
    const modal = document.getElementById('receipt-modal');
    const closeBtn = document.getElementById('close-receipt');

    // Fill receipt data from confirmation page
    function fillReceipt() {
        document.getElementById('receipt-method').textContent = document.getElementById('payment-method').textContent;
        document.getElementById('receipt-txid').textContent = document.getElementById('transaction-id').textContent;
        document.getElementById('receipt-amount').textContent = document.getElementById('amount').textContent;
        document.getElementById('receipt-datetime').textContent = document.getElementById('datetime').textContent;
    }

    // Show modal on button click
    if (viewReceiptBtn) {
        viewReceiptBtn.addEventListener('click', function(e) {
            e.preventDefault();
            fillReceipt();
            modal.style.display = 'flex';
        });
    }

    // Close modal on close button
    if (closeBtn) {
        closeBtn.addEventListener('click', function() {
            modal.style.display = 'none';
        });
    }

    // Close modal when clicking outside modal content
    window.addEventListener('click', function(event) {
        if (event.target === modal) {
            modal.style.display = 'none';
        }
    });
});