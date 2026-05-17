<?php
require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../model/wishlist.php';

$wishlistModel = new Wishlist();
$wishlist = $wishlistModel->getUserWishlist($_SESSION['user_id']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Wishlist - Travel Guide</title>
    <link rel="stylesheet" href="<?php echo asset('css/style.css'); ?>">
</head>
<body>
    <?php include __DIR__ . '/components/navbar.php'; ?>

    <main class="wishlist-container">
        <div class="wishlist-header">
            <h1>❤️ My Wishlist</h1>
            <p>Your saved travel destinations</p>
        </div>

        <div id="wishlist-alerts"></div>

        <?php if (!empty($wishlist)): ?>
            <div class="wishlist-grid">
                <?php foreach ($wishlist as $item): ?>
                    <article class="wishlist-item">
                        <div class="wishlist-item-header">
                            <h3><?php echo htmlspecialchars($item['title']); ?></h3>
                            <button class="btn-remove"
                                    data-post-id="<?php echo $item['post_id']; ?>"
                                    data-api-remove="<?php echo asset('api/wishlist.php?action=remove'); ?>"
                                    data-title="<?php echo htmlspecialchars($item['title']); ?>">
                                ✕ Remove
                            </button>
                        </div>

                        <div class="wishlist-item-details">
                            <span class="detail-item">
                                <strong>Country:</strong>
                                <?php echo htmlspecialchars($item['country']); ?>
                            </span>
                            <span class="detail-item cost-level cost-<?php echo strtolower($item['cost_level']); ?>">
                                <?php echo htmlspecialchars($item['cost_level']); ?>
                            </span>
                        </div>

                        <div class="wishlist-item-footer">
                            <small>Added on <?php echo date('F j, Y', strtotime($item['created_at'])); ?></small>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>

            <div class="wishlist-actions">
                <a href="<?php echo url('home'); ?>" class="btn btn-secondary">← Back to Home</a>
                <a href="<?php echo url('browse'); ?>" class="btn btn-primary">Browse More Destinations</a>
            </div>

        <?php else: ?>
            <div class="empty-wishlist">
                <div class="empty-icon">❤️</div>
                <h2>Your wishlist is empty</h2>
                <p>Start saving your favorite travel destinations!</p>
                <a href="<?php echo url('home'); ?>" class="btn btn-primary">Explore Destinations</a>
            </div>
        <?php endif; ?>
    </main>

    <script src="<?php echo asset('js/main.js'); ?>"></script>
    <script src="<?php echo asset('js/wishlist-page.js'); ?>"></script>
</body>
</html>
