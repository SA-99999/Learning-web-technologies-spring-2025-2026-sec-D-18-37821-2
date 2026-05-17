<?php
require_once __DIR__ . '/../config/session.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Travel Guide</title>
    <link rel="stylesheet" href="<?php echo asset('css/style.css'); ?>">
    <script>
        window.BASE_URL = '<?php echo getBaseUrl(); ?>/';
    </script>
</head>
<body>
    <?php include __DIR__ . '/components/navbar.php'; ?>

    <main class="auth-container">
        <div class="auth-card">
            <h1>Welcome Back</h1>
            <p>Login to access your account</p>

            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-danger">
                    <?php echo htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?>
                </div>
            <?php endif; ?>

            <?php if (isset($_SESSION['success'])): ?>
                <div class="alert alert-success">
                    <?php echo htmlspecialchars($_SESSION['success']); unset($_SESSION['success']); ?>
                </div>
            <?php endif; ?>

            <form id="loginForm" class="auth-form">
                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input type="email" id="email" name="email" required
                           placeholder="Enter your email">
                    <span class="error-message" id="email-error"></span>
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required
                           placeholder="Enter your password">
                    <span class="error-message" id="password-error"></span>
                </div>

                <div class="form-group checkbox-group">
                    <label>
                        <input type="checkbox" name="remember" id="remember">
                        <span>Remember me for 30 days</span>
                    </label>
                </div>

                <button type="submit" class="btn btn-primary btn-block">Login</button>

                <div id="form-alerts"></div>
            </form>

            <p class="auth-link">
                Don't have an account? <a href="<?php echo url('register'); ?>">Register here</a>
            </p>
        </div>
    </main>

    <script src="<?php echo asset('js/main.js'); ?>"></script>
    <script src="<?php echo asset('js/login.js'); ?>"></script>
</body>
</html>
