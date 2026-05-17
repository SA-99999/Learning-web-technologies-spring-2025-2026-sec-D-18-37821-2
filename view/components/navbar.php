<?php
if (!function_exists('asset')) {
    require_once __DIR__ . '/../../config/session.php';
}
?>
<nav class="navbar">
    <div class="navbar-container">
        <a href="<?php echo url('home'); ?>" class="navbar-brand">
            🌍 Travel Guide
        </a>

        <button class="navbar-toggle" id="navbarToggle" aria-label="Toggle navigation">
            <span></span>
            <span></span>
            <span></span>
        </button>

        <ul class="navbar-nav" id="navbarNav">
            <?php if (!isLoggedIn()): ?>
                <!-- Non-authenticated users -->
                <li><a href="<?php echo url('home'); ?>" class="<?php echo ($_GET['route'] ?? '') === 'home' ? 'active' : ''; ?>">Home</a></li>
                <li><a href="<?php echo url('login'); ?>" class="btn-link">Login</a></li>
                <li><a href="<?php echo url('register'); ?>" class="btn btn-primary btn-small">Register</a></li>

            <?php elseif (!isVerified()): ?>
                <!-- Unverified users -->
                <li><a href="<?php echo url('home'); ?>" class="<?php echo ($_GET['route'] ?? '') === 'home' ? 'active' : ''; ?>">Home</a></li>
                <li><span class="navbar-text">⏳ Pending Approval</span></li>
                <li><a href="<?php echo url('profile'); ?>" class="nav-profile">
                    <?php if ($_SESSION['profile_picture']): ?>
                        <img src="<?php echo asset('public/uploads/' . $_SESSION['profile_picture']); ?>" alt="Profile">
                    <?php else: ?>
                        <span class="avatar"><?php echo strtoupper(substr($_SESSION['name'], 0, 1)); ?></span>
                    <?php endif; ?>
                    <?php echo htmlspecialchars($_SESSION['name']); ?>
                </a></li>
                <li><a href="<?php echo url('logout'); ?>" class="btn-link">Logout</a></li>

            <?php else: ?>
                <!-- Verified users -->
                <li><a href="<?php echo url('home'); ?>" class="<?php echo ($_GET['route'] ?? '') === 'home' ? 'active' : ''; ?>">Home</a></li>

                <?php if ($_SESSION['role'] === 'user'): ?>
                    <li><a href="<?php echo url('wishlist'); ?>" class="<?php echo ($_GET['route'] ?? '') === 'wishlist' ? 'active' : ''; ?>">
                        ❤️ Wishlist
                    </a></li>
                <?php endif; ?>

                <?php if ($_SESSION['role'] === 'admin'): ?>
                    <li><a href="<?php echo url('admin'); ?>" class="<?php echo ($_GET['route'] ?? '') === 'admin' ? 'active' : ''; ?>">Admin Panel</a></li>
                <?php endif; ?>

                <li><a href="<?php echo url('profile'); ?>" class="nav-profile <?php echo ($_GET['route'] ?? '') === 'profile' ? 'active' : ''; ?>">
                    <?php if ($_SESSION['profile_picture']): ?>
                        <img src="<?php echo asset('public/uploads/' . $_SESSION['profile_picture']); ?>" alt="Profile">
                    <?php else: ?>
                        <span class="avatar"><?php echo strtoupper(substr($_SESSION['name'], 0, 1)); ?></span>
                    <?php endif; ?>
                    <?php echo htmlspecialchars($_SESSION['name']); ?>
                </a></li>

                <li><a href="<?php echo url('logout'); ?>" class="btn-link">Logout</a></li>
            <?php endif; ?>
        </ul>
    </div>
</nav>
