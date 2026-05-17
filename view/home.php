<?php
require_once __DIR__ . '/../config/session.php';

// If logged in, load posts
$posts = [];
if (isLoggedIn() && isVerified()) {
    require_once __DIR__ . '/../config/database.php';
    $db = getDB();
    $stmt = $db->query("
        SELECT id, title, country, cost_level, image, created_at
        FROM posts
        WHERE is_approved = 1
        ORDER BY created_at DESC
        LIMIT 6
    ");
    $posts = $stmt->fetchAll();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Travel Guide - Home</title>
    <link rel="stylesheet" href="<?php echo asset('css/style.css'); ?>">
</head>
<body>
    <?php include __DIR__ . '/components/navbar.php'; ?>

    <main class="home-container">
        <?php if (!isLoggedIn()): ?>
            <!-- Non-registered users -->
            <section class="hero">
                <h1>🌍 Travel Guide</h1>
                <p>Discover amazing destinations and plan your next adventure</p>
                <div class="cta-buttons">
                    <a href="<?php echo url('register'); ?>" class="btn btn-primary">Register Now</a>
                    <a href="<?php echo url('login'); ?>" class="btn btn-secondary">Login</a>
                </div>
            </section>

            <section class="features">
                <div class="feature-card">
                    <h3>🌍 Explore</h3>
                    <p>Browse through curated travel guides from around the world</p>
                </div>
                <div class="feature-card">
                    <h3>📝 Create</h3>
                    <p>Share your travel experiences with our community</p>
                </div>
                <div class="feature-card">
                    <h3>❤️ Save</h3>
                    <p>Build your wishlist of dream destinations</p>
                </div>
            </section>

            <!-- Setup Instructions -->
            <section class="setup-guide">
                <h2>📋 Quick Start Guide</h2>
                <div class="guide-steps">
                    <div class="guide-step">
                        <span class="step-number">1</span>
                        <div class="step-content">
                            <h3>Setup Database</h3>
                            <p>First, run the setup to create the database:</p>
                            <a href="/setup.php" class="btn btn-small">Run Setup</a>
                        </div>
                    </div>
                    <div class="guide-step">
                        <span class="step-number">2</span>
                        <div class="step-content">
                            <h3>Login as Admin</h3>
                            <p>Default admin credentials:</p>
                            <div class="admin-creds">
                                <strong>Email:</strong> admin@travelguide.com<br>
                                <strong>Password:</strong> Admin123
                            </div>
                        </div>
                    </div>
                    <div class="guide-step">
                        <span class="step-number">3</span>
                        <div class="step-content">
                            <h3>Verify Users</h3>
                            <p>Go to <strong>Admin Panel</strong> to approve new user registrations</p>
                        </div>
                    </div>
                </div>
            </section>

        <?php elseif (!isVerified()): ?>
            <!-- Logged in but not verified -->
            <section class="pending-approval">
                <div class="alert alert-warning">
                    <h2>⏳ Account Pending Approval</h2>
                    <p>Your account is currently pending admin approval.</p>
                    <p><strong>Admin:</strong> Please login to verify this account.</p>
                    <p><strong>Users:</strong> Please wait for an admin to approve your account.</p>
                    <div class="pending-actions">
                        <a href="<?php echo url('logout'); ?>" class="btn btn-secondary">Logout</a>
                        <a href="<?php echo url('profile'); ?>" class="btn btn-primary">Edit Profile</a>
                    </div>
                </div>
            </section>

        <?php else: ?>
            <!-- Verified users -->
            <section class="dashboard-welcome">
                <h1>Welcome back, <?php echo htmlspecialchars($_SESSION['name']); ?>! 👋</h1>
                <p>
                    <?php if ($_SESSION['role'] === 'admin'): ?>
                        You are logged in as <strong>Admin</strong>. You can verify users in the Admin Panel.
                    <?php else: ?>
                        Here are the latest travel destinations. Save your favorites to your wishlist!
                    <?php endif; ?>
                </p>
            </section>

            <?php if ($_SESSION['role'] === 'admin'): ?>
                <!-- Admin quick actions -->
                <section class="admin-quick-actions">
                    <h2>🛡️ Admin Quick Actions</h2>
                    <div class="quick-actions">
                        <a href="<?php echo url('admin'); ?>" class="btn btn-primary">Verify Users</a>
                        <a href="<?php echo url('profile'); ?>" class="btn btn-secondary">My Profile</a>
                    </div>
                </section>
            <?php endif; ?>

            <?php if (!empty($posts)): ?>
                <section class="posts-section">
                    <h2>🗺️ Latest Destinations</h2>
                    <div class="posts-grid">
                        <?php foreach ($posts as $post): ?>
                            <?php
                            $imagePath = $post['image'] ?? null;
                            if ($imagePath && file_exists(__DIR__ . '/../public/uploads/' . $imagePath)) {
                                $imageSrc = asset('public/uploads/' . $imagePath);
                            } else {
                                $imageSrc = 'data:image/svg+xml,' . rawurlencode('<svg xmlns="http://www.w3.org/2000/svg" width="400" height="200" viewBox="0 0 400 200"><rect fill="#e2e8f0" width="400" height="200"/><text x="50%" y="50%" text-anchor="middle" dy=".3em" fill="#64748b" font-size="24">📍 ' . htmlspecialchars($post['country']) . '</text></svg>');
                            }
                            ?>
                            <article class="post-card">
                                <div class="post-image">
                                    <img src="<?php echo $imageSrc; ?>"
                                         alt="<?php echo htmlspecialchars($post['title']); ?>">
                                </div>
                                <div class="post-content">
                                    <span class="post-country"><?php echo htmlspecialchars($post['country']); ?></span>
                                    <h3><?php echo htmlspecialchars($post['title']); ?></h3>
                                    <span class="cost-level cost-<?php echo strtolower($post['cost_level']); ?>">
                                        💰 <?php echo htmlspecialchars($post['cost_level']); ?>
                                    </span>

                                    <?php if ($_SESSION['role'] === 'user'): ?>
                                        <button class="btn-wishlist"
                                                data-post-id="<?php echo $post['id']; ?>"
                                                data-api-add="<?php echo asset('api/wishlist.php?action=add'); ?>"
                                                data-api-remove="<?php echo asset('api/wishlist.php?action=remove'); ?>">
                                            ❤️ Add to Wishlist
                                        </button>
                                    <?php endif; ?>
                                </div>
                            </article>
                        <?php endforeach; ?>
                    </div>

                    <div class="browse-more">
                        <a href="<?php echo url('wishlist'); ?>" class="btn btn-primary">View My Wishlist</a>
                        <a href="<?php echo url('profile'); ?>" class="btn btn-secondary">My Profile</a>
                    </div>
                </section>
            <?php else: ?>
                <section class="no-posts">
                    <p>No destinations available yet. Check back soon!</p>
                </section>
            <?php endif; ?>
        <?php endif; ?>
    </main>

    <script src="<?php echo asset('js/main.js'); ?>"></script>
    <?php if (isLoggedIn() && isVerified() && $_SESSION['role'] === 'user'): ?>
        <script src="<?php echo asset('js/wishlist.js'); ?>"></script>
    <?php endif; ?>
</body>
</html>
