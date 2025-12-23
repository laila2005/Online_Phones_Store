-- Save this as 09_product_images.sql
USE electronics_db;

-- Clear existing product images to avoid duplicates
DELETE FROM product_images;
ALTER TABLE product_images AUTO_INCREMENT = 1;

-- ============================================
-- PRODUCT IMAGES FOR SMARTPHONES (Products 1-6)
-- ============================================

-- Product 1: Apple iPhone 15 Pro Max 256GB
INSERT INTO product_images (product_id, image_url, alt_text, display_order, is_primary) VALUES
(1, 'https://images.unsplash.com/photo-1695048133142-1a20484d2569?w=800&auto=format&fit=crop', 'Apple iPhone 15 Pro Max - Front View', 1, 1),
(1, 'https://images.unsplash.com/photo-1695048133142-1a20484d2569?w=800&auto=format&fit=crop&q=80', 'Apple iPhone 15 Pro Max - Side View', 2, 0),
(1, 'https://images.unsplash.com/photo-1695048133142-1a20484d2569?w=800&auto=format&fit=crop&q=75', 'Apple iPhone 15 Pro Max - Camera Detail', 3, 0),
(1, 'https://images.unsplash.com/photo-1695048133142-1a20484d2569?w=800&auto=format&fit=crop&q=70', 'Apple iPhone 15 Pro Max - Display', 4, 0);

-- Product 2: Samsung Galaxy S24 Ultra 512GB
INSERT INTO product_images (product_id, image_url, alt_text, display_order, is_primary) VALUES
(2, 'https://images.unsplash.com/photo-1610945415295-d9bbf067e59c?w=800&auto=format&fit=crop', 'Samsung Galaxy S24 Ultra - Front View', 1, 1),
(2, 'https://images.unsplash.com/photo-1610945415295-d9bbf067e59c?w=800&auto=format&fit=crop&q=80', 'Samsung Galaxy S24 Ultra - S Pen', 2, 0),
(2, 'https://images.unsplash.com/photo-1610945415295-d9bbf067e59c?w=800&auto=format&fit=crop&q=75', 'Samsung Galaxy S24 Ultra - Camera Array', 3, 0),
(2, 'https://images.unsplash.com/photo-1610945415295-d9bbf067e59c?w=800&auto=format&fit=crop&q=70', 'Samsung Galaxy S24 Ultra - Display', 4, 0);

-- Product 3: Google Pixel 8 Pro 256GB
INSERT INTO product_images (product_id, image_url, alt_text, display_order, is_primary) VALUES
(3, 'https://images.unsplash.com/photo-1598327105666-5b89351aff97?w=800&auto=format&fit=crop', 'Google Pixel 8 Pro - Front View', 1, 1),
(3, 'https://images.unsplash.com/photo-1598327105666-5b89351aff97?w=800&auto=format&fit=crop&q=80', 'Google Pixel 8 Pro - Camera Bar', 2, 0),
(3, 'https://images.unsplash.com/photo-1598327105666-5b89351aff97?w=800&auto=format&fit=crop&q=75', 'Google Pixel 8 Pro - Side View', 3, 0);

-- Product 4: Samsung Galaxy Z Fold5 512GB
INSERT INTO product_images (product_id, image_url, alt_text, display_order, is_primary) VALUES
(4, 'https://images.unsplash.com/photo-1585060544812-6b45742d762f?w=800&auto=format&fit=crop', 'Samsung Galaxy Z Fold5 - Unfolded', 1, 1),
(4, 'https://images.unsplash.com/photo-1585060544812-6b45742d762f?w=800&auto=format&fit=crop&q=80', 'Samsung Galaxy Z Fold5 - Folded', 2, 0),
(4, 'https://images.unsplash.com/photo-1585060544812-6b45742d762f?w=800&auto=format&fit=crop&q=75', 'Samsung Galaxy Z Fold5 - Multitasking', 3, 0),
(4, 'https://images.unsplash.com/photo-1585060544812-6b45742d762f?w=800&auto=format&fit=crop&q=70', 'Samsung Galaxy Z Fold5 - Hinge Detail', 4, 0);

