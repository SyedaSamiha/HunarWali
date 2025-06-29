// Modern Login Page JavaScript

document.addEventListener('DOMContentLoaded', function() {
    // Password visibility toggle
    const togglePassword = document.querySelector('.toggle-password');
    const passwordInput = document.querySelector('#password');
    
    if (togglePassword && passwordInput) {
        togglePassword.addEventListener('click', function() {
            // Toggle password visibility
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);
            
            // Toggle icon
            this.classList.toggle('fa-eye');
            this.classList.toggle('fa-eye-slash');
            
            // Add pulse animation
            this.classList.add('pulse-animation');
            setTimeout(() => {
                this.classList.remove('pulse-animation');
            }, 500);
        });
    }
    
    // Form validation with enhanced feedback
    const loginForm = document.getElementById('login-form');
    if (loginForm) {
        loginForm.addEventListener('submit', function(e) {
            const email = document.getElementById('email').value;
            const password = document.getElementById('password').value;
            let isValid = true;
            
            // Clear previous error messages
            const existingMessages = document.querySelectorAll('.animated-message');
            existingMessages.forEach(msg => msg.remove());
            
            // Email validation
            if (!email) {
                showErrorMessage('Please enter your email address.');
                highlightInput('email');
                isValid = false;
            } else if (!isValidEmail(email)) {
                showErrorMessage('Please enter a valid email address.');
                highlightInput('email');
                isValid = false;
            }
            
            // Password validation
            if (!password) {
                if (isValid) { // Only show one error at a time
                    showErrorMessage('Please enter your password.');
                    highlightInput('password');
                    isValid = false;
                }
            }
            
            if (!isValid) {
                e.preventDefault();
            } else {
                // Add loading state to button
                const submitButton = document.getElementById('submit-button');
                submitButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Logging in...';
                submitButton.disabled = true;
            }
        });
    }
    
    // Input focus effects
    const inputs = document.querySelectorAll('input');
    inputs.forEach(input => {
        // Add focus class to parent when input is focused
        input.addEventListener('focus', function() {
            this.parentElement.classList.add('input-focused');
        });
        
        // Remove focus class when input loses focus
        input.addEventListener('blur', function() {
            this.parentElement.classList.remove('input-focused');
        });
    });
    
    // Helper functions
    function showErrorMessage(message) {
        const errorMessage = document.createElement('p');
        errorMessage.className = 'animated-message';
        errorMessage.innerHTML = `<i class="fas fa-exclamation-circle"></i> ${message}`;
        
        // Insert before the form
        loginForm.insertAdjacentElement('beforebegin', errorMessage);
        
        // Hide the message after 4 seconds
        setTimeout(function() {
            errorMessage.style.opacity = '0';
            setTimeout(() => {
                errorMessage.remove();
            }, 500);
        }, 4000);
    }
    
    function highlightInput(inputId) {
        const input = document.getElementById(inputId);
        input.classList.add('input-error');
        
        // Remove error class after 4 seconds
        setTimeout(function() {
            input.classList.remove('input-error');
        }, 4000);
    }
    
    function isValidEmail(email) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return emailRegex.test(email);
    }
    
    // Handle session messages
    const sessionMessage = document.querySelector('.animated-message');
    if (sessionMessage) {
        setTimeout(function() {
            sessionMessage.style.opacity = '0';
            setTimeout(() => {
                sessionMessage.style.display = 'none';
            }, 500);
        }, 4000);
    }
});

// Add CSS for new animations
document.head.insertAdjacentHTML('beforeend', `
<style>
    @keyframes pulse {
        0% { transform: translateY(-50%) scale(1); }
        50% { transform: translateY(-50%) scale(1.2); }
        100% { transform: translateY(-50%) scale(1); }
    }
    
    .pulse-animation {
        animation: pulse 0.5s ease-in-out;
    }
    
    .input-focused {
        transform: translateY(-2px);
    }
    
    .input-error {
        border-color: #e74c3c !important;
        animation: shake 0.5s ease-in-out;
    }
    
    @keyframes shake {
        0%, 100% { transform: translateX(0); }
        10%, 30%, 50%, 70%, 90% { transform: translateX(-5px); }
        20%, 40%, 60%, 80% { transform: translateX(5px); }
    }
</style>
`);