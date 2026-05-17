<?php
/**
 * Home Controller
 * Handles home page display based on user status
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/../model/user.php';

class HomeController {
    private $userModel;

    public function __construct() {
        $this->userModel = new User();
    }

    public function index() {
        $posts = [];
        $showPendingMessage = false;

        if (isLoggedIn()) {
            if (!isVerified()) {
                $showPendingMessage = true;
            } else {
                // Get latest approved posts for verified users
                $db = getDB();
                $stmt = $db->query("
                    SELECT id, title, country, cost_level, created_at
                    FROM posts
                    WHERE is_approved = 1
                    ORDER BY created_at DESC
                    LIMIT 6
                ");
                $posts = $stmt->fetchAll();
            }
        }

        require __DIR__ . '/../view/home.php';
    }
}