-- Product 5: Apple iPhone 15 128GB
INSERT INTO product_images (product_id, image_url, alt_text, display_order, is_primary) VALUES
(5, 'https://images.unsplash.com/photo-1678652197831-2d180705cd2c?w=800&auto=format&fit=crop', 'Apple iPhone 15 - Front View', 1, 1),
(5, 'https://images.unsplash.com/photo-1678652197831-2d180705cd2c?w=800&auto=format&fit=crop&q=80', 'Apple iPhone 15 - Dynamic Island', 2, 0),
(5, 'https://images.unsplash.com/photo-1678652197831-2d180705cd2c?w=800&auto=format&fit=crop&q=75', 'Apple iPhone 15 - Camera', 3, 0);

-- Product 6: OnePlus 12 256GB
INSERT INTO product_images (product_id, image_url, alt_text, display_order, is_primary) VALUES
(6, 'https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?w=800&auto=format&fit=crop', 'OnePlus 12 - Front View', 1, 1),
(6, 'https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?w=800&auto=format&fit=crop&q=80', 'OnePlus 12 - Hasselblad Camera', 2, 0),
(6, 'https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?w=800&auto=format&fit=crop&q=75', 'OnePlus 12 - Back View', 3, 0);

-- ============================================
-- PRODUCT IMAGES FOR HEADPHONES & SPEAKERS (Products 7-13)
-- ============================================

-- Product 7: Sony WH-1000XM5 Wireless Headphones
INSERT INTO product_images (product_id, image_url, alt_text, display_order, is_primary) VALUES
(7, 'https://images.unsplash.com/photo-1546435770-a3e426bf472b?w=800&auto=format&fit=crop', 'Sony WH-1000XM5 - Front View', 1, 1),
(7, 'https://images.unsplash.com/photo-1546435770-a3e426bf472b?w=800&auto=format&fit=crop&q=80', 'Sony WH-1000XM5 - Side Profile', 2, 0),
(7, 'https://images.unsplash.com/photo-1546435770-a3e426bf472b?w=800&auto=format&fit=crop&q=75', 'Sony WH-1000XM5 - Folded', 3, 0),
(7, 'https://images.unsplash.com/photo-1546435770-a3e426bf472b?w=800&auto=format&fit=crop&q=70', 'Sony WH-1000XM5 - Case', 4, 0);

-- Product 8: Bose QuietComfort 45 Wireless Headphones
INSERT INTO product_images (product_id, image_url, alt_text, display_order, is_primary) VALUES
(8, 'https://images.unsplash.com/photo-1484704849700-f032a568e944?w=800&auto=format&fit=crop', 'Bose QuietComfort 45 - Front View', 1, 1),
(8, 'https://images.unsplash.com/photo-1484704849700-f032a568e944?w=800&auto=format&fit=crop&q=80', 'Bose QuietComfort 45 - Side View', 2, 0),
(8, 'https://images.unsplash.com/photo-1484704849700-f032a568e944?w=800&auto=format&fit=crop&q=75', 'Bose QuietComfort 45 - Comfort Fit', 3, 0);

-- Product 9: Apple AirPods Pro (2nd Generation)
INSERT INTO product_images (product_id, image_url, alt_text, display_order, is_primary) VALUES
(9, 'https://images.unsplash.com/photo-1606841837239-c5a1a4a07af7?w=800&auto=format&fit=crop', 'AirPods Pro 2 - Case Open', 1, 1),
(9, 'https://images.unsplash.com/photo-1606841837239-c5a1a4a07af7?w=800&auto=format&fit=crop&q=80', 'AirPods Pro 2 - Earbuds', 2, 0),
(9, 'https://images.unsplash.com/photo-1606841837239-c5a1a4a07af7?w=800&auto=format&fit=crop&q=75', 'AirPods Pro 2 - MagSafe Case', 3, 0);

-- Product 10: JBL Flip 6 Portable Speaker
INSERT INTO product_images (product_id, image_url, alt_text, display_order, is_primary) VALUES
(10, 'https://images.unsplash.com/photo-1608043152269-423dbba4e7e1?w=800&auto=format&fit=crop', 'JBL Flip 6 - Front View', 1, 1),
(10, 'https://images.unsplash.com/photo-1608043152269-423dbba4e7e1?w=800&auto=format&fit=crop&q=80', 'JBL Flip 6 - Side View', 2, 0),
(10, 'https://images.unsplash.com/photo-1608043152269-423dbba4e7e1?w=800&auto=format&fit=crop&q=75', 'JBL Flip 6 - Waterproof', 3, 0);

