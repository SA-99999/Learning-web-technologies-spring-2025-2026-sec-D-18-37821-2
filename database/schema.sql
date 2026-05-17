-- Travel Guide Database Schema
-- Task 1: User Authentication, Registration, Profile, Home Page & Wishlist
-- Student: 18-37821-2

-- Users table
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    role ENUM('admin', 'scout', 'user') NOT NULL DEFAULT 'user',
    is_verified TINYINT(1) NOT NULL DEFAULT 0,
    profile_picture VARCHAR(255) DEFAULT NULL,
    remember_token VARCHAR(255) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_email (email),
    INDEX idx_role (role),
    INDEX idx_verified (is_verified)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert sample admin user FIRST (password: Admin123)
-- This user will have id=1, which posts can reference
INSERT IGNORE INTO users (name, email, password_hash, role, is_verified, profile_picture)
VALUES ('Administrator', 'admin@travelguide.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', 1, NULL);

-- Posts table (must be created AFTER users exist)
CREATE TABLE IF NOT EXISTS posts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    country VARCHAR(100) NOT NULL,
    cost_level ENUM('Low', 'Medium', 'High') NOT NULL DEFAULT 'Medium',
    image VARCHAR(255),
    created_by INT NOT NULL,
    is_approved TINYINT(1) NOT NULL DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_approved (is_approved),
    INDEX idx_country (country)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Wishlist table
CREATE TABLE IF NOT EXISTS wishlist (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    post_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (post_id) REFERENCES posts(id) ON DELETE CASCADE,
    UNIQUE KEY unique_user_post (user_id, post_id),
    INDEX idx_user (user_id),
    INDEX idx_post (post_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert sample posts for testing (admin user with id=1 created these)
INSERT INTO posts (title, description, country, cost_level, created_by, is_approved, image)
VALUES
    ('Paris Adventure', 'Experience the city of lights with its stunning architecture and delicious cuisine.', 'France', 'High', 1, 1, 'paris.jpg'),
    ('Tokyo Explorer', 'Discover the blend of traditional and modern Japan in vibrant Tokyo.', 'Japan', 'High', 1, 1, 'tokyo.jpg'),
    ('Bali Paradise', 'Relax on beautiful beaches and explore lush rice terraces in Bali.', 'Indonesia', 'Medium', 1, 1, 'bali.jpg'),
    ('Swiss Alps Getaway', 'Breathtaking mountain scenery and world-class skiing in Switzerland.', 'Switzerland', 'High', 1, 1, 'swiss.jpg'),
    ('Thai Island Hopping', 'Explore crystal clear waters and pristine beaches in Thailand.', 'Thailand', 'Low', 1, 1, 'thailand.jpg'),
    ('New Zealand Road Trip', 'Epic landscapes and adventure activities await in Middle-earth.', 'New Zealand', 'High', 1, 1, 'nz.jpg')
ON DUPLICATE KEY UPDATE title=title;
