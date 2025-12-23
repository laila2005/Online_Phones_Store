-- Save this as 08_reviews.sql
USE electronics_store;

-- Reviews
INSERT INTO reviews (product_id, user_id, order_id, rating, title, comment, is_verified_purchase, is_approved, helpful_count) VALUES
(1, 1, 1, 5, 'Best iPhone Ever!', 'The titanium build feels amazing and the camera is incredible. Battery life lasts all day even with heavy use.', 1, 1, 24),
(1, 2, NULL, 4, 'Great but expensive', 'Excellent phone but the price is steep. The camera quality is worth it though.', 0, 1, 12),
(7, 4, 4, 5, 'Perfect for travel', 'These headphones block out everything! The noise cancellation is magical on flights.', 1, 1, 18),
(15, 8, 8, 5, 'Amazing battery life', 'I get through a full work day without charging. The M3 chip is super fast for my development work.', 1, 1, 15),
(10, 1, 7, 4, 'Great portable speaker', 'Perfect for the beach! Waterproof and the sound quality is impressive for its size.', 1, 1, 8),
(2, 5, 5, 5, 'S Pen is a game changer', 'The built-in S Pen makes this phone so versatile for note-taking and creative work.', 1, 1, 21),
(13, 6, NULL, 4, 'Solid ultrabook', 'Great performance and build quality. The keyboard is comfortable for long typing sessions.', 0, 1, 7),
(8, 6, 6, 5, 'Most comfortable headphones', 'I can wear these all day without discomfort. The Bose signature sound is amazing.', 1, 1, 14),
(17, 3, 3, 5, 'Beast of a gaming laptop', 'Handles all my games at max settings. The RGB keyboard customization is awesome.', 1, 1, 19);

-- Coupons
INSERT INTO coupons (code, description, discount_type, discount_value, min_purchase_amount, max_discount_amount, usage_limit, valid_until) VALUES
('WELCOME10', 'Welcome discount for new customers', 'percentage', 10.00, 50.00, 100.00, 1000, '2025-12-31 23:59:59'),
('SUMMER25', 'Summer sale discount', 'percentage', 25.00, 200.00, 250.00, 500, '2025-08-31 23:59:59'),
('AUDIO20', 'Audio products discount', 'percentage', 20.00, 100.00, 150.00, 300, '2025-12-31 23:59:59'),
('FREESHIP', 'Free shipping on all orders', 'fixed_amount', 15.00, 75.00, 15.00, 1000, '2025-12-31 23:59:59'),
('APPLE50', 'Apple products discount', 'fixed_amount', 50.00, 500.00, 50.00, 200, '2025-10-31 23:59:59'),
('SAMSUNG30', 'Samsung products discount', 'percentage', 30.00, 300.00, 300.00, 150, '2025-09-30 23:59:59'),
('BLACKFRIDAY', 'Black Friday special', 'percentage', 40.00, 100.00, 500.00, 100, '2025-11-30 23:59:59'),
('NEWYEAR2025', 'New Year celebration', 'fixed_amount', 100.00, 1000.00, 100.00, 50, '2025-01-31 23:59:59');

-- Price History
INSERT INTO price_history (product_id, old_price, new_price, change_reason) VALUES
(1, 1299.99, 1199.99, 'Seasonal price drop'),
(2, 1399.99, 1299.99, 'Competitor price adjustment'),
(7, 449.99, 399.99, 'New model announcement'),
(13, 1499.99, 1299.99, 'Back to school sale'),
(15, 1599.99, 1499.99, 'Student discount promotion'),
(10, 149.99, 129.99, 'Clearance sale'),
(3, 1099.99, 999.99, 'Price match guarantee'),
(17, 2699.99, 2499.99, 'Holiday promotion');


-- Cart Items
INSERT INTO cart (user_id, product_id, variant_id, quantity) VALUES
(1, 3, NULL, 1),
(1, 9, NULL, 2),
(2, 16, NULL, 1),
(3, 4, NULL, 1),
(4, 11, NULL, 1),
(5, 13, NULL, 1),
(6, 7, NULL, 1),
(7, 2, NULL, 1),
(8, 14, NULL, 1);