-- Product 11: Bose Home Speaker 500
INSERT INTO product_images (product_id, image_url, alt_text, display_order, is_primary) VALUES
(11, 'https://images.unsplash.com/photo-1545454675-3531b543be5d?w=800&auto=format&fit=crop', 'Bose Home Speaker 500 - Front View', 1, 1),
(11, 'https://images.unsplash.com/photo-1545454675-3531b543be5d?w=800&auto=format&fit=crop&q=80', 'Bose Home Speaker 500 - Display', 2, 0),
(11, 'https://images.unsplash.com/photo-1545454675-3531b543be5d?w=800&auto=format&fit=crop&q=75', 'Bose Home Speaker 500 - Setup', 3, 0);

-- Product 12: Sony WF-1000XM5 Wireless Earbuds
INSERT INTO product_images (product_id, image_url, alt_text, display_order, is_primary) VALUES
(12, 'https://images.unsplash.com/photo-1590658268037-6bf12165a8df?w=800&auto=format&fit=crop', 'Sony WF-1000XM5 - Case Open', 1, 1),
(12, 'https://images.unsplash.com/photo-1590658268037-6bf12165a8df?w=800&auto=format&fit=crop&q=80', 'Sony WF-1000XM5 - Earbuds', 2, 0),
(12, 'https://images.unsplash.com/photo-1590658268037-6bf12165a8df?w=800&auto=format&fit=crop&q=75', 'Sony WF-1000XM5 - Charging Case', 3, 0);

-- Product 13: JBL Charge 5 Portable Speaker
INSERT INTO product_images (product_id, image_url, alt_text, display_order, is_primary) VALUES
(13, 'https://images.unsplash.com/photo-1608043152269-423dbba4e7e1?w=800&auto=format&fit=crop&sat=-50', 'JBL Charge 5 - Front View', 1, 1),
(13, 'https://images.unsplash.com/photo-1608043152269-423dbba4e7e1?w=800&auto=format&fit=crop&q=80&sat=-50', 'JBL Charge 5 - Side View', 2, 0),
(13, 'https://images.unsplash.com/photo-1608043152269-423dbba4e7e1?w=800&auto=format&fit=crop&q=75&sat=-50', 'JBL Charge 5 - Power Bank Feature', 3, 0);

-- ============================================
-- PRODUCT IMAGES FOR LAPTOPS & PC (Products 14-19)
-- ============================================

-- Product 14: Dell XPS 13 9340 Laptop
INSERT INTO product_images (product_id, image_url, alt_text, display_order, is_primary) VALUES
(14, 'https://images.unsplash.com/photo-1593642632823-8f785ba67e45?w=800&auto=format&fit=crop', 'Dell XPS 13 - Front View', 1, 1),
(14, 'https://images.unsplash.com/photo-1593642632823-8f785ba67e45?w=800&auto=format&fit=crop&q=80', 'Dell XPS 13 - InfinityEdge Display', 2, 0),
(14, 'https://images.unsplash.com/photo-1593642632823-8f785ba67e45?w=800&auto=format&fit=crop&q=75', 'Dell XPS 13 - Keyboard', 3, 0),
(14, 'https://images.unsplash.com/photo-1593642632823-8f785ba67e45?w=800&auto=format&fit=crop&q=70', 'Dell XPS 13 - Side Profile', 4, 0);

-- Product 15: HP Spectre x360 14 2-in-1 Laptop
INSERT INTO product_images (product_id, image_url, alt_text, display_order, is_primary) VALUES
(15, 'https://images.unsplash.com/photo-1588872657578-7efd1f1555ed?w=800&auto=format&fit=crop', 'HP Spectre x360 - Laptop Mode', 1, 1),
(15, 'https://images.unsplash.com/photo-1588872657578-7efd1f1555ed?w=800&auto=format&fit=crop&q=80', 'HP Spectre x360 - Tent Mode', 2, 0),
(15, 'https://images.unsplash.com/photo-1588872657578-7efd1f1555ed?w=800&auto=format&fit=crop&q=75', 'HP Spectre x360 - Tablet Mode', 3, 0),
(15, 'https://images.unsplash.com/photo-1588872657578-7efd1f1555ed?w=800&auto=format&fit=crop&q=70', 'HP Spectre x360 - OLED Display', 4, 0);

