-- ============================================
-- PROFESSIONAL ELECTRONICS STORE DATABASE
-- ============================================
-- Database: electronics_store
-- Version: 2.0
-- Description: Comprehensive e-commerce platform for electronics
-- ============================================

USE electronics_store;

-- ============================================
-- TABLE: categories
-- Description: Product category hierarchy
-- ============================================
CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE,
    slug VARCHAR(100) NOT NULL UNIQUE,
    description TEXT,
    parent_id INT DEFAULT NULL,
    image_url VARCHAR(500),
    icon VARCHAR(50),
    display_order INT DEFAULT 0,
    is_active TINYINT(1) DEFAULT 1,
    meta_title VARCHAR(255),
    meta_description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (parent_id) REFERENCES categories(id) ON DELETE SET NULL,
    INDEX idx_parent (parent_id),
    INDEX idx_active (is_active)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLE: brands
-- Description: Product manufacturers and brands
-- ============================================
CREATE TABLE brands (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE,
    slug VARCHAR(100) NOT NULL UNIQUE,
    description TEXT,
    logo_url VARCHAR(500),
    website VARCHAR(255),
    country_origin VARCHAR(100),
    is_featured TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLE: products
-- Description: Main product catalog
-- ============================================
CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    sku VARCHAR(50) NOT NULL UNIQUE,
    name VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL UNIQUE,
    brand_id INT,
    category_id INT,
    description TEXT,
    short_description VARCHAR(500),
    price DECIMAL(10,2) NOT NULL,
    compare_at_price DECIMAL(10,2) DEFAULT NULL,
    cost_price DECIMAL(10,2) DEFAULT NULL,
    stock_quantity INT DEFAULT 0,
    low_stock_threshold INT DEFAULT 10,
    weight DECIMAL(8,2) DEFAULT NULL COMMENT 'Weight in kg',
    dimensions JSON COMMENT 'length, width, height in cm',
    specs JSON COMMENT 'Technical specifications',
    warranty_period INT DEFAULT 12 COMMENT 'Warranty in months',
    is_featured TINYINT(1) DEFAULT 0,
    is_active TINYINT(1) DEFAULT 1,
    status ENUM('draft','active','discontinued','out_of_stock') DEFAULT 'active',
    view_count INT DEFAULT 0,
    meta_title VARCHAR(255),
    meta_description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL,
    FOREIGN KEY (brand_id) REFERENCES brands(id) ON DELETE SET NULL,
    INDEX idx_price (price),
    INDEX idx_category (category_id),
    INDEX idx_brand (brand_id),
    INDEX idx_status (status),
    INDEX idx_featured (is_featured),
    FULLTEXT idx_search (name, description)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLE: product_images
-- Description: Product image gallery
-- ============================================
CREATE TABLE product_images (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT NOT NULL,
    image_url VARCHAR(500) NOT NULL,
    alt_text VARCHAR(255),
    display_order INT DEFAULT 0,
    is_primary TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    INDEX idx_product (product_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLE: product_variants
-- Description: Product variations (color, size, etc.)
-- ============================================
CREATE TABLE product_variants (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT NOT NULL,
    sku VARCHAR(50) NOT NULL UNIQUE,
    variant_name VARCHAR(100),
    price DECIMAL(10,2) NOT NULL,
    stock_quantity INT DEFAULT 0,
    specs JSON,
    image_url VARCHAR(500),
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    INDEX idx_product (product_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLE: users
-- Description: Customer accounts
-- ============================================
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(100),
    phone VARCHAR(20),
    date_of_birth DATE,
    gender ENUM('male','female','other','prefer_not_to_say'),
    profile_image VARCHAR(500),
    email_verified TINYINT(1) DEFAULT 0,
    is_active TINYINT(1) DEFAULT 1,
    last_login TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLE: user_addresses
-- Description: Customer shipping/billing addresses
-- ============================================
CREATE TABLE user_addresses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    address_type ENUM('shipping','billing','both') DEFAULT 'shipping',
    full_name VARCHAR(100) NOT NULL,
    phone VARCHAR(20),
    address_line1 VARCHAR(255) NOT NULL,
    address_line2 VARCHAR(255),
    city VARCHAR(100) NOT NULL,
    state VARCHAR(100),
    postal_code VARCHAR(20),
    country VARCHAR(100) NOT NULL DEFAULT 'United States',
    is_default TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user (user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLE: admin_users
-- Description: Admin and staff accounts
-- ============================================
CREATE TABLE admin_users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    full_name VARCHAR(100),
    role ENUM('super_admin','admin','manager','staff') DEFAULT 'staff',
    permissions JSON,
    is_active TINYINT(1) DEFAULT 1,
    last_login TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLE: orders
-- Description: Customer orders
-- ============================================
CREATE TABLE orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    order_number VARCHAR(50) UNIQUE NOT NULL,
    subtotal DECIMAL(10,2) NOT NULL,
    tax_amount DECIMAL(10,2) DEFAULT 0.00,
    shipping_cost DECIMAL(10,2) DEFAULT 0.00,
    discount_amount DECIMAL(10,2) DEFAULT 0.00,
    total_amount DECIMAL(10,2) NOT NULL,
    currency VARCHAR(3) DEFAULT 'USD',
    status ENUM('pending','confirmed','processing','shipped','delivered','cancelled','refunded') DEFAULT 'pending',
    payment_status ENUM('pending','paid','failed','refunded') DEFAULT 'pending',
    payment_method VARCHAR(50),
    payment_transaction_id VARCHAR(255),
    shipping_address_id INT,
    billing_address_id INT,
    tracking_number VARCHAR(100),
    notes TEXT,
    order_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    paid_at TIMESTAMP NULL,
    shipped_at TIMESTAMP NULL,
    delivered_at TIMESTAMP NULL,
    cancelled_at TIMESTAMP NULL,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
    FOREIGN KEY (shipping_address_id) REFERENCES user_addresses(id) ON DELETE SET NULL,
    FOREIGN KEY (billing_address_id) REFERENCES user_addresses(id) ON DELETE SET NULL,
    INDEX idx_user (user_id),
    INDEX idx_status (status),
    INDEX idx_order_date (order_date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLE: order_items
-- Description: Items in each order
-- ============================================
CREATE TABLE order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    product_id INT NOT NULL,
    variant_id INT DEFAULT NULL,
    product_name VARCHAR(255) NOT NULL,
    sku VARCHAR(50),
    quantity INT NOT NULL DEFAULT 1,
    unit_price DECIMAL(10,2) NOT NULL,
    subtotal DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    FOREIGN KEY (variant_id) REFERENCES product_variants(id) ON DELETE SET NULL,
    INDEX idx_order (order_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLE: cart
-- Description: Shopping cart items
-- ============================================
CREATE TABLE cart (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    session_id VARCHAR(255),
    product_id INT NOT NULL,
    variant_id INT DEFAULT NULL,
    quantity INT NOT NULL DEFAULT 1,
    added_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    FOREIGN KEY (variant_id) REFERENCES product_variants(id) ON DELETE SET NULL,
    UNIQUE KEY unique_user_product (user_id, product_id, variant_id),
    INDEX idx_session (session_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLE: wishlists
-- Description: User saved/favorite products
-- ============================================
CREATE TABLE wishlists (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    product_id INT NOT NULL,
    added_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    UNIQUE KEY unique_wishlist (user_id, product_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLE: reviews
-- Description: Product reviews and ratings
-- ============================================
CREATE TABLE reviews (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT NOT NULL,
    user_id INT NOT NULL,
    order_id INT,
    rating TINYINT CHECK (rating BETWEEN 1 AND 5),
    title VARCHAR(255),
    comment TEXT,
    is_verified_purchase TINYINT(1) DEFAULT 0,
    is_approved TINYINT(1) DEFAULT 0,
    helpful_count INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE SET NULL,
    INDEX idx_product (product_id),
    INDEX idx_approved (is_approved)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLE: coupons
-- Description: Discount coupons and promo codes
-- ============================================
CREATE TABLE coupons (
    id INT AUTO_INCREMENT PRIMARY KEY,
    code VARCHAR(50) NOT NULL UNIQUE,
    description VARCHAR(255),
    discount_type ENUM('percentage','fixed_amount') NOT NULL,
    discount_value DECIMAL(10,2) NOT NULL,
    min_purchase_amount DECIMAL(10,2) DEFAULT 0.00,
    max_discount_amount DECIMAL(10,2),
    usage_limit INT DEFAULT NULL,
    usage_count INT DEFAULT 0,
    is_active TINYINT(1) DEFAULT 1,
    valid_from TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    valid_until TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLE: price_history
-- Description: Product price change tracking
-- ============================================
CREATE TABLE price_history (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT NOT NULL,
    old_price DECIMAL(10,2),
    new_price DECIMAL(10,2),
    changed_by INT,
    change_reason VARCHAR(255),
    changed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    FOREIGN KEY (changed_by) REFERENCES admin_users(id) ON DELETE SET NULL,
    INDEX idx_product (product_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLE: newsletters
-- Description: Newsletter subscriptions
-- ============================================
CREATE TABLE newsletters (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(100) NOT NULL UNIQUE,
    is_active TINYINT(1) DEFAULT 1,
    subscribed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    unsubscribed_at TIMESTAMP NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- SEED DATA: Brands
-- ============================================
INSERT INTO brands (name, slug, description, country_origin, is_featured) VALUES
('Apple', 'apple', 'Premium consumer electronics and software', 'United States', 1),
('Samsung', 'samsung', 'Leading electronics and appliances manufacturer', 'South Korea', 1),
('Sony', 'sony', 'Entertainment and electronics innovator', 'Japan', 1),
('LG', 'lg', 'Home appliances and electronics', 'South Korea', 1),
('Dell', 'dell', 'Computer technology and solutions', 'United States', 1),
('HP', 'hp', 'Computing and printing solutions', 'United States', 1),
('Lenovo', 'lenovo', 'Personal computers and technology', 'China', 1),
('Canon', 'canon', 'Imaging and optical products', 'Japan', 0),
('Nikon', 'nikon', 'Optical and imaging products', 'Japan', 0),
('Bose', 'bose', 'Premium audio equipment', 'United States', 1),
('JBL', 'jbl', 'Audio equipment and speakers', 'United States', 0),
('Logitech', 'logitech', 'Computer peripherals and accessories', 'Switzerland', 0),
('Microsoft', 'microsoft', 'Software and computing devices', 'United States', 1),
('Google', 'google', 'Technology and consumer electronics', 'United States', 0),
('Asus', 'asus', 'Computer hardware and electronics', 'Taiwan', 0);

-- ============================================
-- SEED DATA: Categories (Only 3 categories)
-- ============================================
INSERT INTO categories (id, name, slug, description, parent_id, display_order, is_active) VALUES
(1, 'Computers & Laptops', 'computers-laptops', 'Computers, laptops and related products', NULL, 1, 1),
(2, 'Mobile Devices', 'mobile-devices', 'Smartphones, tablets and mobile accessories', NULL, 2, 1),
(3, 'Audio & Headphones', 'audio-headphones', 'Headphones, speakers and audio equipment', NULL, 3, 1);

-- ============================================
-- SEED DATA: Products - COMPUTERS & LAPTOPS
-- ============================================
INSERT INTO products (sku, name, slug, brand_id, category_id, description, short_description, price, compare_at_price, stock_quantity, specs, warranty_period, is_featured, status) VALUES
('DELL-XPS13-9340', 'Dell XPS 13 9340', 'dell-xps-13-9340', 5, 1, 'Ultra-portable 13-inch laptop with Intel Core Ultra processors and stunning InfinityEdge display. Perfect for professionals and students who need power on the go.', 'Premium 13-inch ultrabook with Intel Core Ultra', 1299.99, 1499.99, 35, JSON_OBJECT('processor','Intel Core Ultra 7','ram','16GB LPDDR5','storage','512GB NVMe SSD','display','13.4-inch FHD+','graphics','Intel Arc','battery','Up to 12 hours','weight','1.2 kg'), 24, 1, 'active'),

('HP-SPECTRE-X360', 'HP Spectre x360 14', 'hp-spectre-x360-14', 6, 1, '2-in-1 convertible laptop with OLED display, Intel Evo platform, and premium design. Versatile device for work and entertainment.', 'Convertible 2-in-1 laptop with OLED display', 1499.99, NULL, 28, JSON_OBJECT('processor','Intel Core i7-1355U','ram','16GB DDR4','storage','1TB PCIe SSD','display','13.5-inch 3K2K OLED','graphics','Intel Iris Xe','battery','Up to 10 hours','weight','1.4 kg'), 24, 1, 'active'),

('LENOVO-THINKPAD-X1', 'Lenovo ThinkPad X1 Carbon Gen 11', 'lenovo-thinkpad-x1-carbon-gen11', 7, 1, 'Business-class laptop with legendary ThinkPad durability, security features, and exceptional keyboard. Built for enterprise users.', 'Business ultrabook with military-grade durability', 1799.99, NULL, 22, JSON_OBJECT('processor','Intel Core i7-1365U','ram','32GB LPDDR5','storage','1TB SSD','display','14-inch 2.8K OLED','graphics','Intel Iris Xe','battery','Up to 14 hours','weight','1.12 kg'), 36, 0, 'active'),

('APPLE-MBA-M3', 'Apple MacBook Air 15" M3', 'apple-macbook-air-15-m3', 1, 1, 'Incredibly thin and light laptop powered by Apple M3 chip. Features stunning Liquid Retina display and all-day battery life.', 'Thin and light 15-inch laptop with M3 chip', 1499.99, NULL, 45, JSON_OBJECT('processor','Apple M3 chip','ram','8GB unified memory','storage','512GB SSD','display','15.3-inch Liquid Retina','graphics','10-core GPU','battery','Up to 18 hours','weight','1.51 kg'), 12, 1, 'active'),

('ASUS-ROG-ZEPHYRUS', 'ASUS ROG Zephyrus G14', 'asus-rog-zephyrus-g14', 15, 1, 'Compact gaming laptop with AMD Ryzen 9 processor and NVIDIA RTX 4060. Powerful performance in an ultraportable design.', 'Compact 14-inch gaming laptop', 1699.99, 1899.99, 18, JSON_OBJECT('processor','AMD Ryzen 9 7940HS','ram','16GB DDR5','storage','1TB NVMe SSD','display','14-inch QHD+ 165Hz','graphics','NVIDIA RTX 4060','battery','Up to 10 hours','weight','1.65 kg'), 24, 1, 'active');

-- ============================================
-- SEED DATA: Products - SMARTPHONES
-- ============================================
INSERT INTO products (sku, name, slug, brand_id, category_id, description, short_description, price, stock_quantity, specs, warranty_period, is_featured, status) VALUES
('APPLE-IP15PM-256', 'Apple iPhone 15 Pro Max 256GB', 'apple-iphone-15-pro-max', 1, 2, 'Flagship iPhone with titanium design, A17 Pro chip, and advanced camera system with 5x telephoto zoom. The ultimate iPhone experience.', 'Premium iPhone with titanium design', 1199.99, 65, JSON_OBJECT('chipset','A17 Pro','display','6.7-inch Super Retina XDR','ram','8GB','storage','256GB','camera','48MP main, 12MP ultra-wide, 12MP telephoto','battery','4422mAh','os','iOS 17'), 12, 1, 'active'),

('SAMSUNG-S24U-512', 'Samsung Galaxy S24 Ultra 512GB', 'samsung-galaxy-s24-ultra', 2, 2, 'Ultimate Android flagship with 200MP camera, built-in S Pen, and AI-powered features. Premium smartphone experience.', 'Flagship Android with 200MP camera and S Pen', 1299.99, 48, JSON_OBJECT('chipset','Snapdragon 8 Gen 3','display','6.8-inch Dynamic AMOLED 2X','ram','12GB','storage','512GB','camera','200MP main, 50MP 5x telephoto, 12MP ultra-wide','battery','5000mAh','os','Android 14'), 24, 1, 'active'),

('GOOGLE-P8P-256', 'Google Pixel 8 Pro 256GB', 'google-pixel-8-pro', 14, 2, 'Google flagship with advanced AI features, exceptional camera performance, and pure Android experience. Best for photography enthusiasts.', 'AI-powered flagship with exceptional camera', 999.99, 55, JSON_OBJECT('chipset','Google Tensor G3','display','6.7-inch LTPO OLED','ram','12GB','storage','256GB','camera','50MP main, 48MP telephoto, 48MP ultra-wide','battery','5050mAh','os','Android 14'), 24, 1, 'active'),

('SAMSUNG-ZFOLD5', 'Samsung Galaxy Z Fold5', 'samsung-galaxy-z-fold5', 2, 2, 'Foldable smartphone with large 7.6-inch inner display. Multitasking powerhouse that unfolds into a tablet.', 'Foldable phone with 7.6-inch display', 1799.99, 25, JSON_OBJECT('chipset','Snapdragon 8 Gen 2','display','7.6-inch inner, 6.2-inch cover','ram','12GB','storage','512GB','camera','50MP main, 12MP ultra-wide, 10MP telephoto','battery','4400mAh','os','Android 13'), 24, 1, 'active');

-- ============================================
-- SEED DATA: Products - AUDIO & HEADPHONES
-- ============================================
INSERT INTO products (sku, name, slug, brand_id, category_id, description, short_description, price, stock_quantity, specs, warranty_period, is_featured, status) VALUES
('SONY-WH1000XM5', 'Sony WH-1000XM5', 'sony-wh-1000xm5', 3, 3, 'Industry-leading noise canceling headphones with exceptional sound quality. 30-hour battery life and premium comfort.', 'Premium noise-canceling headphones', 399.99, 75, JSON_OBJECT('driver','30mm','frequency','4-40000Hz','battery','30 hours','connectivity','Bluetooth 5.2','anc','Yes','weight','250g'), 24, 1, 'active'),

('BOSE-QC45', 'Bose QuietComfort 45', 'bose-quietcomfort-45', 10, 3, 'Legendary comfort meets advanced noise cancellation. Perfect balance of quietness and audio performance.', 'Comfortable noise-canceling headphones', 329.99, 60, JSON_OBJECT('driver','40mm','frequency','20-20000Hz','battery','24 hours','connectivity','Bluetooth 5.1','anc','Yes','weight','240g'), 24, 1, 'active'),

('APPLE-AIRPODS-PRO2', 'Apple AirPods Pro 2nd Gen', 'apple-airpods-pro-2', 1, 3, 'Next-generation AirPods Pro with H2 chip, adaptive audio, and personalized spatial audio. Seamless Apple ecosystem integration.', 'Premium wireless earbuds with ANC', 249.99, 120, JSON_OBJECT('driver','Custom','battery','6 hours (30 with case)','connectivity','Bluetooth 5.3','anc','Yes','features','Adaptive Audio, Spatial Audio','water_resistance','IPX4'), 12, 1, 'active'),

('JBL-FLIP6', 'JBL Flip 6 Portable Speaker', 'jbl-flip-6', 11, 6, 'Powerful portable Bluetooth speaker with deep bass and IP67 waterproof rating. Perfect for outdoor adventures.', 'Waterproof portable Bluetooth speaker', 129.99, 95, JSON_OBJECT('power','30W','battery','12 hours','connectivity','Bluetooth 5.1','water_resistance','IP67','weight','550g'), 12, 0, 'active'),

('BOSE-HOME500', 'Bose Home Speaker 500', 'bose-home-speaker-500', 10, 6, 'Smart speaker with powerful stereo sound and Alexa built-in. Premium audio for your home.', 'Smart home speaker with Alexa', 299.99, 40, JSON_OBJECT('power','Not specified','connectivity','WiFi, Bluetooth','voice_assistant','Alexa, Google Assistant','features','Stereo sound, Touch controls'), 24, 0, 'active');

-- ============================================
-- SEED DATA: Products - CAMERAS
-- ============================================
INSERT INTO products (sku, name, slug, brand_id, category_id, description, short_description, price, stock_quantity, specs, warranty_period, is_featured, status) VALUES
('SONY-A7IV-BODY', 'Sony Alpha 7 IV (Body)', 'sony-alpha-7-iv', 3, 4, 'Professional full-frame mirrorless camera with 33MP sensor, advanced autofocus, and 4K 60p video. Perfect for hybrid shooters.', 'Full-frame mirrorless camera', 2499.99, 22, JSON_OBJECT('sensor','33MP Full-frame CMOS','iso','100-51200','video','4K 60p','autofocus','693-point AF','screen','3-inch vari-angle','weight','658g'), 24, 1, 'active'),

('CANON-R6MKII', 'Canon EOS R6 Mark II', 'canon-eos-r6-mark-ii', 8, 4, 'High-performance full-frame mirrorless with 24MP sensor and superb autofocus. Excellent for sports and wildlife photography.', '24MP full-frame with advanced AF', 2499.99, 18, JSON_OBJECT('sensor','24MP Full-frame CMOS','iso','100-102400','video','4K 60p','autofocus','1053-point AF','screen','3.2-inch vari-angle','weight','670g'), 24, 1, 'active'),

('NIKON-Z8', 'Nikon Z8', 'nikon-z8', 9, 4, 'Professional mirrorless camera with 45.7MP stacked sensor. Exceptional image quality and speed for professionals.', '45.7MP professional mirrorless', 3999.99, 12, JSON_OBJECT('sensor','45.7MP Stacked CMOS','iso','64-25600','video','8K 30p','autofocus','493-point AF','screen','3.2-inch tilting','weight','910g'), 24, 1, 'active');

