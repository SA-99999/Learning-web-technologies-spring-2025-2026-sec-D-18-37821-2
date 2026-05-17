<?php
/**
 * Database Configuration and Connection
 * Handles PDO connection with proper error handling
 */

class Database {
    private static $instance = null;
    private $conn;

    private function __construct() {
        try {
            $dsn = 'mysql:host=127.0.0.1;dbname=travel_guide_db;charset=utf8mb4';
            $username = 'root';
            $password = '';

            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ];

            $this->conn = new PDO($dsn, $username, $password, $options);
        } catch (PDOException $e) {
            error_log("Database connection failed: " . $e->getMessage());
            die("Database connection failed. Please try again later.");
        }
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function getConnection() {
        return $this->conn;
    }
}

function getDB() {
    return Database::getInstance()->getConnection();
}
