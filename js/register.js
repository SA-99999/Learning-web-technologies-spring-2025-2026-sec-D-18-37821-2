/**
 * Registration Form Validation
 * Client-side validation before server submission
 */

document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('registerForm');
    const nameInput = document.getElementById('name');
    const emailInput = document.getElementById('email');
    const passwordInput = document.getElementById('password');
    const confirmPasswordInput = document.getElementById('confirm_password');
    const roleInput = document.getElementById('role');
    const profilePictureInput = document.getElementById('profile_picture');

    // Use base URL from PHP
    const baseUrl = window.BASE_URL || '';

    // Real-time validation
    nameInput.addEventListener('blur', validateName);
    emailInput.addEventListener('blur', validateEmail);
    passwordInput.addEventListener('blur', validatePassword);
    confirmPasswordInput.addEventListener('blur', validateConfirmPassword);
    roleInput.addEventListener('change', validateRole);
    profilePictureInput.addEventListener('change', validateProfilePicture);

    form.addEventListener('submit', async function(e) {
        e.preventDefault();

        // Clear previous errors
        clearErrors();

        // Validate all fields
        const errors = [];

        if (!validateName()) errors.push('Name is required');
        if (!validateEmail()) errors.push('Valid email is required');
        if (!validatePassword()) errors.push('Password must be at least 8 characters with uppercase and number');
        if (!validateConfirmPassword()) errors.push('Passwords do not match');
        if (!validateRole()) errors.push('Please select a role');

        if (errors.length > 0) {
            showAlert('danger', errors.join('<br>'));
            return;
        }

        // Submit form via AJAX
        const formData = new FormData(form);
        const submitBtn = form.querySelector('button[type="submit"]');
        submitBtn.disabled = true;
        submitBtn.textContent = 'Creating account...';

        try {
            const response = await fetch(`${baseUrl}index.php?route=register`, {
                method: 'POST',
                body: formData
            });

            const data = await response.json();

            if (data.success) {
                showAlert('success', data.message);
                form.reset();
                setTimeout(() => {
                    window.location.href = `${baseUrl}index.php?route=login`;
                }, 2000);
            } else {
                showAlert('danger', data.errors.join('<br>'));
            }
        } catch (error) {
            console.error('Registration error:', error);
            showAlert('danger', 'An error occurred. Please try again.');
        } finally {
            submitBtn.disabled = false;
            submitBtn.textContent = 'Create Account';
        }
    });

    function validateName() {
        const name = nameInput.value.trim();
        const errorEl = document.getElementById('name-error');

        if (name.length < 2) {
            showError(nameInput, errorEl, 'Name must be at least 2 characters');
            return false;
        }

        clearError(nameInput, errorEl);
        return true;
    }

    function validateEmail() {
        const email = emailInput.value.trim();
        const errorEl = document.getElementById('email-error');
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

        if (!emailRegex.test(email)) {
            showError(emailInput, errorEl, 'Please enter a valid email address');
            return false;
        }

        clearError(emailInput, errorEl);
        return true;
    }

    function validatePassword() {
        const password = passwordInput.value;
        const errorEl = document.getElementById('password-error');

        if (password.length < 8) {
            showError(passwordInput, errorEl, 'Password must be at least 8 characters');
            return false;
        }

        if (!/[A-Z]/.test(password)) {
            showError(passwordInput, errorEl, 'Password must contain at least one uppercase letter');
            return false;
        }

        if (!/[0-9]/.test(password)) {
            showError(passwordInput, errorEl, 'Password must contain at least one number');
            return false;
        }

        clearError(passwordInput, errorEl);
        return true;
    }

    function validateConfirmPassword() {
        const password = passwordInput.value;
        const confirmPassword = confirmPasswordInput.value;
        const errorEl = document.getElementById('confirm_password-error');

        if (password !== confirmPassword) {
            showError(confirmPasswordInput, errorEl, 'Passwords do not match');
            return false;
        }

        clearError(confirmPasswordInput, errorEl);
        return true;
    }

    function validateRole() {
        const role = roleInput.value;
        const errorEl = document.getElementById('role-error');

        if (!role) {
            showError(roleInput, errorEl, 'Please select a role');
            return false;
        }

        clearError(roleInput, errorEl);
        return true;
    }

    function validateProfilePicture() {
        const file = profilePictureInput.files[0];
        const errorEl = document.getElementById('profile_picture-error');

        if (!file) {
            clearError(profilePictureInput, errorEl);
            return true;
        }

        const allowedTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif'];
        const maxSize = 5 * 1024 * 1024; // 5MB

        if (!allowedTypes.includes(file.type)) {
            showError(profilePictureInput, errorEl, 'Only JPG, PNG, and GIF images are allowed');
            return false;
        }

        if (file.size > maxSize) {
            showError(profilePictureInput, errorEl, 'File size must be less than 5MB');
            return false;
        }

        clearError(profilePictureInput, errorEl);
        return true;
    }

    function showError(input, errorEl, message) {
        input.classList.add('error');
        errorEl.textContent = message;
        errorEl.classList.add('show');
    }

    function clearError(input, errorEl) {
        input.classList.remove('error');
        errorEl.textContent = '';
        errorEl.classList.remove('show');
    }

    function clearErrors() {
        document.querySelectorAll('.error-message').forEach(el => {
            el.textContent = '';
            el.classList.remove('show');
        });
        document.querySelectorAll('input, select').forEach(el => {
            el.classList.remove('error');
        });
    }

    function showAlert(type, message) {
        const alertsContainer = document.getElementById('form-alerts');
        alertsContainer.innerHTML = `<div class="alert alert-${type}">${message}</div>`;
        setTimeout(() => {
            alertsContainer.innerHTML = '';
        }, 5000);
    }
});
