USE electronics_db;

-- Clear existing data to avoid duplicates
SET FOREIGN_KEY_CHECKS = 0;

-- Delete existing data (order items first due to foreign key)
DELETE FROM order_items;
DELETE FROM orders;

-- Reset auto-increments
ALTER TABLE order_items AUTO_INCREMENT = 1;
ALTER TABLE orders AUTO_INCREMENT = 1;

SET FOREIGN_KEY_CHECKS = 1;

-- Admin Users (if not already inserted)
INSERT IGNORE INTO admin_users (username, password, email, full_name, role, permissions) VALUES
('super_admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin@electronics.com', 'Super Admin', 'super_admin', '["*"]'),
('store_manager', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'manager@electronics.com', 'Store Manager', 'manager', '["products.view", "products.edit", "orders.view", "orders.edit", "users.view"]'),
('sales_staff', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'sales@electronics.com', 'Sales Staff', 'staff', '["products.view", "orders.view", "orders.edit"]'),
('inventory_mgr', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'inventory@electronics.com', 'Inventory Manager', 'manager', '["products.view", "products.edit", "inventory.view", "inventory.edit"]');

-- Orders
INSERT INTO orders (user_id, order_number, subtotal, tax_amount, shipping_cost, discount_amount, total_amount, status, payment_status, payment_method, shipping_address_id, billing_address_id, tracking_number) VALUES
(1, 'ORD-2024-001', 1199.99, 96.00, 0.00, 0.00, 1295.99, 'delivered', 'paid', 'credit_card', 1, 1, '1Z999AA1234567890'),
(2, 'ORD-2024-002', 799.99, 64.00, 15.00, 50.00, 829.00, 'shipped', 'paid', 'paypal', 3, 3, '1Z999BB1234567891'),
(3, 'ORD-2024-003', 2499.99, 200.00, 0.00, 0.00, 2699.99, 'processing', 'paid', 'credit_card', 4, 4, '1Z999CC1234567892'),
(4, 'ORD-2024-004', 399.99, 32.00, 10.00, 0.00, 441.99, 'delivered', 'paid', 'apple_pay', 5, 5, '1Z999DD1234567893'),
(5, 'ORD-2024-005', 1299.99, 104.00, 0.00, 100.00, 1304.00, 'confirmed', 'paid', 'credit_card', 6, 6, '1Z999EE1234567894'),
(6, 'ORD-2024-006', 329.99, 26.40, 15.00, 0.00, 371.39, 'pending', 'pending', 'credit_card', 7, 7, NULL),
(1, 'ORD-2024-007', 129.99, 10.40, 10.00, 0.00, 150.39, 'delivered', 'paid', 'google_pay', 2, 1, '1Z999FF1234567895'),
(7, 'ORD-2024-008', 1799.99, 144.00, 25.00, 150.00, 1818.99, 'shipped', 'paid', 'credit_card', 8, 8, '1Z999GG1234567896'),
(8, 'ORD-2024-009', 299.99, 24.00, 15.00, 0.00, 338.99, 'processing', 'paid', 'paypal', 9, 9, '1Z999HH1234567897');

-- Order Items
INSERT INTO order_items (order_id, product_id, variant_id, product_name, sku, quantity, unit_price, subtotal) VALUES
(1, 1, NULL, 'Apple iPhone 15 Pro Max 256GB', 'IP15PM-256-TITANIUM', 1, 1199.99, 1199.99),
(2, 5, NULL, 'Apple iPhone 15 128GB', 'IP15-128-BLUE', 1, 799.99, 799.99),
(3, 17, NULL, 'Razer Blade 15 Gaming Laptop', 'RAZER-BLADE15-2024', 1, 2499.99, 2499.99),
(4, 7, NULL, 'Sony WH-1000XM5 Wireless Headphones', 'SONY-WH1000XM5-BLK', 1, 399.99, 399.99),
(5, 2, NULL, 'Samsung Galaxy S24 Ultra 512GB', 'S24U-512-TITANIUM', 1, 1299.99, 1299.99),
(6, 8, NULL, 'Bose QuietComfort 45 Wireless Headphones', 'BOSE-QC45-BLACK', 1, 329.99, 329.99),
(7, 10, NULL, 'JBL Flip 6 Portable Speaker', 'JBL-FLIP6-BLUE', 1, 129.99, 129.99),
(8, 15, NULL, 'Apple MacBook Air 15-inch M3', 'MBA-M3-15-512', 1, 1499.99, 1499.99),
(9, 12, NULL, 'Sony WF-1000XM5 Wireless Earbuds', 'SONY-WF1000XM5-BLK', 1, 299.99, 299.99);