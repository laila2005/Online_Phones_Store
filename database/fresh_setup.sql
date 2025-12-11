-- ============================================
-- Online Phones Store - Complete Database Setup
-- ============================================
-- This file creates a fresh database from scratch
-- All team members can use this to set up their local environment

-- Drop existing database if it exists (CAUTION: This deletes all data!)
DROP DATABASE IF EXISTS ecommerce_db;

-- Create new database
CREATE DATABASE ecommerce_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE ecommerce_db;

-- ============================================
-- Table: products
-- ============================================
CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    price DECIMAL(10, 2) NOT NULL,
    image_url VARCHAR(500),
    category VARCHAR(100),
    stock_quantity INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_category (category),
    INDEX idx_price (price)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- Table: users (for customer accounts)
-- ============================================
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(100),
    phone VARCHAR(20),
    address TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- Table: admin_users
-- ============================================
CREATE TABLE admin_users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100),
    full_name VARCHAR(100),
    role ENUM('admin', 'manager', 'staff') DEFAULT 'staff',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    last_login TIMESTAMP NULL,
    INDEX idx_username (username)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- Table: orders
-- ============================================
CREATE TABLE orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    order_number VARCHAR(50) UNIQUE NOT NULL,
    total_amount DECIMAL(10, 2) NOT NULL,
    status ENUM('pending', 'processing', 'shipped', 'delivered', 'cancelled') DEFAULT 'pending',
    payment_method VARCHAR(50),
    shipping_address TEXT,
    order_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_user_id (user_id),
    INDEX idx_status (status),
    INDEX idx_order_date (order_date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- Table: order_items
-- ============================================
CREATE TABLE order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL DEFAULT 1,
    price DECIMAL(10, 2) NOT NULL,
    subtotal DECIMAL(10, 2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    INDEX idx_order_id (order_id),
    INDEX idx_product_id (product_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- Table: cart (shopping cart items)
-- ============================================
CREATE TABLE cart (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    product_id INT NOT NULL,
    quantity INT NOT NULL DEFAULT 1,
    added_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    UNIQUE KEY unique_user_product (user_id, product_id),
    INDEX idx_user_id (user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- Table: categories (optional - for better organization)
-- ============================================
CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE,
    description TEXT,
    image_url VARCHAR(500),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- SAMPLE DATA - Products
-- ============================================
INSERT INTO products (name, description, price, image_url, category, stock_quantity) VALUES
-- Apple Products
('iPhone 15 Pro Max', 'Latest flagship iPhone with A17 Pro chip, titanium design, and advanced camera system with 5x optical zoom', 1199.00, 'https://images.unsplash.com/photo-1696446702061-cbd8ab720dba?w=400', 'Apple', 50),
('iPhone 15 Pro', 'Premium iPhone with A17 Pro chip, titanium design, and pro camera system', 999.00, 'https://images.unsplash.com/photo-1695048133142-1a20484d2569?w=400', 'Apple', 75),
('iPhone 15', 'Latest iPhone with Dynamic Island, 48MP camera, and all-day battery life', 799.00, 'https://images.unsplash.com/photo-1678652197950-d4c0268e8f18?w=400', 'Apple', 100),
('iPhone 14 Pro', 'Previous generation Pro model with excellent performance and camera', 899.00, 'https://images.unsplash.com/photo-1678911820864-e2c567c655d7?w=400', 'Apple', 60),
('iPhone 14', 'Reliable iPhone with great features at a more affordable price', 699.00, 'https://images.unsplash.com/photo-1678652197950-d4c0268e8f18?w=400', 'Apple', 80),

-- Samsung Products
('Samsung Galaxy S24 Ultra', 'Ultimate flagship with S Pen, 200MP camera, AI features, and titanium frame', 1299.00, 'https://images.unsplash.com/photo-1610945415295-d9bbf067e59c?w=400', 'Samsung', 45),
('Samsung Galaxy S24+', 'Premium Galaxy with large display and advanced features', 999.00, 'https://images.unsplash.com/photo-1583573607873-4f5826e7f1d7?w=400', 'Samsung', 65),
('Samsung Galaxy S24', 'Compact flagship with powerful performance', 799.00, 'https://images.unsplash.com/photo-1610945415295-d9bbf067e59c?w=400', 'Samsung', 90),
('Samsung Galaxy A54', 'Mid-range champion with great display, camera, and battery life', 449.00, 'https://images.unsplash.com/photo-1583573607873-4f5826e7f1d7?w=400', 'Samsung', 120),
('Samsung Galaxy Z Fold 5', 'Foldable innovation with large inner display and multitasking capabilities', 1799.00, 'https://images.unsplash.com/photo-1610945415295-d9bbf067e59c?w=400', 'Samsung', 30),

-- Google Products
('Google Pixel 8 Pro', 'Google flagship with advanced AI photography, Tensor G3 chip, and pure Android', 999.00, 'https://images.unsplash.com/photo-1598327105666-5b89351aff97?w=400', 'Google', 55),
('Google Pixel 8', 'Compact Pixel with excellent camera and AI features', 699.00, 'https://images.unsplash.com/photo-1598327105666-5b89351aff97?w=400', 'Google', 70),
('Google Pixel 7a', 'Budget-friendly Pixel with flagship camera quality', 499.00, 'https://images.unsplash.com/photo-1598327105666-5b89351aff97?w=400', 'Google', 85),

-- OnePlus Products
('OnePlus 12', 'Flagship killer with Snapdragon 8 Gen 3, 100W fast charging, and Hasselblad camera', 799.00, 'https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?w=400', 'OnePlus', 60),
('OnePlus 11', 'Previous flagship with excellent value and performance', 649.00, 'https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?w=400', 'OnePlus', 75),
('OnePlus Nord 3', 'Mid-range OnePlus with flagship features at affordable price', 399.00, 'https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?w=400', 'OnePlus', 95),

-- Xiaomi Products
('Xiaomi 14 Pro', 'Flagship with Leica cameras, Snapdragon 8 Gen 3, and premium build', 999.00, 'https://images.unsplash.com/photo-1592286927505-b0c2fc1d9b65?w=400', 'Xiaomi', 50),
('Xiaomi 13T Pro', 'Performance flagship with 144Hz display and fast charging', 699.00, 'https://images.unsplash.com/photo-1592286927505-b0c2fc1d9b65?w=400', 'Xiaomi', 70),
('Xiaomi Redmi Note 13 Pro', 'Budget champion with 200MP camera and AMOLED display', 349.00, 'https://images.unsplash.com/photo-1592286927505-b0c2fc1d9b65?w=400', 'Xiaomi', 110),

-- Other Brands
('Nothing Phone 2', 'Unique design with Glyph interface, clean software, and flagship specs', 599.00, 'https://images.unsplash.com/photo-1598327105666-5b89351aff97?w=400', 'Nothing', 65),
('Motorola Edge 40 Pro', 'Flagship Motorola with clean Android and excellent display', 699.00, 'https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?w=400', 'Motorola', 55),
('Sony Xperia 1 V', 'Professional smartphone with 4K display and advanced camera controls', 1299.00, 'https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?w=400', 'Sony', 35),
('ASUS ROG Phone 7', 'Gaming powerhouse with 165Hz display and advanced cooling', 999.00, 'https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?w=400', 'ASUS', 40),
('Oppo Find X6 Pro', 'Flagship with Hasselblad camera and premium design', 899.00, 'https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?w=400', 'Oppo', 50);

-- ============================================
-- SAMPLE DATA - Categories
-- ============================================
INSERT INTO categories (name, description, image_url) VALUES
('Apple', 'Premium smartphones from Apple with iOS', 'https://images.unsplash.com/photo-1611472173362-3f53dbd65d80?w=400'),
('Samsung', 'Innovative Android smartphones from Samsung', 'https://images.unsplash.com/photo-1610945415295-d9bbf067e59c?w=400'),
('Google', 'Pure Android experience with Pixel phones', 'https://images.unsplash.com/photo-1598327105666-5b89351aff97?w=400'),
('OnePlus', 'Flagship killers with great value', 'https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?w=400'),
('Xiaomi', 'Feature-packed phones at competitive prices', 'https://images.unsplash.com/photo-1592286927505-b0c2fc1d9b65?w=400'),
('Nothing', 'Unique design and clean software', 'https://images.unsplash.com/photo-1598327105666-5b89351aff97?w=400'),
('Motorola', 'Classic brand with modern features', 'https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?w=400'),
('Sony', 'Professional-grade smartphones', 'https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?w=400'),
('ASUS', 'Gaming and performance focused', 'https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?w=400'),
('Oppo', 'Innovative camera technology', 'https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?w=400');

-- ============================================
-- SAMPLE DATA - Admin Users
-- ============================================
-- Password for all admin users: admin123 (hashed with bcrypt)
INSERT INTO admin_users (username, password, email, full_name, role) VALUES
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin@phonesstore.com', 'System Administrator', 'admin'),
('judy', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'judy@phonesstore.com', 'Judy Manager', 'manager'),
('habiba', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'habiba@phonesstore.com', 'Habiba Staff', 'staff');

-- ============================================
-- SAMPLE DATA - Customer Users
-- ============================================
-- Password for all test users: password123 (hashed with bcrypt)
INSERT INTO users (username, email, password, full_name, phone, address) VALUES
('john_doe', 'john@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'John Doe', '+1234567890', '123 Main St, New York, NY 10001'),
('jane_smith', 'jane@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Jane Smith', '+1234567891', '456 Oak Ave, Los Angeles, CA 90001'),
('mike_wilson', 'mike@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Mike Wilson', '+1234567892', '789 Pine Rd, Chicago, IL 60601');

-- ============================================
-- SAMPLE DATA - Orders
-- ============================================
INSERT INTO orders (user_id, order_number, total_amount, status, payment_method, shipping_address) VALUES
(1, 'ORD-2024-001', 1998.00, 'delivered', 'Credit Card', '123 Main St, New York, NY 10001'),
(1, 'ORD-2024-002', 799.00, 'shipped', 'PayPal', '123 Main St, New York, NY 10001'),
(2, 'ORD-2024-003', 1299.00, 'processing', 'Credit Card', '456 Oak Ave, Los Angeles, CA 90001'),
(3, 'ORD-2024-004', 1398.00, 'pending', 'Debit Card', '789 Pine Rd, Chicago, IL 60601');

-- ============================================
-- SAMPLE DATA - Order Items
-- ============================================
INSERT INTO order_items (order_id, product_id, quantity, price, subtotal) VALUES
-- Order 1 items
(1, 2, 1, 999.00, 999.00),
(1, 3, 1, 999.00, 999.00),
-- Order 2 items
(2, 3, 1, 799.00, 799.00),
-- Order 3 items
(3, 6, 1, 1299.00, 1299.00),
-- Order 4 items
(4, 9, 2, 449.00, 898.00),
(4, 13, 1, 499.00, 499.00);

-- ============================================
-- Create Views for Easy Queries
-- ============================================

-- View: Product inventory status
CREATE VIEW product_inventory AS
SELECT 
    id,
    name,
    category,
    price,
    stock_quantity,
    CASE 
        WHEN stock_quantity = 0 THEN 'Out of Stock'
        WHEN stock_quantity < 20 THEN 'Low Stock'
        ELSE 'In Stock'
    END as stock_status
FROM products
ORDER BY category, name;

-- View: Order summary with customer details
CREATE VIEW order_summary AS
SELECT 
    o.id,
    o.order_number,
    u.username,
    u.email,
    o.total_amount,
    o.status,
    o.order_date,
    COUNT(oi.id) as total_items
FROM orders o
LEFT JOIN users u ON o.user_id = u.id
LEFT JOIN order_items oi ON o.id = oi.order_id
GROUP BY o.id, o.order_number, u.username, u.email, o.total_amount, o.status, o.order_date
ORDER BY o.order_date DESC;

-- ============================================
-- END OF SCHEMA
-- ============================================

-- Display success message
SELECT 'Database created successfully!' as Status,
       (SELECT COUNT(*) FROM products) as Products,
       (SELECT COUNT(*) FROM users) as Users,
       (SELECT COUNT(*) FROM admin_users) as Admins,
       (SELECT COUNT(*) FROM orders) as Orders;
