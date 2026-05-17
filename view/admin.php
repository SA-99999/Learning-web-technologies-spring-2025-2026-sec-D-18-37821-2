<?php
error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);
require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../model/user.php';

if (!isLoggedIn() || $_SESSION['role'] !== 'admin' || !$_SESSION['is_verified']) {
    header('Location: ' . url('login'));
    exit();
}

$userModel = new User();
$unverifiedUsers = $userModel->getAllUnverified();
$allUsers = [];

// Get all users for full management
$db = getDB();
$stmt = $db->query("
    SELECT id, name, email, role, is_verified, profile_picture, created_at
    FROM users
    ORDER BY created_at DESC
");
$allUsers = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Handle verification toggle
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userId = intval($_POST['user_id'] ?? 0);
    $action = $_POST['action'] ?? '';

    if ($action === 'toggle_verify') {
        $currentStatus = $db->prepare("SELECT is_verified FROM users WHERE id = ?");
        $currentStatus->execute([$userId]);
        $user = $currentStatus->fetch();

        if ($user) {
            $newStatus = $user['is_verified'] == 1 ? 0 : 1;
            $userModel->setVerified($userId, $newStatus);
        }
    }

    header('Location: ' . url('admin'));
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - Travel Guide</title>
    <link rel="stylesheet" href="<?php echo asset('css/style.css'); ?>">
    <script>
        window.BASE_URL = '<?php echo getBaseUrl(); ?>/';
    </script>
    <style>
        .admin-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem 1rem;
        }

        .admin-header {
            text-align: center;
            margin-bottom: 3rem;
        }

        .admin-header h1 {
            color: var(--primary-color);
            margin-bottom: 0.5rem;
        }

        .admin-tabs {
            display: flex;
            gap: 1rem;
            justify-content: center;
            margin-bottom: 2rem;
            border-bottom: 2px solid var(--border-color);
        }

        .admin-tab {
            padding: 1rem 2rem;
            background: none;
            border: none;
            cursor: pointer;
            font-size: 1rem;
            font-weight: 600;
            color: var(--text-muted);
            border-bottom: 3px solid transparent;
            margin-bottom: -2px;
            transition: all 0.2s;
        }

        .admin-tab:hover {
            color: var(--primary-color);
        }

        .admin-tab.active {
            color: var(--primary-color);
            border-bottom-color: var(--primary-color);
        }

        .tab-content {
            display: none;
        }

        .tab-content.active {
            display: block;
        }

        .admin-section {
            background: var(--surface-color);
            padding: 2rem;
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow);
        }

        .admin-section h2 {
            margin-bottom: 1.5rem;
            color: var(--primary-color);
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .stat-card {
            background: var(--bg-color);
            padding: 1.5rem;
            border-radius: var(--radius);
            text-align: center;
        }

        .stat-card h3 {
            font-size: 2.5rem;
            color: var(--primary-color);
            margin-bottom: 0.5rem;
        }

        .stat-card p {
            color: var(--text-muted);
            font-size: 0.9rem;
        }

        .admin-table-container {
            overflow-x: auto;
        }

        .admin-table {
            width: 100%;
            border-collapse: collapse;
        }

        .admin-table th,
        .admin-table td {
            padding: 1rem;
            text-align: left;
            border-bottom: 1px solid var(--border-color);
        }

        .admin-table th {
            background-color: var(--bg-color);
            font-weight: 600;
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .user-info img,
        .user-info .avatar-small {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
        }

        .avatar-small {
            background-color: var(--primary-color);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
        }

        .status-badge {
            display: inline-block;
            padding: 0.375rem 0.75rem;
            border-radius: 999px;
            font-size: 0.875rem;
            font-weight: 600;
        }

        .status-verified {
            background-color: #d1fae5;
            color: #059669;
        }

        .status-pending {
            background-color: #fef3c7;
            color: #d97706;
        }

        .role-badge {
            display: inline-block;
            padding: 0.25rem 0.75rem;
            border-radius: 999px;
            font-size: 0.8rem;
            font-weight: 600;
            background-color: var(--primary-color);
            color: white;
        }

        .btn-verify,
        .btn-unverify,
        .btn-toggle {
            padding: 0.5rem 1rem;
            border-radius: var(--radius);
            font-size: 0.875rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
            border: none;
        }

        .btn-verify {
            background-color: #10b981;
            color: white;
        }

        .btn-verify:hover {
            background-color: #059669;
        }

        .btn-unverify {
            background-color: #ef4444;
            color: white;
        }

        .btn-unverify:hover {
            background-color: #dc2626;
        }

        .btn-toggle {
            background-color: var(--primary-color);
            color: white;
        }

        .btn-toggle:hover {
            background-color: var(--primary-dark);
        }

        .admin-table form {
            margin: 0;
        }

        @media (max-width: 768px) {
            .admin-tabs {
                flex-direction: column;
            }

            .admin-tab {
                width: 100%;
                text-align: center;
            }

            .admin-table {
                font-size: 0.875rem;
            }

            .admin-table th,
            .admin-table td {
                padding: 0.75rem 0.5rem;
            }

            .user-info {
                flex-direction: column;
                gap: 0.5rem;
            }
        }
    </style>
</head>
<body>
    <?php include __DIR__ . '/components/navbar.php'; ?>

    <main class="admin-container">
        <div class="admin-header">
            <h1>🛡️ Admin Panel</h1>
            <p>Manage user accounts and verification</p>
        </div>

        <!-- Stats -->
        <div class="stats-grid">
            <div class="stat-card">
                <h3><?php echo count($allUsers); ?></h3>
                <p>Total Users</p>
            </div>
            <div class="stat-card">
                <h3><?php echo count($unverifiedUsers); ?></h3>
                <p>Pending Verification</p>
            </div>
            <?php
            $verifiedCount = 0;
            foreach ($allUsers as $u) {
                if ($u['is_verified']) $verifiedCount++;
            }
            ?>
            <div class="stat-card">
                <h3><?php echo $verifiedCount; ?></h3>
                <p>Verified Users</p>
            </div>
        </div>

        <!-- Tabs -->
        <div class="admin-tabs">
            <button class="admin-tab active" onclick="showTab('pending')">
                ⏳ Pending Users (<?php echo count($unverifiedUsers); ?>)
            </button>
            <button class="admin-tab" onclick="showTab('all')">
                👥 All Users (<?php echo count($allUsers); ?>)
            </button>
        </div>

        <!-- Pending Users Tab -->
        <div id="pending-tab" class="tab-content active">
            <div class="admin-section">
                <h2>⏳ Pending User Verifications</h2>

                <?php if (empty($unverifiedUsers)): ?>
                    <div class="alert alert-success">
                        No pending verifications. All users are verified!
                    </div>
                <?php else: ?>
                    <div class="admin-table-container">
                        <table class="admin-table">
                            <thead>
                                <tr>
                                    <th>User</th>
                                    <th>Email</th>
                                    <th>Role</th>
                                    <th>Registered</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($unverifiedUsers as $user): ?>
                                    <tr>
                                        <td>
                                            <div class="user-info">
                                                <?php if ($user['profile_picture']): ?>
                                                    <img src="<?php echo asset('public/uploads/' . $user['profile_picture']); ?>"
                                                         alt="<?php echo htmlspecialchars($user['name']); ?>">
                                                <?php else: ?>
                                                    <span class="avatar-small"><?php echo strtoupper(substr($user['name'], 0, 1)); ?></span>
                                                <?php endif; ?>
                                                <span><?php echo htmlspecialchars($user['name']); ?></span>
                                            </div>
                                        </td>
                                        <td><?php echo htmlspecialchars($user['email']); ?></td>
                                        <td>
                                            <span class="role-badge"><?php echo ucfirst(htmlspecialchars($user['role'])); ?></span>
                                        </td>
                                        <td><?php echo date('M j, Y', strtotime($user['created_at'])); ?></td>
                                        <td>
                                            <form method="POST" action="<?php echo url('admin'); ?>">
                                                <input type="hidden" name="user_id" value="<?php echo isset($user['id']) ? $user['id'] : 0; ?>">
                                                <input type="hidden" name="action" value="toggle_verify">
                                                <button type="submit" class="btn-verify">✓ Verify</button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- All Users Tab -->
        <div id="all-tab" class="tab-content">
            <div class="admin-section">
                <h2>👥 All Users</h2>

                <div class="admin-table-container">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>User</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Status</th>
                                <th>Registered</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($allUsers as $user): ?>
                                <tr>
                                    <td>
                                        <div class="user-info">
                                            <?php if ($user['profile_picture']): ?>
                                                <img src="<?php echo asset('public/uploads/' . $user['profile_picture']); ?>"
                                                     alt="<?php echo htmlspecialchars($user['name']); ?>">
                                            <?php else: ?>
                                                <span class="avatar-small"><?php echo strtoupper(substr($user['name'], 0, 1)); ?></span>
                                            <?php endif; ?>
                                            <div>
                                                <span><?php echo htmlspecialchars($user['name']); ?></span>
                                                <?php
                                                $userId = isset($user['id']) ? $user['id'] : null;
                                                if ($userId && $userId == $_SESSION['id']):
                                                ?>
                                                    <span class="status-badge status-verified">(You)</span>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </td>
                                    <td><?php echo htmlspecialchars($user['email']); ?></td>
                                    <td>
                                        <span class="role-badge"><?php echo ucfirst(htmlspecialchars($user['role'])); ?></span>
                                    </td>
                                    <td>
                                        <?php if (($user['is_verified'] ?? 0) == 1): ?>
                                            <span class="status-badge status-verified">✓ Verified</span>
                                        <?php else: ?>
                                            <span class="status-badge status-pending">⏳ Pending</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo date('M j, Y', strtotime($user['created_at'])); ?></td>
                                    <td>
                                        <?php
                                        $userId = isset($user['id']) ? $user['id'] : null;
                                        $isVerified = isset($user['is_verified']) ? $user['is_verified'] : 0;
                                        if (!$userId || $userId != $_SESSION['id']):
                                        ?>
                                            <?php if ($isVerified): ?>
                                                <form method="POST" action="<?php echo url('admin'); ?>">
                                                    <input type="hidden" name="user_id" value="<?php echo $userId; ?>">
                                                    <input type="hidden" name="action" value="toggle_verify">
                                                    <button type="submit" class="btn-unverify" onclick="return confirm('Unverify this user?')">✗ Unverify</button>
                                                </form>
                                            <?php else: ?>
                                                <form method="POST" action="<?php echo url('admin'); ?>">
                                                    <input type="hidden" name="user_id" value="<?php echo $userId; ?>">
                                                    <input type="hidden" name="action" value="toggle_verify">
                                                    <button type="submit" class="btn-verify">✓ Verify</button>
                                                </form>
                                            <?php endif; ?>
                                        <?php else: ?>
                                            <span class="status-badge status-verified">Current Admin</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>

    <script src="<?php echo asset('js/main.js'); ?>"></script>
    <script>
        function showTab(tabName) {
            // Hide all tabs
            document.querySelectorAll('.tab-content').forEach(tab => {
                tab.classList.remove('active');
            });

            // Remove active class from all buttons
            document.querySelectorAll('.admin-tab').forEach(btn => {
                btn.classList.remove('active');
            });

            // Show selected tab
            document.getElementById(tabName + '-tab').classList.add('active');

            // Add active class to clicked button
            event.target.classList.add('active');
        }
    </script>
</body>
</html>
