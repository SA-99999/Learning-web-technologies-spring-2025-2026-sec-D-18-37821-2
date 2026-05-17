<?php
/**
 * CSRF Protection
 * Generates and validates CSRF tokens
 */

function generateCsrfToken() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function validateCsrfToken($token) {
    if (!isset($_SESSION['csrf_token']) || $token !== $_SESSION['csrf_token']) {
        return false;
    }
    return true;
}

function requireCsrf() {
    $headers = getallheaders();
    $token = $_POST['csrf_token'] ?? $headers['X-CSRF-Token'] ?? null;

    if (!$token || !validateCsrfToken($token)) {
        http_response_code(403);
        header('Content-Type: application/json');
        echo json_encode(['error' => 'CSRF token validation failed']);
        exit();
    }
}
