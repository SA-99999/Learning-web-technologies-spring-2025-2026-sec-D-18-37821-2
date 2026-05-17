<?php
/**
 * Wishlist Controller
 * Handles wishlist operations for verified users
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/../config/csrf.php';
require_once __DIR__ . '/../model/wishlist.php';

class WishlistController {
    private $wishlistModel;

    public function __construct() {
        requireVerified();
        $this->wishlistModel = new Wishlist();

        // Only verified 'user' role can access wishlist
        if ($_SESSION['role'] !== 'user') {
            http_response_code(403);
            if ($this->isApiRequest()) {
                header('Content-Type: application/json');
                echo json_encode(['error' => 'Wishlist is only available for general users']);
            } else {
                die('Access denied: Wishlist is only available for general users');
            }
            exit();
        }
    }

    private function isApiRequest() {
        return strpos($_SERVER['REQUEST_URI'], '/api/') !== false;
    }

    public function index() {
        $wishlist = $this->wishlistModel->getUserWishlist($_SESSION['user_id']);
        require __DIR__ . '/../view/wishlist.php';
    }

    public function add() {
        header('Content-Type: application/json');

        $postId = intval($_POST['post_id'] ?? 0);

        if ($postId <= 0) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'Invalid post ID']);
            exit();
        }

        // Verify post exists
        $db = getDB();
        $stmt = $db->prepare("SELECT id FROM posts WHERE id = :id AND is_approved = 1");
        $stmt->bindParam(':id', $postId, PDO::PARAM_INT);
        $stmt->execute();

        if (!$stmt->fetch()) {
            http_response_code(404);
            echo json_encode(['success' => false, 'error' => 'Post not found']);
            exit();
        }

        if ($this->wishlistModel->add($_SESSION['user_id'], $postId)) {
            $count = $this->wishlistModel->getWishlistCount($_SESSION['user_id']);
            echo json_encode([
                'success' => true,
                'message' => 'Added to wishlist',
                'count' => $count
            ]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Failed to add to wishlist']);
        }
        exit();
    }

    public function remove() {
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $postId = intval($_POST['post_id'] ?? 0);
        } else {
            $postId = intval($_GET['post_id'] ?? 0);
        }

        if ($postId <= 0) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'Invalid post ID']);
            exit();
        }

        if ($this->wishlistModel->remove($_SESSION['user_id'], $postId)) {
            $count = $this->wishlistModel->getWishlistCount($_SESSION['user_id']);
            echo json_encode([
                'success' => true,
                'message' => 'Removed from wishlist',
                'count' => $count
            ]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Failed to remove from wishlist']);
        }
        exit();
    }

    public function check() {
        header('Content-Type: application/json');

        $postId = intval($_GET['post_id'] ?? 0);

        if ($postId <= 0) {
            echo json_encode(['in_wishlist' => false]);
            exit();
        }

        $inWishlist = $this->wishlistModel->isInWishlist($_SESSION['user_id'], $postId);
        echo json_encode(['in_wishlist' => $inWishlist]);
        exit();
    }
}
