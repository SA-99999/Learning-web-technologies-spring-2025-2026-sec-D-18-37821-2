<?php
/**
 * Wishlist API Endpoint
 * AJAX endpoints for wishlist operations
 */

require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/../controllers/wishlist_controller.php';

header('Content-Type: application/json');

// Check if user is authenticated and verified
if (!isLoggedIn() || !isVerified()) {
    http_response_code(401);
    echo json_encode(['error' => 'Authentication required']);
    exit();
}

// Check if user has correct role
if ($_SESSION['role'] !== 'user') {
    http_response_code(403);
    echo json_encode(['error' => 'Wishlist is only available for general users']);
    exit();
}

$controller = new WishlistController();

$action = $_GET['action'] ?? $_POST['action'] ?? '';

switch ($action) {
    case 'add':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $controller->add();
        }
        break;

    case 'remove':
        if ($_SERVER['REQUEST_METHOD'] === 'POST' || $_SERVER['REQUEST_METHOD'] === 'DELETE') {
            $controller->remove();
        }
        break;

    case 'check':
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $controller->check();
        }
        break;

    default:
        http_response_code(400);
        echo json_encode(['error' => 'Invalid action']);
        break;
}
