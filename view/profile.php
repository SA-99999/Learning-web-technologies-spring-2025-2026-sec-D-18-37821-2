<?php
require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../model/user.php';

$userModel = new User();
$user = $userModel->findById($_SESSION['user_id']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile - Travel Guide</title>
    <link rel="stylesheet" href="<?php echo asset('css/style.css'); ?>">
    <script>
        window.BASE_URL = '<?php echo getBaseUrl(); ?>/';
    </script>
</head>
<body>
    <?php include __DIR__ . '/components/navbar.php'; ?>

    <main class="profile-container">
        <div class="profile-header">
            <div class="profile-avatar">
                <?php if ($user['profile_picture']): ?>
                    <img src="<?php echo asset('public/uploads/' . $user['profile_picture']); ?>"
                         alt="<?php echo htmlspecialchars($user['name']); ?>">
                <?php else: ?>
                    <div class="avatar-placeholder">
                        <?php echo strtoupper(substr($user['name'], 0, 1)); ?>
                    </div>
                <?php endif; ?>
            </div>
            <div class="profile-info">
                <h1><?php echo htmlspecialchars($user['name']); ?></h1>
                <p class="role-badge"><?php echo ucfirst(htmlspecialchars($user['role'])); ?></p>
                <?php if (!$user['is_verified']): ?>
                    <span class="badge badge-warning">Pending Approval</span>
                <?php endif; ?>
            </div>
        </div>

        <div class="profile-content">
            <div class="profile-card">
                <h2>Edit Profile</h2>

                <div id="profile-alerts"></div>

                <form id="profileForm" class="profile-form" enctype="multipart/form-data">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="name">Full Name</label>
                            <input type="text" id="name" name="name"
                                   value="<?php echo htmlspecialchars($user['name']); ?>"
                                   required minlength="2">
                            <span class="error-message" id="name-error"></span>
                        </div>

                        <div class="form-group">
                            <label for="email">Email Address</label>
                            <input type="email" id="email" name="email"
                                   value="<?php echo htmlspecialchars($user['email']); ?>"
                                   required>
                            <span class="error-message" id="email-error"></span>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="profile_picture">Update Profile Picture</label>
                        <input type="file" id="profile_picture" name="profile_picture"
                               accept="image/jpeg,image/png,image/gif">
                        <small>Leave empty to keep current picture. JPG, PNG or GIF (max 5MB)</small>
                        <span class="error-message" id="profile_picture-error"></span>
                    </div>

                    <div class="form-section">
                        <h3>Change Password</h3>
                        <p class="text-muted">Leave empty if you don't want to change your password</p>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="current_password">Current Password</label>
                                <input type="password" id="current_password" name="current_password">
                                <span class="error-message" id="current_password-error"></span>
                            </div>

                            <div class="form-group">
                                <label for="new_password">New Password</label>
                                <input type="password" id="new_password" name="new_password"
                                       minlength="8">
                                <span class="error-message" id="new_password-error"></span>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="confirm_password">Confirm New Password</label>
                            <input type="password" id="confirm_password" name="confirm_password">
                            <span class="error-message" id="confirm_password-error"></span>
                        </div>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">Save Changes</button>
                        <button type="reset" class="btn btn-secondary">Reset</button>
                    </div>
                </form>
            </div>

            <div class="profile-card">
                <h2>Account Information</h2>
                <dl class="account-details">
                    <dt>Account Type:</dt>
                    <dd><?php echo ucfirst(htmlspecialchars($user['role'])); ?></dd>

                    <dt>Member Since:</dt>
                    <dd><?php echo date('F j, Y', strtotime($user['created_at'])); ?></dd>

                    <dt>Verification Status:</dt>
                    <dd>
                        <?php if ($user['is_verified']): ?>
                            <span class="status-badge status-verified">Verified</span>
                        <?php else: ?>
                            <span class="status-badge status-pending">Pending Approval</span>
                        <?php endif; ?>
                    </dd>
                </dl>
            </div>
        </div>
    </main>

    <script src="<?php echo asset('js/main.js'); ?>"></script>
    <script src="<?php echo asset('js/profile.js'); ?>"></script>
</body>
</html>
