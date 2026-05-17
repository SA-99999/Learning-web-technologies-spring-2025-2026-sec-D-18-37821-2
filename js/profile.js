/**
 * Profile Form Validation
 * Client-side validation and AJAX update
 */

document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('profileForm');
    const nameInput = document.getElementById('name');
    const emailInput = document.getElementById('email');
    const currentPasswordInput = document.getElementById('current_password');
    const newPasswordInput = document.getElementById('new_password');
    const confirmPasswordInput = document.getElementById('confirm_password');
    const profilePictureInput = document.getElementById('profile_picture');

    // Use base URL from PHP
    const baseUrl = window.BASE_URL || '';

    // Real-time validation
    nameInput.addEventListener('blur', validateName);
    emailInput.addEventListener('blur', validateEmail);
    profilePictureInput.addEventListener('change', validateProfilePicture);
    newPasswordInput.addEventListener('blur', () => {
        if (newPasswordInput.value) {
            validateNewPassword();
            validateConfirmPassword();
        }
    });
    confirmPasswordInput.addEventListener('blur', () => {
        if (confirmPasswordInput.value) {
            validateConfirmPassword();
        }
    });

    form.addEventListener('submit', async function(e) {
        e.preventDefault();

        // Clear previous errors
        clearErrors();

        // Validate basic fields
        const errors = [];

        if (!validateName()) errors.push('Name is required');
        if (!validateEmail()) errors.push('Valid email is required');
        if (!validateProfilePicture()) errors.push('Invalid profile picture');

        // Validate password change if attempting to change
        const newPassword = newPasswordInput.value.trim();
        const currentPassword = currentPasswordInput.value.trim();

        if (newPassword) {
            if (!currentPassword) {
                errors.push('Current password is required to change password');
                showFieldError('current_password', 'Current password is required');
            } else if (!validateNewPassword()) {
                errors.push('New password does not meet requirements');
            } else if (!validateConfirmPassword()) {
                errors.push('New passwords do not match');
            }
        }

        if (errors.length > 0) {
            showAlert('danger', errors.join('<br>'));
            return;
        }

        // Submit form via AJAX
        const formData = new FormData(form);
        const submitBtn = form.querySelector('button[type="submit"]');
        submitBtn.disabled = true;
        submitBtn.textContent = 'Saving...';

        try {
            const response = await fetch(`${baseUrl}index.php?route=profile&action=update`, {
                method: 'POST',
                body: formData
            });

            const data = await response.json();

            if (data.success) {
                showAlert('success', data.message);
                // Clear password fields
                currentPasswordInput.value = '';
                newPasswordInput.value = '';
                confirmPasswordInput.value = '';
            } else {
                showAlert('danger', data.errors.join('<br>'));
            }
        } catch (error) {
            console.error('Profile update error:', error);
            showAlert('danger', 'An error occurred. Please try again.');
        } finally {
            submitBtn.disabled = false;
            submitBtn.textContent = 'Save Changes';
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

    function validateNewPassword() {
        const password = newPasswordInput.value;
        const errorEl = document.getElementById('new_password-error');

        if (password.length < 8) {
            showError(newPasswordInput, errorEl, 'Password must be at least 8 characters');
            return false;
        }

        if (!/[A-Z]/.test(password)) {
            showError(newPasswordInput, errorEl, 'Password must contain at least one uppercase letter');
            return false;
        }

        if (!/[0-9]/.test(password)) {
            showError(newPasswordInput, errorEl, 'Password must contain at least one number');
            return false;
        }

        clearError(newPasswordInput, errorEl);
        return true;
    }

    function validateConfirmPassword() {
        const newPassword = newPasswordInput.value;
        const confirmPassword = confirmPasswordInput.value;
        const errorEl = document.getElementById('confirm_password-error');

        if (newPassword !== confirmPassword) {
            showError(confirmPasswordInput, errorEl, 'Passwords do not match');
            return false;
        }

        clearError(confirmPasswordInput, errorEl);
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

    function showFieldError(fieldName, message) {
        const input = document.getElementById(fieldName);
        const errorEl = document.getElementById(fieldName + '-error');
        if (input && errorEl) {
            showError(input, errorEl, message);
        }
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
        const alertsContainer = document.getElementById('profile-alerts');
        alertsContainer.innerHTML = `<div class="alert alert-${type}">${message}</div>`;
        setTimeout(() => {
            alertsContainer.innerHTML = '';
        }, 5000);
    }
});
