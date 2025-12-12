-- Fix admin user passwords
-- The password hashes in the database are incorrect

-- Set password 'admin123' for admin, judy, habiba
UPDATE admin_users SET password = '$2y$10$oagsdiZMKpQlsgAk5vDdButvGzcsbtdvZQxeVgoUte1Hr6nnea2Cy' WHERE username = 'admin';
UPDATE admin_users SET password = '$2y$10$oagsdiZMKpQlsgAk5vDdButvGzcsbtdvZQxeVgoUte1Hr6nnea2Cy' WHERE username = 'judy';
UPDATE admin_users SET password = '$2y$10$oagsdiZMKpQlsgAk5vDdButvGzcsbtdvZQxeVgoUte1Hr6nnea2Cy' WHERE username = 'habiba';

-- Add new user 'ahad' with password '1234'
INSERT INTO admin_users (username, password, email, full_name, role) 
VALUES ('ahad', '$2y$10$JBPOKsroOAimeGSNPQ23bugGP2PFDE/Xjkbi9kl00VUqDukzNO9m2', 'ahad@phonesstore.com', 'Ahad Admin', 'admin')
ON DUPLICATE KEY UPDATE password = '$2y$10$JBPOKsroOAimeGSNPQ23bugGP2PFDE/Xjkbi9kl00VUqDukzNO9m2';

-- Verify the changes
SELECT username, email, role, 
       CASE 
           WHEN username IN ('admin', 'judy', 'habiba') THEN 'admin123'
           WHEN username = 'ahad' THEN '1234'
       END as password_text
FROM admin_users;
