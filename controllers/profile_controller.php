<?php
/**
 * Profile Controller
 * Handles user profile management
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/../config/csrf.php';
require_once __DIR__ . '/../model/user.php';

class ProfileController {
    private $userModel;

    public function __construct() {
        requireAuth();
        $this->userModel = new User();
    }

    public function index() {
        $user = $this->userModel->findById($_SESSION['user_id']);
        require __DIR__ . '/../view/profile.php';
    }

    public function update() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'errors' => ['Invalid request method']]);
            exit();
        }

        header('Content-Type: application/json');

        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $currentPassword = $_POST['current_password'] ?? '';
        $newPassword = $_POST['new_password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';

        $errors = [];

        // Validate name and email
        if (empty($name)) {
            $errors[] = 'Name is required';
        } elseif (strlen($name) < 2) {
            $errors[] = 'Name must be at least 2 characters';
        }

        if (empty($email)) {
            $errors[] = 'Email is required';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Invalid email format';
        }

        // Check if email is taken by another user
        $existingUser = $this->userModel->findByEmail($email);
        if ($existingUser && $existingUser['id'] != $_SESSION['user_id']) {
            $errors[] = 'Email already exists';
        }

        // Validate password change if provided
        if (!empty($newPassword)) {
            if (empty($currentPassword)) {
                $errors[] = 'Current password is required to change password';
            } elseif (!$this->userModel->verifyPassword($_SESSION['user_id'], $currentPassword)) {
                $errors[] = 'Current password is incorrect';
            }

            if (strlen($newPassword) < 8) {
                $errors[] = 'New password must be at least 8 characters';
            } elseif (!preg_match('/[A-Z]/', $newPassword)) {
                $errors[] = 'New password must contain at least one uppercase letter';
            } elseif (!preg_match('/[0-9]/', $newPassword)) {
                $errors[] = 'New password must contain at least one number';
            }

            if ($newPassword !== $confirmPassword) {
                $errors[] = 'New passwords do not match';
            }
        }

        if (!empty($errors)) {
            echo json_encode(['success' => false, 'errors' => $errors]);
            exit();
        }

        // Handle profile picture upload
        $profilePicture = null;
        if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] === UPLOAD_ERR_OK) {
            $allowedTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif'];
            $maxSize = 5 * 1024 * 1024;

            if (!in_array($_FILES['profile_picture']['type'], $allowedTypes)) {
                echo json_encode(['success' => false, 'errors' => ['Only JPG, PNG, and GIF images are allowed']]);
                exit();
            }

            if ($_FILES['profile_picture']['size'] > $maxSize) {
                echo json_encode(['success' => false, 'errors' => ['File size must be less than 5MB']]);
                exit();
            }

            $extension = pathinfo($_FILES['profile_picture']['name'], PATHINFO_EXTENSION);
            $filename = uniqid() . '_' . time() . '.' . $extension;
            $uploadPath = __DIR__ . '/../public/uploads/' . $filename;

            if (!move_uploaded_file($_FILES['profile_picture']['tmp_name'], $uploadPath)) {
                echo json_encode(['success' => false, 'errors' => ['Failed to upload profile picture']]);
                exit();
            }

            // Delete old profile picture
            $currentUser = $this->userModel->findById($_SESSION['user_id']);
            if ($currentUser['profile_picture']) {
                $oldPath = __DIR__ . '/../public/uploads/' . $currentUser['profile_picture'];
                if (file_exists($oldPath)) {
                    unlink($oldPath);
                }
            }

            $profilePicture = $filename;
        }

        // Update profile
        $this->userModel->updateProfile($_SESSION['user_id'], $name, $email, $profilePicture);

        // Update password if provided
        if (!empty($newPassword)) {
            $passwordHash = password_hash($newPassword, PASSWORD_DEFAULT);
            $this->userModel->updatePassword($_SESSION['user_id'], $passwordHash);
        }

        // Update session
        $_SESSION['name'] = $name;
        $_SESSION['email'] = $email;
        if ($profilePicture) {
            $_SESSION['profile_picture'] = $profilePicture;
        }

        echo json_encode([
            'success' => true,
            'message' => 'Profile updated successfully'
        ]);
        exit();
    }
}
