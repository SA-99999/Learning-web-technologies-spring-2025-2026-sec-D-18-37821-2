<?php
/**
 * Main Router
 * Routes all requests to appropriate controllers
 */

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/config/session.php';

$route = $_GET['route'] ?? 'home';
$action = $_GET['action'] ?? 'index';

// Simple routing
switch ($route) {
    case 'home':
        require_once __DIR__ . '/controllers/home_controller.php';
        $controller = new HomeController();
        $controller->index();
        break;

    case 'register':
        require_once __DIR__ . '/controllers/auth_controller.php';
        $controller = new AuthController();
        $controller->register();
        break;

    case 'login':
        require_once __DIR__ . '/controllers/auth_controller.php';
        $controller = new AuthController();
        $controller->login();
        break;

    case 'logout':
        require_once __DIR__ . '/controllers/auth_controller.php';
        $controller = new AuthController();
        $controller->logout();
        break;

    case 'profile':
        require_once __DIR__ . '/controllers/profile_controller.php';
        $controller = new ProfileController();
        if ($action === 'update') {
            $controller->update();
        } else {
            $controller->index();
        }
        break;

    case 'wishlist':
        require_once __DIR__ . '/controllers/wishlist_controller.php';
        $controller = new WishlistController();
        if ($action === 'add') {
            $controller->add();
        } elseif ($action === 'remove') {
            $controller->remove();
        } else {
            $controller->index();
        }
        break;

    case 'admin':
        if (isLoggedIn() && $_SESSION['role'] === 'admin' && $_SESSION['is_verified']) {
            require_once __DIR__ . '/view/admin.php';
        } else {
            header('Location: /index.php?route=login');
        }
        break;

    default:
        require_once __DIR__ . '/controllers/home_controller.php';
        $controller = new HomeController();
        $controller->index();
        break;
}
