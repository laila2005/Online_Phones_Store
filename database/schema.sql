-- ============================================
-- Online Phones Store Database Schema
-- ============================================
-- This file creates the database structure for the Online Phones Store
-- Import this file into your MySQL/MariaDB server to set up the database

-- Create database
CREATE DATABASE IF NOT EXISTS ecommerce_db;
USE ecommerce_db;

-- ============================================
-- Table: products
-- ============================================
CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    price DECIMAL(10, 2) NOT NULL,
    image_url VARCHAR(500),
    category VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- Table: admin_users
-- ============================================
CREATE TABLE IF NOT EXISTS admin_users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- Sample Data for Testing
-- ============================================

-- Insert sample products
INSERT INTO products (name, description, price, image_url, category) VALUES
('iPhone 15 Pro', 'Latest iPhone with A17 Pro chip, titanium design, and advanced camera system', 999.00, 'https://images.unsplash.com/photo-1696446702061-cbd8ab720dba?w=400', 'Apple'),
('Samsung Galaxy S24 Ultra', 'Premium Android phone with S Pen, 200MP camera, and AI features', 1199.00, 'https://images.unsplash.com/photo-1610945415295-d9bbf067e59c?w=400', 'Samsung'),
('Google Pixel 8 Pro', 'Google flagship with advanced AI photography and pure Android experience', 899.00, 'https://images.unsplash.com/photo-1598327105666-5b89351aff97?w=400', 'Google'),
('OnePlus 12', 'Flagship killer with Snapdragon 8 Gen 3 and fast charging', 799.00, 'https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?w=400', 'OnePlus'),
('iPhone 14', 'Previous generation iPhone with excellent performance and camera', 699.00, 'https://images.unsplash.com/photo-1678652197950-d4c0268e8f18?w=400', 'Apple'),
('Samsung Galaxy A54', 'Mid-range Samsung with great display and battery life', 449.00, 'https://images.unsplash.com/photo-1583573607873-4f5826e7f1d7?w=400', 'Samsung'),
('Xiaomi 13 Pro', 'Flagship phone with Leica cameras and premium build', 899.00, 'https://images.unsplash.com/photo-1592286927505-b0c2fc1d9b65?w=400', 'Xiaomi'),
('Nothing Phone 2', 'Unique design with Glyph interface and clean software', 599.00, 'https://images.unsplash.com/photo-1598327105666-5b89351aff97?w=400', 'Nothing');

-- Insert default admin user (username: admin, password: admin123)
-- NOTE: This is a hashed password using PHP password_hash()
-- You should change this after first login!
INSERT INTO admin_users (username, password, email) VALUES
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin@example.com');

-- ============================================
-- End of Schema
-- ============================================
