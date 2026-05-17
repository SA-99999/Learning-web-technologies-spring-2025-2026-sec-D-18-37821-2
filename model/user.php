<?php
/**
 * User Model
 * Handles all user-related database operations
 */

class User {
    private $db;

    public function __construct() {
        $this->db = getDB();
    }

    public function findByEmail($email) {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE email = :email LIMIT 1");
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetch();
    }

    public function findById($id) {
        $stmt = $this->db->prepare("SELECT id, name, email, role, is_verified, profile_picture, created_at FROM users WHERE id = :id LIMIT 1");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch();
    }

    public function create($name, $email, $passwordHash, $role, $profilePicture = null) {
        $stmt = $this->db->prepare("
            INSERT INTO users (name, email, password_hash, role, profile_picture, is_verified, created_at)
            VALUES (:name, :email, :password_hash, :role, :profile_picture, 0, NOW())
        ");
        $stmt->bindParam(':name', $name, PDO::PARAM_STR);
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->bindParam(':password_hash', $passwordHash, PDO::PARAM_STR);
        $stmt->bindParam(':role', $role, PDO::PARAM_STR);
        // Handle NULL for profile_picture correctly
        if ($profilePicture === null) {
            $stmt->bindValue(':profile_picture', null, PDO::PARAM_NULL);
        } else {
            $stmt->bindValue(':profile_picture', $profilePicture, PDO::PARAM_STR);
        }

        if ($stmt->execute()) {
            return $this->db->lastInsertId();
        }
        return false;
    }

    public function updateProfile($userId, $name, $email, $profilePicture = null) {
        if ($profilePicture) {
            $stmt = $this->db->prepare("
                UPDATE users
                SET name = :name, email = :email, profile_picture = :profile_picture
                WHERE id = :id
            ");
            $stmt->bindParam(':profile_picture', $profilePicture, PDO::PARAM_STR);
        } else {
            $stmt = $this->db->prepare("
                UPDATE users
                SET name = :name, email = :email
                WHERE id = :id
            ");
        }
        $stmt->bindParam(':name', $name, PDO::PARAM_STR);
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->bindParam(':id', $userId, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function updatePassword($userId, $passwordHash) {
        $stmt = $this->db->prepare("UPDATE users SET password_hash = :password_hash WHERE id = :id");
        $stmt->bindParam(':password_hash', $passwordHash, PDO::PARAM_STR);
        $stmt->bindParam(':id', $userId, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function setRememberToken($userId, $tokenHash) {
        $expiry = date('Y-m-d H:i:s', strtotime('+30 days'));
        $stmt = $this->db->prepare("UPDATE users SET remember_token = :token WHERE id = :id");
        $stmt->bindParam(':token', $tokenHash, PDO::PARAM_STR);
        $stmt->bindParam(':id', $userId, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function findByRememberToken($tokenHash) {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE remember_token = :token LIMIT 1");
        $stmt->bindParam(':token', $tokenHash, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetch();
    }

    public function clearRememberToken($userId) {
        $stmt = $this->db->prepare("UPDATE users SET remember_token = NULL WHERE id = :id");
        $stmt->bindParam(':id', $userId, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function getAllUnverified() {
        $stmt = $this->db->query("SELECT * FROM users WHERE is_verified = 0 ORDER BY created_at DESC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function setVerified($userId, $verified = 1) {
        $stmt = $this->db->prepare("UPDATE users SET is_verified = :verified WHERE id = :id");
        $stmt->bindParam(':verified', $verified, PDO::PARAM_INT);
        $stmt->bindParam(':id', $userId, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function verifyPassword($userId, $password) {
        $stmt = $this->db->prepare("SELECT password_hash FROM users WHERE id = :id LIMIT 1");
        $stmt->bindParam(':id', $userId, PDO::PARAM_INT);
        $stmt->execute();
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password_hash'])) {
            return true;
        }
        return false;
    }
}
