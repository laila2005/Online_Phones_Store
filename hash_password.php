<?php
// Generate password hash for 'ahad' user
$password = '1234';
$hash = password_hash($password, PASSWORD_DEFAULT);

echo "Password: $password\n";
echo "Hash: $hash\n";
echo "\n";
echo "SQL to add user:\n";
echo "INSERT INTO admin_users (username, password, email, full_name, role) VALUES\n";
echo "('ahad', '$hash', 'ahad@phonesstore.com', 'Ahad Admin', 'admin');\n";
?>
