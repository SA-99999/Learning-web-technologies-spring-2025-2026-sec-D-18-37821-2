/**
 * Login Form Validation
 * Client-side validation and AJAX submission
 */

document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('loginForm');
    const emailInput = document.getElementById('email');
    const passwordInput = document.getElementById('password');

    // Use base URL from PHP
    const baseUrl = window.BASE_URL || '';

    // Real-time validation
    emailInput.addEventListener('blur', validateEmail);
    passwordInput.addEventListener('blur', validatePassword);

    form.addEventListener('submit', async function(e) {
        e.preventDefault();

        // Clear previous errors
        clearErrors();

        // Validate fields
        const errors = [];

        if (!validateEmail()) errors.push('Please enter a valid email');
        if (!validatePassword()) errors.push('Password is required');

        if (errors.length > 0) {
            showAlert('danger', errors.join('<br>'));
            return;
        }

        // Submit form via AJAX
        const formData = new FormData(form);
        const submitBtn = form.querySelector('button[type="submit"]');
        submitBtn.disabled = true;
        submitBtn.textContent = 'Logging in...';

        try {
            const response = await fetch(`${baseUrl}index.php?route=login`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: new URLSearchParams(formData)
            });

            const data = await response.json();

            if (data.success) {
                showAlert('success', data.message);
                setTimeout(() => {
                    window.location.href = data.redirect || `${baseUrl}index.php?route=home`;
                }, 500);
            } else {
                showAlert('danger', data.errors.join('<br>'));
            }
        } catch (error) {
            console.error('Login error:', error);
            showAlert('danger', 'An error occurred. Please try again.');
        } finally {
            submitBtn.disabled = false;
            submitBtn.textContent = 'Login';
        }
    });

    function validateEmail() {
        const email = emailInput.value.trim();
        const errorEl = document.getElementById('email-error');
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

        if (!email) {
            showError(emailInput, errorEl, 'Email is required');
            return false;
        }

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

        if (!password) {
            showError(passwordInput, errorEl, 'Password is required');
            return false;
        }

        clearError(passwordInput, errorEl);
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
        document.querySelectorAll('input').forEach(el => {
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
