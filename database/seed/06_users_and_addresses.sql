-- Save this as 06_users_addresses.sql
USE electronics_db;

-- Users
INSERT INTO users (username, email, password, full_name, phone, gender, email_verified, profile_image) VALUES
('john_doe', 'john.doe@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'John Doe', '+1-555-010-1234', 'male', 1, 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=400&auto=format&fit=crop'),
('jane_smith', 'jane.smith@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Jane Smith', '+1-555-010-5678', 'female', 1, 'https://images.unsplash.com/photo-1494790108755-2616b612b786?w=400&auto=format&fit=crop'),
('alex_wong', 'alex.wong@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Alex Wong', '+1-555-010-9012', 'male', 1, 'https://images.unsplash.com/photo-1500648767791-00dcc994a43e?w=400&auto=format&fit=crop'),
('sara_jones', 'sara.jones@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Sara Jones', '+1-555-010-3456', 'female', 1, 'https://images.unsplash.com/photo-1438761681033-6461ffad8d80?w=400&auto=format&fit=crop'),
('mike_chen', 'mike.chen@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Mike Chen', '+1-555-010-7890', 'male', 1, 'https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?w=400&auto=format&fit=crop'),
('emily_davis', 'emily.davis@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Emily Davis', '+1-555-010-2345', 'female', 1, 'https://images.unsplash.com/photo-1544005313-94ddf0286df2?w=400&auto=format&fit=crop'),
('david_wilson', 'david.wilson@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'David Wilson', '+1-555-010-6789', 'male', 1, 'https://images.unsplash.com/photo-1506794778202-cad84cf45f1d?w=400&auto=format&fit=crop'),
('lisa_miller', 'lisa.miller@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Lisa Miller', '+1-555-010-0123', 'female', 1, 'https://images.unsplash.com/photo-1517841905240-472988babdf9?w=400&auto=format&fit=crop');

-- User Addresses
INSERT INTO user_addresses (user_id, address_type, full_name, phone, address_line1, address_line2, city, state, postal_code, country, is_default) VALUES
(1, 'both', 'John Doe', '+1-555-010-1234', '123 Main Street', 'Apt 4B', 'New York', 'NY', '10001', 'United States', 1),
(1, 'shipping', 'Jane Smith', '+1-555-010-1234', '456 Park Avenue', '', 'New York', 'NY', '10022', 'United States', 0),
(2, 'both', 'Jane Smith', '+1-555-010-5678', '789 Broadway', 'Suite 200', 'Los Angeles', 'CA', '90001', 'United States', 1),
(3, 'both', 'Alex Wong', '+1-555-010-9012', '321 Market Street', '', 'San Francisco', 'CA', '94105', 'United States', 1),
(4, 'both', 'Sara Jones', '+1-555-010-3456', '654 Elm Street', 'Floor 3', 'Chicago', 'IL', '60601', 'United States', 1),
(5, 'both', 'Mike Chen', '+1-555-010-7890', '987 Oak Avenue', 'Unit 12', 'Houston', 'TX', '77002', 'United States', 1),
(6, 'both', 'Emily Davis', '+1-555-010-2345', '147 Pine Street', '', 'Miami', 'FL', '33101', 'United States', 1),
(7, 'both', 'David Wilson', '+1-555-010-6789', '258 Cedar Road', 'Apt 5C', 'Seattle', 'WA', '98101', 'United States', 1),
(8, 'both', 'Lisa Miller', '+1-555-010-0123', '369 Maple Drive', '', 'Boston', 'MA', '02101', 'United States', 1);