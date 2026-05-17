<?php
require_once __DIR__ . '/../config/session.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Travel Guide</title>
    <link rel="stylesheet" href="<?php echo asset('css/style.css'); ?>">
    <script>
        // Set base URL for JavaScript
        window.BASE_URL = '<?php echo getBaseUrl(); ?>/';
    </script>
</head>
<body>
    <?php include __DIR__ . '/components/navbar.php'; ?>

    <main class="auth-container">
        <div class="auth-card">
            <h1>Create Account</h1>
            <p>Join our community of travel enthusiasts</p>

            <form id="registerForm" class="auth-form" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="name">Full Name *</label>
                    <input type="text" id="name" name="name" required
                           minlength="2" placeholder="Enter your full name">
                    <span class="error-message" id="name-error"></span>
                </div>

                <div class="form-group">
                    <label for="email">Email Address *</label>
                    <input type="email" id="email" name="email" required
                           placeholder="Enter your email">
                    <span class="error-message" id="email-error"></span>
                </div>

                <div class="form-group">
                    <label for="password">Password *</label>
                    <input type="password" id="password" name="password" required
                           minlength="8" placeholder="At least 8 characters">
                    <span class="error-message" id="password-error"></span>
                    <small>Password must contain at least one uppercase letter and one number</small>
                </div>

                <div class="form-group">
                    <label for="confirm_password">Confirm Password *</label>
                    <input type="password" id="confirm_password" name="confirm_password" required
                           placeholder="Re-enter your password">
                    <span class="error-message" id="confirm_password-error"></span>
                </div>

                <div class="form-group">
                    <label for="role">Account Type *</label>
                    <select id="role" name="role" required>
                        <option value="">Select your role</option>
                        <option value="user">General User</option>
                        <option value="scout">Scout</option>
                        <option value="admin">Admin</option>
                    </select>
                    <span class="error-message" id="role-error"></span>
                </div>

                <div class="form-group">
                    <label for="profile_picture">Profile Picture</label>
                    <input type="file" id="profile_picture" name="profile_picture"
                           accept="image/jpeg,image/png,image/gif">
                    <small>Optional: JPG, PNG or GIF (max 5MB)</small>
                    <span class="error-message" id="profile_picture-error"></span>
                </div>

                <button type="submit" class="btn btn-primary btn-block">Create Account</button>

                <div id="form-alerts"></div>
            </form>

            <p class="auth-link">
                Already have an account? <a href="<?php echo url('login'); ?>">Login here</a>
            </p>
        </div>
    </main>

    <script src="<?php echo asset('js/main.js'); ?>"></script>
    <script src="<?php echo asset('js/register.js'); ?>"></script>
</body>
</html>