-- Product 16: Apple MacBook Air 15-inch M3
INSERT INTO product_images (product_id, image_url, alt_text, display_order, is_primary) VALUES
(16, 'https://images.unsplash.com/photo-1517336714731-489689fd1ca8?w=800&auto=format&fit=crop', 'MacBook Air 15 M3 - Front View', 1, 1),
(16, 'https://images.unsplash.com/photo-1517336714731-489689fd1ca8?w=800&auto=format&fit=crop&q=80', 'MacBook Air 15 M3 - Thin Profile', 2, 0),
(16, 'https://images.unsplash.com/photo-1517336714731-489689fd1ca8?w=800&auto=format&fit=crop&q=75', 'MacBook Air 15 M3 - Display', 3, 0),
(16, 'https://images.unsplash.com/photo-1517336714731-489689fd1ca8?w=800&auto=format&fit=crop&q=70', 'MacBook Air 15 M3 - Keyboard', 4, 0);

-- Product 17: ASUS ROG Zephyrus G14 Gaming Laptop
INSERT INTO product_images (product_id, image_url, alt_text, display_order, is_primary) VALUES
(17, 'https://images.unsplash.com/photo-1603302576837-37561b2e2302?w=800&auto=format&fit=crop', 'ASUS ROG Zephyrus G14 - Front View', 1, 1),
(17, 'https://images.unsplash.com/photo-1603302576837-37561b2e2302?w=800&auto=format&fit=crop&q=80', 'ASUS ROG Zephyrus G14 - AniMe Matrix', 2, 0),
(17, 'https://images.unsplash.com/photo-1603302576837-37561b2e2302?w=800&auto=format&fit=crop&q=75', 'ASUS ROG Zephyrus G14 - RGB Keyboard', 3, 0),
(17, 'https://images.unsplash.com/photo-1603302576837-37561b2e2302?w=800&auto=format&fit=crop&q=70', 'ASUS ROG Zephyrus G14 - Gaming', 4, 0);

-- Product 18: Lenovo ThinkPad X1 Carbon Gen 11
INSERT INTO product_images (product_id, image_url, alt_text, display_order, is_primary) VALUES
(18, 'https://images.unsplash.com/photo-1588872657578-7efd1f1555ed?w=800&auto=format&fit=crop&hue=180', 'ThinkPad X1 Carbon - Front View', 1, 1),
(18, 'https://images.unsplash.com/photo-1588872657578-7efd1f1555ed?w=800&auto=format&fit=crop&q=80&hue=180', 'ThinkPad X1 Carbon - Keyboard', 2, 0),
(18, 'https://images.unsplash.com/photo-1588872657578-7efd1f1555ed?w=800&auto=format&fit=crop&q=75&hue=180', 'ThinkPad X1 Carbon - OLED Display', 3, 0),
(18, 'https://images.unsplash.com/photo-1588872657578-7efd1f1555ed?w=800&auto=format&fit=crop&q=70&hue=180', 'ThinkPad X1 Carbon - Ports', 4, 0);

-- Product 19: Apple MacBook Pro 14-inch M3 Pro
INSERT INTO product_images (product_id, image_url, alt_text, display_order, is_primary) VALUES
(19, 'https://images.unsplash.com/photo-1611186871348-b1ce696e52c9?w=800&auto=format&fit=crop', 'MacBook Pro 14 M3 Pro - Front View', 1, 1),
(19, 'https://images.unsplash.com/photo-1611186871348-b1ce696e52c9?w=800&auto=format&fit=crop&q=80', 'MacBook Pro 14 M3 Pro - XDR Display', 2, 0),
(19, 'https://images.unsplash.com/photo-1611186871348-b1ce696e52c9?w=800&auto=format&fit=crop&q=75', 'MacBook Pro 14 M3 Pro - Ports', 3, 0),
(19, 'https://images.unsplash.com/photo-1611186871348-b1ce696e52c9?w=800&auto=format&fit=crop&q=70', 'MacBook Pro 14 M3 Pro - Keyboard', 4, 0);
