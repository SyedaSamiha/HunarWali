// Modern Registration Page JavaScript

document.addEventListener('DOMContentLoaded', function() {
    // Password visibility toggle
    const togglePassword = document.querySelector('.toggle-password');
    const passwordInput = document.querySelector('#password');
    const toggleConfirmPassword = document.querySelector('.toggle-confirm-password');
    const confirmPasswordInput = document.querySelector('#confirm_password');
    
    // Setup password toggle
    if (togglePassword && passwordInput) {
        setupPasswordToggle(togglePassword, passwordInput);
    }
    
    // Setup confirm password toggle
    if (toggleConfirmPassword && confirmPasswordInput) {
        setupPasswordToggle(toggleConfirmPassword, confirmPasswordInput);
    }
    
    // Form validation with enhanced feedback
    const registrationForm = document.getElementById('registration-form');
    if (registrationForm) {
        registrationForm.addEventListener('submit', function(e) {
            let isValid = validateForm();
            
            if (!isValid) {
                e.preventDefault();
            } else {
                // Add loading state to button
                const submitButton = document.getElementById('submit-button');
                submitButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Registering...';
                submitButton.disabled = true;
            }
        });
    }
    
    // Input focus effects
    const inputs = document.querySelectorAll('input, select');
    inputs.forEach(input => {
        // Add focus class to parent when input is focused
        input.addEventListener('focus', function() {
            this.parentElement.classList.add('input-focused');
        });
        
        // Remove focus class when input loses focus
        input.addEventListener('blur', function() {
            this.parentElement.classList.remove('input-focused');
            
            // Validate field on blur
            validateField(this);
        });
    });
    
    // File input preview for ID card uploads
    setupFileInputPreview('id_card_front', 'id-front-preview');
    setupFileInputPreview('id_card_back', 'id-back-preview');
    
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
    
    // Helper functions
    function setupPasswordToggle(toggleBtn, inputField) {
        toggleBtn.addEventListener('click', function() {
            // Toggle password visibility
            const type = inputField.getAttribute('type') === 'password' ? 'text' : 'password';
            inputField.setAttribute('type', type);
            
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
    
    function setupFileInputPreview(inputId, previewId) {
        const input = document.getElementById(inputId);
        const preview = document.getElementById(previewId);
        
        if (input && preview) {
            input.addEventListener('change', function() {
                if (this.files && this.files[0]) {
                    const reader = new FileReader();
                    
                    reader.onload = function(e) {
                        preview.innerHTML = `
                            <div class="preview-container">
                                <img src="${e.target.result}" alt="ID Card Preview" class="id-preview-img">
                                <span class="preview-filename">${input.files[0].name}</span>
                            </div>
                        `;
                        preview.classList.add('has-preview');
                    };
                    
                    reader.readAsDataURL(this.files[0]);
                } else {
                    preview.innerHTML = '';
                    preview.classList.remove('has-preview');
                }
            });
        }
    }
    
    function validateForm() {
        // Clear previous error messages
        const existingMessages = document.querySelectorAll('.animated-message');
        existingMessages.forEach(msg => msg.remove());
        
        let isValid = true;
        
        // Validate username
        const username = document.getElementById('username');
        if (!username.value.trim()) {
            showErrorMessage('Please enter a username.');
            highlightInput(username);
            isValid = false;
        }
        
        // Validate email
        const email = document.getElementById('email');
        if (!email.value.trim()) {
            if (isValid) { // Only show one error at a time
                showErrorMessage('Please enter your email address.');
                highlightInput(email);
                isValid = false;
            }
        } else if (!isValidEmail(email.value)) {
            if (isValid) {
                showErrorMessage('Please enter a valid email address.');
                highlightInput(email);
                isValid = false;
            }
        }
        
        // Validate password
        const password = document.getElementById('password');
        if (!password.value) {
            if (isValid) {
                showErrorMessage('Please enter a password.');
                highlightInput(password);
                isValid = false;
            }
        } else if (password.value.length < 8) {
            if (isValid) {
                showErrorMessage('Password must be at least 8 characters long.');
                highlightInput(password);
                isValid = false;
            }
        }
        
        // Validate confirm password
        const confirmPassword = document.getElementById('confirm_password');
        if (!confirmPassword.value) {
            if (isValid) {
                showErrorMessage('Please confirm your password.');
                highlightInput(confirmPassword);
                isValid = false;
            }
        } else if (password.value && confirmPassword.value !== password.value) {
            if (isValid) {
                showErrorMessage('Passwords do not match. Please try again.');
                highlightInput(confirmPassword);
                highlightInput(password);
                isValid = false;
            }
        }
        
        // Validate gender
        const gender = document.getElementById('gender');
        if (gender.value === '') {
            if (isValid) {
                showErrorMessage('Please select your gender.');
                highlightInput(gender);
                isValid = false;
            }
        }
        
        // Validate role
        const role = document.getElementById('role');
        if (role.value === '') {
            if (isValid) {
                showErrorMessage('Please select your role.');
                highlightInput(role);
                isValid = false;
            }
        }
        
        // Validate ID card uploads - these are compulsory fields
        const idCardFront = document.getElementById('id_card_front');
        const idCardBack = document.getElementById('id_card_back');
        
        if (!idCardFront.files || !idCardFront.files[0]) {
            if (isValid) {
                showErrorMessage('Please upload the front of your ID card. This is required.');
                highlightInput(idCardFront);
                // Add a red border to the file input container
                idCardFront.parentElement.classList.add('file-error');
                setTimeout(() => {
                    idCardFront.parentElement.classList.remove('file-error');
                }, 4000);
                isValid = false;
            }
        }
        
        if (!idCardBack.files || !idCardBack.files[0]) {
            if (isValid) {
                showErrorMessage('Please upload the back of your ID card. This is required.');
                highlightInput(idCardBack);
                // Add a red border to the file input container
                idCardBack.parentElement.classList.add('file-error');
                setTimeout(() => {
                    idCardBack.parentElement.classList.remove('file-error');
                }, 4000);
                isValid = false;
            }
        }
        
        return isValid;
    }
    
    function validateField(field) {
        // Skip validation if field is empty and not required
        if (!field.value.trim() && !field.hasAttribute('required')) {
            return true;
        }
        
        let isValid = true;
        
        switch(field.id) {
            case 'email':
                isValid = isValidEmail(field.value);
                if (!isValid) {
                    showErrorMessage('Please enter a valid email address.');
                }
                break;
            case 'password':
                isValid = field.value.length >= 8;
                if (!isValid) {
                    showErrorMessage('Password must be at least 8 characters long.');
                }
                break;
            case 'confirm_password':
                const password = document.getElementById('password');
                isValid = field.value === password.value;
                if (!isValid && field.value.trim() !== '') {
                    showErrorMessage('Passwords do not match. Please try again.');
                    // Also highlight the password field to indicate the mismatch
                    password.classList.add('input-error');
                    setTimeout(function() {
                        password.classList.remove('input-error');
                    }, 4000);
                }
                break;
            case 'gender':
            case 'role':
                isValid = field.value !== '';
                break;
            case 'id_card_front':
            case 'id_card_back':
                isValid = field.files && field.files.length > 0;
                if (!isValid) {
                    showErrorMessage(`Please upload your ID card ${field.id === 'id_card_front' ? 'front' : 'back'} image.`);
                }
                break;
        }
        
        if (!isValid) {
            field.classList.add('input-error');
        } else {
            field.classList.remove('input-error');
        }
        
        return isValid;
    }
    
    function showErrorMessage(message) {
        // Clear any existing error messages first
        const existingMessages = document.querySelectorAll('.animated-message');
        existingMessages.forEach(msg => {
            msg.style.opacity = '0';
            setTimeout(() => {
                msg.remove();
            }, 500);
        });
        
        // Create new error message
        const errorMessage = document.createElement('p');
        errorMessage.className = 'animated-message';
        errorMessage.innerHTML = `<i class="fas fa-exclamation-circle"></i> ${message}`;
        
        // Find the error message container and insert the message
        const errorContainer = document.querySelector('.error-message-container');
        if (errorContainer) {
            errorContainer.appendChild(errorMessage);
        } else {
            // Fallback to inserting before the form if container not found
            registrationForm.insertAdjacentElement('beforebegin', errorMessage);
        }
        
        // Hide the message after 6 seconds (increased from 4 seconds for better visibility)
        setTimeout(function() {
            errorMessage.style.opacity = '0';
            setTimeout(() => {
                errorMessage.remove();
            }, 500);
        }, 6000);
    }
    
    function highlightInput(input) {
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
    
    .preview-container {
        display: flex;
        flex-direction: column;
        align-items: center;
        margin-top: 10px;
        animation: fadeIn 0.5s ease-in-out;
    }
    
    .id-preview-img {
        max-width: 100%;
        max-height: 150px;
        border-radius: 8px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }
    
    .preview-filename {
        margin-top: 5px;
        font-size: 0.8rem;
        color: #666;
    }
    
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
</style>
`);