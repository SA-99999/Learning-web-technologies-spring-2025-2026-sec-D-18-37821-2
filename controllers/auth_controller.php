<?php
/**
 * Authentication Controller
 * Handles registration, login, logout, and remember me functionality
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/../config/csrf.php';
require_once __DIR__ . '/../model/user.php';

class AuthController {
    private $userModel;

    public function __construct() {
        $this->userModel = new User();
    }

    public function register() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            header('Content-Type: application/json');

            $name = trim($_POST['name'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';
            $confirmPassword = $_POST['confirm_password'] ?? '';
            $role = $_POST['role'] ?? 'user';

            // Server-side validation
            $errors = [];

            if (empty($name)) {
                $errors[] = 'Name is required';
            } elseif (strlen($name) < 2) {
                $errors[] = 'Name must be at least 2 characters';
            }

            if (empty($email)) {
                $errors[] = 'Email is required';
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors[] = 'Invalid email format';
            } elseif ($this->userModel->findByEmail($email)) {
                $errors[] = 'Email already exists';
            }

            if (empty($password)) {
                $errors[] = 'Password is required';
            } elseif (strlen($password) < 8) {
                $errors[] = 'Password must be at least 8 characters';
            } elseif (!preg_match('/[A-Z]/', $password)) {
                $errors[] = 'Password must contain at least one uppercase letter';
            } elseif (!preg_match('/[0-9]/', $password)) {
                $errors[] = 'Password must contain at least one number';
            }

            if ($password !== $confirmPassword) {
                $errors[] = 'Passwords do not match';
            }

            if (!in_array($role, ['admin', 'scout', 'user'])) {
                $errors[] = 'Invalid role selected';
            }

            if (!empty($errors)) {
                echo json_encode(['success' => false, 'errors' => $errors]);
                exit();
            }

            // Handle profile picture upload
            $profilePicture = null;
            if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] === UPLOAD_ERR_OK) {
                $allowedTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif'];
                $maxSize = 5 * 1024 * 1024; // 5MB

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

                $profilePicture = $filename;
            }

            // Create user
            $passwordHash = password_hash($password, PASSWORD_DEFAULT);
            $userId = $this->userModel->create($name, $email, $passwordHash, $role, $profilePicture);

            if ($userId) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Registration successful! Please wait for admin approval.'
                ]);
            } else {
                echo json_encode(['success' => false, 'errors' => ['Registration failed. Please try again.']]);
            }
            exit();
        }

        require __DIR__ . '/../view/register.php';
    }

    public function login() {
        // Handle remember me
        if (!isLoggedIn() && isset($_COOKIE['remember_me'])) {
            $token = hash('sha256', $_COOKIE['remember_me']);
            $user = $this->userModel->findByRememberToken($token);

            if ($user) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['name'] = $user['name'];
                $_SESSION['email'] = $user['email'];
                $_SESSION['role'] = $user['role'];
                $_SESSION['is_verified'] = $user['is_verified'];
                $_SESSION['profile_picture'] = $user['profile_picture'];

                header('Location: ' . url('home'));
                exit();
            }
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            header('Content-Type: application/json');

            $email = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';
            $remember = isset($_POST['remember']);

            $errors = [];

            if (empty($email)) {
                $errors[] = 'Email is required';
            }

            if (empty($password)) {
                $errors[] = 'Password is required';
            }

            if (!empty($errors)) {
                echo json_encode(['success' => false, 'errors' => $errors]);
                exit();
            }

            $user = $this->userModel->findByEmail($email);

            if (!$user || !password_verify($password, $user['password_hash'])) {
                echo json_encode(['success' => false, 'errors' => ['Invalid email or password']]);
                exit();
            }

            // Set session
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['name'] = $user['name'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['is_verified'] = $user['is_verified'];
            $_SESSION['profile_picture'] = $user['profile_picture'];

            // Handle remember me
            if ($remember) {
                $token = bin2hex(random_bytes(32));
                $tokenHash = hash('sha256', $token);
                $this->userModel->setRememberToken($user['id'], $tokenHash);

                setcookie('remember_me', $token, time() + (30 * 86400), '/', '', false, true);
            }

            $redirectUrl = url('home');
            if ($user['role'] === 'admin' && $user['is_verified']) {
                $redirectUrl = url('admin');
            }

            echo json_encode([
                'success' => true,
                'message' => 'Login successful',
                'redirect' => $redirectUrl
            ]);
            exit();
        }

        require __DIR__ . '/../view/login.php';
    }

    public function logout() {
        // Clear remember me token
        if (isLoggedIn() && isset($_COOKIE['remember_me'])) {
            $this->userModel->clearRememberToken($_SESSION['user_id']);
            setcookie('remember_me', '', time() - 3600, '/', '', false, true);
        }

        // Destroy session
        session_unset();
        session_destroy();

        header('Location: ' . url('login'));
        exit();
    }
}
