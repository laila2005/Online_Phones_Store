-- Add new admin user: ahad
-- Password: 1234 (hashed with bcrypt)

INSERT INTO admin_users (username, password, email, full_name, role) 
VALUES ('ahad', '$2y$10$YtpJu/MZmYTmKLPIvtM/0OAKj2l1xIVTw6Qe3mnYoyGz82GKjN6a.', 'ahad@phonesstore.com', 'Ahad Admin', 'admin');

-- Note: The password hash above is for '1234'
-- To verify, you can test login with:
-- Username: ahad
-- Password: 1234
