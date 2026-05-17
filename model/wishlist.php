<?php
/**
 * Wishlist Model
 * Handles all wishlist-related database operations
 */

class Wishlist {
    private $db;

    public function __construct() {
        $this->db = getDB();
    }

    public function add($userId, $postId) {
        $stmt = $this->db->prepare("
            INSERT IGNORE INTO wishlist (user_id, post_id, created_at)
            VALUES (:user_id, :post_id, NOW())
        ");
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        $stmt->bindParam(':post_id', $postId, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function remove($userId, $postId) {
        $stmt = $this->db->prepare("DELETE FROM wishlist WHERE user_id = :user_id AND post_id = :post_id");
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        $stmt->bindParam(':post_id', $postId, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function getUserWishlist($userId) {
        $stmt = $this->db->prepare("
            SELECT w.*, p.title, p.country, p.cost_level
            FROM wishlist w
            JOIN posts p ON w.post_id = p.id
            WHERE w.user_id = :user_id
            ORDER BY w.created_at DESC
        ");
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function isInWishlist($userId, $postId) {
        $stmt = $this->db->prepare("SELECT COUNT(*) as count FROM wishlist WHERE user_id = :user_id AND post_id = :post_id");
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        $stmt->bindParam(':post_id', $postId, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch();
        return $result['count'] > 0;
    }

    public function getWishlistCount($userId) {
        $stmt = $this->db->prepare("SELECT COUNT(*) as count FROM wishlist WHERE user_id = :user_id");
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch();
        return $result['count'];
    }
}
