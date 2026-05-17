<?php
/**
 * Session Management
 * Handles session initialization, remember me functionality
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Auto-sync session with database
// This keeps session data (like is_verified) in sync with actual database values
syncSessionWithDatabase();

function syncSessionWithDatabase() {
    // Only sync if user is logged in
    if (!isset($_SESSION['user_id'])) {
        return;
    }

    try {
        $db = getDB();
        $stmt = $db->prepare("
            SELECT id, name, email, role, is_verified, profile_picture
            FROM users
            WHERE id = ?
            LIMIT 1
        ");
        $stmt->execute([$_SESSION['user_id']]);
        $user = $stmt->fetch();

        // If user exists, update session with latest data
        if ($user) {
            $sessionChanged = false;

            // Check if verification status changed
            if (isset($_SESSION['is_verified']) && $_SESSION['is_verified'] != $user['is_verified']) {
                $_SESSION['is_verified'] = $user['is_verified'];
                $sessionChanged = true;
            }

            // Check if role changed
            if (isset($_SESSION['role']) && $_SESSION['role'] != $user['role']) {
                $_SESSION['role'] = $user['role'];
                $sessionChanged = true;
            }

            // Check if name changed
            if (isset($_SESSION['name']) && $_SESSION['name'] != $user['name']) {
                $_SESSION['name'] = $user['name'];
            }

            // Check if email changed
            if (isset($_SESSION['email']) && $_SESSION['email'] != $user['email']) {
                $_SESSION['email'] = $user['email'];
            }

            // Update profile picture if changed
            if (isset($_SESSION['profile_picture']) && $_SESSION['profile_picture'] != $user['profile_picture']) {
                $_SESSION['profile_picture'] = $user['profile_picture'];
            }

            // If session data was updated, show success message
            if ($sessionChanged) {
                $_SESSION['success'] = 'Your account status has been updated!';
            }
        }
    } catch (PDOException $e) {
        // Silently fail - don't break the site if database check fails
        error_log("Session sync error: " . $e->getMessage());
    }
}

function requireAuth() {
    if (!isset($_SESSION['user_id'])) {
        header('Location: /index.php?route=login');
        exit();
    }
}

function requireVerified() {
    requireAuth();
    if (!isset($_SESSION['is_verified']) || $_SESSION['is_verified'] != 1) {
        $_SESSION['error'] = 'Your account is pending admin approval';
        header('Location: /index.php?route=home');
        exit();
    }
}

function requireRole($allowedRoles) {
    requireAuth();
    if (!in_array($_SESSION['role'], $allowedRoles)) {
        http_response_code(403);
        die('Access denied');
    }
}

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function isVerified() {
    return isLoggedIn() && isset($_SESSION['is_verified']) && $_SESSION['is_verified'] == 1;
}

function getCurrentUserId() {
    return $_SESSION['user_id'] ?? null;
}

function getCurrentUserRole() {
    return $_SESSION['role'] ?? null;
}

// Get base URL for assets
function getBaseUrl() {
    $scriptName = $_SERVER['SCRIPT_NAME'];
    $basePath = rtrim(dirname($scriptName), '/');
    return $basePath;
}

// Generate asset URL
function asset($path) {
    return getBaseUrl() . '/' . ltrim($path, '/');
}

// Generate URL for routes
function url($route = '') {
    return getBaseUrl() . '/index.php?route=' . $route;
}
