-- Save this as 02_categories_brands.sql
USE electronics_db;

-- INSERT categories (3 categories)
INSERT INTO categories (name, slug, description, display_order, is_active, image_url) VALUES
('Smartphones', 'smartphones', 'Latest smartphones and mobile devices from top brands', 1, 1, 'https://images.unsplash.com/photo-1598327105666-5b89351aff97?w=800&auto=format&fit=crop'),
('Headphones & Speakers', 'headphones-speakers', 'Premium audio equipment including headphones, earbuds, and speakers', 2, 1, 'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?w-800&auto=format&fit=crop'),
('Laptops & PC', 'laptops-pc', 'Laptops, desktops, and computer systems for work and gaming', 3, 1, 'https://images.unsplash.com/photo-1496181133206-80ce9b88a853?w=800&auto=format&fit=crop');

-- INSERT brands
INSERT INTO brands (name, slug, description, country_origin, is_featured, logo_url, website) VALUES
('Apple', 'apple', 'Premium consumer electronics and software', 'United States', 1, 'https://upload.wikimedia.org/wikipedia/commons/thumb/f/fa/Apple_logo_black.svg/800px-Apple_logo_black.svg.png', 'https://www.apple.com'),
('Samsung', 'samsung', 'Leading electronics and appliances manufacturer', 'South Korea', 1, 'https://upload.wikimedia.org/wikipedia/commons/thumb/2/24/Samsung_Logo.svg/800px-Samsung_Logo.svg.png', 'https://www.samsung.com'),
('Sony', 'sony', 'Entertainment and electronics innovator', 'Japan', 1, 'https://upload.wikimedia.org/wikipedia/commons/thumb/c/ca/Sony_logo.svg/800px-Sony_logo.svg.png', 'https://www.sony.com'),
('Dell', 'dell', 'Computer technology and solutions', 'United States', 1, 'https://upload.wikimedia.org/wikipedia/commons/thumb/1/18/Dell_logo_2016.svg/800px-Dell_logo_2016.svg.png', 'https://www.dell.com'),
('HP', 'hp', 'Computing and printing solutions', 'United States', 1, 'https://upload.wikimedia.org/wikipedia/commons/thumb/2/29/HP_New_Logo_2D.svg/800px-HP_New_Logo_2D.svg.png', 'https://www.hp.com'),
('Lenovo', 'lenovo', 'Personal computers and technology', 'China', 1, 'https://upload.wikimedia.org/wikipedia/commons/thumb/b/b0/Lenovo_global_logo.svg/800px-Lenovo_global_logo.svg.png', 'https://www.lenovo.com'),
('Bose', 'bose', 'Premium audio equipment', 'United States', 1, 'https://upload.wikimedia.org/wikipedia/commons/thumb/2/2e/Bose_logo.svg/800px-Bose_logo.svg.png', 'https://www.bose.com'),
('JBL', 'jbl', 'Audio equipment and speakers', 'United States', 1, 'https://upload.wikimedia.org/wikipedia/commons/thumb/5/5e/JBL_logo.svg/800px-JBL_logo.svg.png', 'https://www.jbl.com'),
('Google', 'google', 'Technology and consumer electronics', 'United States', 1, 'https://upload.wikimedia.org/wikipedia/commons/thumb/2/2f/Google_2015_logo.svg/800px-Google_2015_logo.svg.png', 'https://store.google.com'),
('Microsoft', 'microsoft', 'Software and computing devices', 'United States', 1, 'https://upload.wikimedia.org/wikipedia/commons/thumb/4/44/Microsoft_logo.svg/800px-Microsoft_logo.svg.png', 'https://www.microsoft.com'),
('Asus', 'asus', 'Computer hardware and electronics', 'Taiwan', 0, 'https://upload.wikimedia.org/wikipedia/commons/thumb/2/2e/ASUS_Logo.svg/800px-ASUS_Logo.svg.png', 'https://www.asus.com'),
('OnePlus', 'oneplus', 'Premium smartphones and accessories', 'China', 0, 'https://upload.wikimedia.org/wikipedia/commons/thumb/6/6a/OnePlus_logo.svg/800px-OnePlus_logo.svg.png', 'https://www.oneplus.com'),
('Razer', 'razer', 'Gaming peripherals and laptops', 'United States', 0, 'https://upload.wikimedia.org/wikipedia/commons/thumb/5/5e/Razer_snake_logo.svg/800px-Razer_snake_logo.svg.png', 'https://www.razer.com');